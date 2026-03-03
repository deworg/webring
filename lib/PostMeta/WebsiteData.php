<?php
/**
 * WebsiteData Class
 *
 * @package webring
 */

namespace Webring\PostMeta;

/**
 * WebsiteData Class
 *
 * @package webring
 */
class WebsiteData {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_post_meta' ] );
	}

	/**
	 * Registers custom post meta fields for webring websites.
	 *
	 * @return void
	 */
	public function register_post_meta() {
		register_post_meta(
			'webring_website',
			'webring_website_url',
			[
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_url',
			]
		);
	}
}
