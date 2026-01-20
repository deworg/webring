<?php
/**
 * Setup Class
 *
 * @package webring
 */

namespace Webring\Rewrite;

/**
 * Setup Class
 *
 * @package webring
 */
class Setup {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init', [ $this, 'flush_rewrite_rules' ] );
	}

	/**
	 * Set the activated option when the plugin is activated.
	 *
	 * @return void
	 */
	public static function activated() {
		add_option( 'webring_activated', 'webring' );
	}

	/**
	 * Flushes WordPress rewrite rules if the plugin is activated in the admin area.
	 *
	 * This method checks if the plugin activation option is set to 'webring' in the database,
	 * deletes the option to prevent repeated flushing, and then calls the WordPress
	 * `flush_rewrite_rules` function to refresh the rewrite rules.
	 *
	 * @return void
	 */
	public function flush_rewrite_rules() {
		if ( is_admin() && get_option( 'webring_activated' ) === 'webring' ) {
			delete_option( 'webring_activated' );
			flush_rewrite_rules();
		}
	}
}
