<?php
/**
 * ManifestLoader Class
 *
 * @package webring
 */

namespace Webring\Block;

/**
 * ManifestLoader Class
 *
 * @package webring
 */
class ManifestLoader {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_block_types' ] );
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata. Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	public function register_block_types() {
		wp_register_block_types_from_metadata_collection( WEBRING_PATH . '/build', WEBRING_PATH . '/build/blocks-manifest.php' );
	}
}
