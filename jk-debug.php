<?php

/**
 * Plugin Name:  JK Debug
 * Plugin URI:   https://github.com/justinkruit/jk-debug/
 * Description:  Debug for WordPress
 *
 * Version:      1.0.0
 *
 * Author:       Justin Kruit
 * Author URI:   https://justinkruit.com
 */
?>

<?php

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

require_once plugin_dir_path(__FILE__) . "panels/wppanelbase.class.php";

foreach (glob(plugin_dir_path(__FILE__) . "panels/*.php") as $filename) {
  require_once $filename;
}

use Tracy\Debugger;

add_filter('wp_die_handler', function ($handler) {
  if (!is_admin() && jkdebug_should_load() && jkdebug_get_settings()['override_wpdie']) {
    return 'jkdebug_load_tracy';
  }
  return $handler;
}, 10);

add_action('init', 'jkdebug_load_tracy', 2);
add_action('plugins_loaded', 'jkdebug_plugins_loaded');

function jkdebug_load_tracy() {
  if (is_admin() || !jkdebug_should_load()) {
    return;
  }

  $panelsClasses = [
    "WpTracy\\WpPanel",
    "WpTracy\\WpUserPanel",
    "WpTracy\\WpPostPanel",
    "WpTracy\\WpQueryPanel",
    "WpTracy\\WpQueriedObjectPanel",
    "WpTracy\\WpDbPanel",
    "WpTracy\\WpRolesPanel",
    "WpTracy\\WpRewritePanel",
    "WpTracy\\WpCurrentScreenPanel",
  ]; // in the correct order

  switch (jkdebug_get_settings()["debugger_mode"]) {
    case "development":
      $debugMode = Tracy\Debugger::DEVELOPMENT;
      break;
    case "production":
      $debugMode = Tracy\Debugger::PRODUCTION;
      break;
    default:
      $debugMode = Tracy\Debugger::DETECT;
      break;
  }

  Tracy\Debugger::enable($debugMode);

  foreach ($panelsClasses as $className) {
    $panel = new $className;
    if ($panel instanceof Tracy\IBarPanel) {
      Tracy\Debugger::getBar()->addPanel(new $className);
    }
  }
}

function jkdebug_plugins_loaded() {
  require plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';

  $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/justinkruit/jk-debug/',
    __FILE__,
    'jk-debug'
  );

  $myUpdateChecker->setBranch('release');
  // $myUpdateChecker->getVcsApi()->enableReleaseAssets();
}

function jkdebug_should_load() {
  $allowed = true;
  $env = wp_get_environment_type();
  $settings = jkdebug_get_settings();

  if (!$settings['enable']) {
    return false;
  }

  if ($settings['required_environment'] !== $env) {
    return false;
  }

  if ($settings['require_signed_in']) {
    if (!is_user_logged_in()) {
      return false;
    }

    if ($settings['allowed_users']) {
      if (!in_array(get_current_user_id(), $settings['allowed_users'])) {
        return false;
      }
    }
  }

  return $allowed;
}

function jkdebug_get_settings() {
  $userSettings = array();
  $defaultSettings = [
    "enable" => true,
    "required_environment" => defined('JKDEBUG_REQUIRED_ENVIRONMENT') ? JKDEBUG_REQUIRED_ENVIRONMENT : 'development',
    "require_signed_in" => defined('JKDEBUG_REQUIRE_SIGN_IN') ? JKDEBUG_REQUIRE_SIGN_IN : false,
    "allowed_users" => defined('JKDEBUG_ALLOWED_USERS') ? JKDEBUG_ALLOWED_USERS : false,
    "debugger_mode" => defined('JKDEBUG_DEBUGGER_MODE') ? JKDEBUG_DEBUGGER_MODE : 'development',
    "override_wpdie" => defined('JKDEBUG_OVERRIDE_WPDIE') ? JKDEBUG_OVERRIDE_WPDIE : false,
  ];

  if (class_exists('ACF')) {
    $userSettings = get_field('jkdebug', 'options');
  }

  return wp_parse_args($userSettings, $defaultSettings);
}

function jkdebug_register_acf_options_pages() {
  if (function_exists('acf_add_options_page')) {
    acf_add_options_sub_page(array(
      'page_title'  => 'JK Debug',
      'menu_title'  => 'JK Debug',
      'menu_slug'   => 'jk-debug',
      'parent_slug' => 'options-general.php',
    ));
  }
}
add_action('acf/init', 'jkdebug_register_acf_options_pages');
