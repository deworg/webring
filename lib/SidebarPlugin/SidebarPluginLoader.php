<?php
/**
 * SidebarPluginLoader Class
 *
 * @package webring
 */

namespace Webring\SidebarPlugin;

/**
 * SidebarPluginLoader Class
 *
 * @package webring
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
		$asset = include WEBRING_PATH . '/build/sidebar-plugin.asset.php';

		wp_enqueue_script(
			'webring-sidebar-plugin',
			WEBRING_URL . '/build/sidebar-plugin.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
