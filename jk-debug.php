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

add_action('plugins_loaded', 'jkdebug_plugins_loaded');


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


// function jkdebug_get_settings() {
//   $userSettings = array();
//   $defaultSettings = [
//     "enable" => true,
//     "required_environment" => defined('JKDEBUG_REQUIRED_ENVIRONMENT') ? JKDEBUG_REQUIRED_ENVIRONMENT : 'development',
//     "require_signed_in" => defined('JKDEBUG_REQUIRE_SIGN_IN') ? JKDEBUG_REQUIRE_SIGN_IN : false,
//     "allowed_users" => defined('JKDEBUG_ALLOWED_USERS') ? JKDEBUG_ALLOWED_USERS : false,
//     "debugger_mode" => defined('JKDEBUG_DEBUGGER_MODE') ? JKDEBUG_DEBUGGER_MODE : 'development',
//     "override_wpdie" => defined('JKDEBUG_OVERRIDE_WPDIE') ? JKDEBUG_OVERRIDE_WPDIE : false,
//   ];

//   if (class_exists('ACF')) {
//     $userSettings = get_field('jkdebug', 'options');
//   }

//   return wp_parse_args($userSettings, $defaultSettings);
// }

// function jkdebug_register_acf_options_pages() {
//   if (function_exists('acf_add_options_page')) {
//     acf_add_options_sub_page(array(
//       'page_title'  => 'JK Debug',
//       'menu_title'  => 'JK Debug',
//       'menu_slug'   => 'jk-debug',
//       'parent_slug' => 'options-general.php',
//     ));
//   }
// }
// add_action('acf/init', 'jkdebug_register_acf_options_pages');

// include_once 'acf-settings.php';





use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Log {

	protected static $instance;

	/**
	 * Method to return the Monolog instance
	 *
	 * @return \Monolog\Logger
	 */
	static public function getLogger()
	{
		if (! self::$instance) {
			self::configureInstance();
		}

		return self::$instance;
	}

	/**
	 * Configure Monolog to use a rotating files system.
	 *
	 * @return Logger
	 */
	protected static function configureInstance()
	{
		$dir = wp_upload_dir()['basedir'] . '/log';

		if (!file_exists($dir)){
			mkdir($dir, 0777, true);
		}

		$logger = new Logger('RdLotteryManager');
		$logger->pushHandler(new RotatingFileHandler($dir . '/main.log', 5));
		//$logger->pushHandler(new LogglyHandler('eeb5ba83-f0d6-4273-bb1d-523231583855/tag/monolog'));
		self::$instance = $logger;
	}

	public static function debug($message, array $context = []){
		self::getLogger()->debug($message, $context);
	}

	public static function info($message, array $context = []){
		self::getLogger()->info($message, $context);
	}

	public static function notice($message, array $context = []){
		self::getLogger()->notice($message, $context);
	}

	public static function warning($message, array $context = []){
		self::getLogger()->warning($message, $context);
	}

	public static function error($message, array $context = []){
		self::getLogger()->error($message, $context);
	}

	public static function critical($message, array $context = []){
		self::getLogger()->critical($message, $context);
	}

	public static function alert($message, array $context = []){
		self::getLogger()->alert($message, $context);
	}

	public static function emergency($message, array $context = []){
		self::getLogger()->emergency($message, $context);
	}

}