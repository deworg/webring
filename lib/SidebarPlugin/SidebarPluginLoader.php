<?php
/**
 * SidebarPluginLoader Class
 *
 * @package WebringManager
 */

namespace WebringManager\SidebarPlugin;

/**
 * SidebarPluginLoader Class
 *
 * @package WebringManager
 */
class SidebarPluginLoader {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'enqueue_block_editor_assets' ] );
	}

	/**
	 * Enqueues block editor assets.
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets() {
		$asset = include WEBRING_MANAGER_PATH . '/build/sidebar-plugin.asset.php';

		wp_enqueue_script(
			'webring-sidebar-plugin',
			WEBRING_MANAGER_URL . '/build/sidebar-plugin.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
