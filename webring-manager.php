<?php
/**
 * Webring Manager
 *
 * @package deworg/webring-manager
 * @author  Bernhard Kau
 * @license GPLv3
 *
 * @wordpress-plugin
 * Plugin Name: Webring Manager
 * Plugin URI: https://wordpress.org/plugins/webring-manager/
 * Description: Manage a webring on your WordPress site.
 * Version: 1.1.0
 * Author: Bernhard Kau
 * Author URI: https://github.com/deworg/webring-manager
 * Text Domain: webring-manager
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WEBRING_MANAGER_PATH', plugin_dir_path( __FILE__ ) );
define( 'WEBRING_MANAGER_URL', plugin_dir_url( __FILE__ ) );

// The pre_init functions check the compatibility of the plugin and calls the init function if check were successful.
webring_manager_pre_init();

/**
 * Pre init function to check the plugin's compatibility.
 */
function webring_manager_pre_init(): void {
	// Check if the min. required PHP version is available and if not, show an admin notice.
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		add_action( 'admin_notices', 'webring_manager_min_php_version_error' );

		// Stop the further processing of the plugin.
		return;
	}

	if ( file_exists( WEBRING_MANAGER_PATH . 'src/index.js' ) && ! file_exists( WEBRING_MANAGER_PATH . 'build/index.js' ) ) {
		add_action( 'admin_notices', 'webring_manager_build_files_missing' );

		// Stop the further processing of the plugin.
		return;
	}

	if ( file_exists( WEBRING_MANAGER_PATH . 'composer.json' ) && ! file_exists( WEBRING_MANAGER_PATH . 'vendor/autoload.php' ) ) {
		add_action( 'admin_notices', 'webring_manager_autoloader_missing' );

		// Stop the further processing of the plugin.
		return;
	} else {
		$autoloader = WEBRING_MANAGER_PATH . 'vendor/autoload.php';

		if ( is_readable( $autoloader ) ) {
			include $autoloader;
		}
	}

	// Add and remove rewrite rules on activation and deactivation.
	register_activation_hook( __FILE__, [ WebringManager\Rewrite\Setup::class, 'activated' ] );
	register_deactivation_hook( __FILE__, [ WebringManager\Rewrite\Setup::class, 'activated' ] );

	// If all checks were successful, load the plugin.
	require_once WEBRING_MANAGER_PATH . 'lib/load.php';
}

/**
 * Show an admin notice error message if the PHP version is too low.
 */
function webring_manager_min_php_version_error(): void {
	echo '<div class="error"><p>';
	esc_html_e( 'Webring Manager requires PHP version 7.4 or higher to function properly. Please upgrade PHP or deactivate Webring Manager.', 'webring-manager' );
	echo '</p></div>';
}

/**
 * Show an admin notice error message if the composer autoloader is missing.
 */
function webring_manager_autoloader_missing(): void {
	echo '<div class="error"><p>';
	esc_html_e( 'Webring Manager is missing the Composer autoloader file. Please run `composer install --no-dev -o` in the root folder of the plugin or use a release version including the `vendor` folder.', 'webring-manager' );
	echo '</p></div>';
}

/**
 * Show an admin notice error message if the build files are missing.
 */
function webring_manager_build_files_missing(): void {
	echo '<div class="error"><p>';
	esc_html_e( 'Webring Manager is missing the build file. Please run `npm install` and `npm run build` in the root folder of the plugin or use a release version including the `build` folder.', 'webring-manager' );
	echo '</p></div>';
}
