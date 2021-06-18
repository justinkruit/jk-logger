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

add_action("init", "jk_debug_init_action", 2);
add_action( 'plugins_loaded', 'jk_debug_plugins_loaded' );

function jk_debug_init_action() {
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

  Tracy\Debugger::enable();

  foreach ($panelsClasses as $className) {
    $panel = new $className;
    if ($panel instanceof Tracy\IBarPanel) {
      Tracy\Debugger::getBar()->addPanel(new $className);
    }
  }
}

function jk_debug_plugins_loaded() {
  require plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';

  $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/justinkruit/jk-debug/',
    __FILE__,
    'jk-debug'
  );
}
