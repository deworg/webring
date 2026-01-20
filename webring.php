<?php
/**
 * Webring
 *
 * @package deworg/webring
 * @author  Bernhard Kau
 * @license GPLv3
 *
 * @wordpress-plugin
 * Plugin Name: Webring
 * Plugin URI: https://wordpress.org/plugins/webring/
 * Description: Manage a webring on your WordPress site.
 * Version: 1.0.0
 * Author: Bernhard Kau
 * Author URI: https://github.com/deworg/webring
 * Text Domain: webring
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Webring\Rewrite\Setup;

define( 'WEBRING_VERSION', '1.0.0' );
define( 'WEBRING_FILE', __FILE__ );
define( 'WEBRING_PATH', plugin_dir_path( WEBRING_FILE ) );
define( 'WEBRING_URL', plugin_dir_url( WEBRING_FILE ) );

// The pre_init functions check the compatibility of the plugin and calls the init function if check were successful.
webring_pre_init();

/**
 * Pre init function to check the plugin's compatibility.
 */
function webring_pre_init() {
	// Check if the min. required PHP version is available and if not, show an admin notice.
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		add_action( 'admin_notices', 'webring_min_php_version_error' );

		// Stop the further processing of the plugin.
		return;
	}

	if ( file_exists( WEBRING_PATH . 'src/index.js' ) && ! file_exists( WEBRING_PATH . 'build/index.js' ) ) {
		add_action( 'admin_notices', 'webring_build_files_missing' );

		// Stop the further processing of the plugin.
		return;
	}

	if ( file_exists( WEBRING_PATH . 'composer.json' ) && ! file_exists( WEBRING_PATH . 'vendor/autoload.php' ) ) {
		add_action( 'admin_notices', 'webring_autoloader_missing' );

		// Stop the further processing of the plugin.
		return;
	} else {
		$autoloader = WEBRING_PATH . 'vendor/autoload.php';

		if ( is_readable( $autoloader ) ) {
			include $autoloader;
		}
	}

	// Add and remove rewrite rules on activation and deactivation.
	register_activation_hook( __FILE__, [ Setup::class, 'activated' ] );
	register_deactivation_hook( __FILE__, [ Setup::class, 'activated' ] );

	// If all checks were successful, load the plugin.
	require_once WEBRING_PATH . 'lib/load.php';
}

/**
 * Show an admin notice error message if the PHP version is too low.
 */
function webring_min_php_version_error() {
	echo '<div class="error"><p>';
	esc_html_e( 'Webring requires PHP version 7.4 or higher to function properly. Please upgrade PHP or deactivate Webring.', 'webring' );
	echo '</p></div>';
}

/**
 * Show an admin notice error message if the composer autoloader is missing.
 */
function webring_autoloader_missing() {
	echo '<div class="error"><p>';
	esc_html_e( 'Webring is missing the Composer autoloader file. Please run `composer install --no-dev -o` in the root folder of the plugin or use a release version including the `vendor` folder.', 'webring' );
	echo '</p></div>';
}

/**
 * Show an admin notice error message if the build files are missing.
 */
function webring_build_files_missing() {
	echo '<div class="error"><p>';
	esc_html_e( 'Webring is missing the build file. Please run `npm install` and `npm run build` in the root folder of the plugin or use a release version including the `build` folder.', 'webring' );
	echo '</p></div>';
}
