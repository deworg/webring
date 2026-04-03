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
		add_action( 'save_post', [ $this, 'save_sanitized_url_meta' ], 99, 2 );
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

	/**
	 * Saves sanitized URL meta for webring websites.
	 *
	 * @param int     $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 *
	 * @return void
	 */
	public function save_sanitized_url_meta( $post_id, $post ) {
		if ( 'webring_website' !== $post->post_type ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$url = get_post_meta( $post_id, 'webring_website_url', true );

		if ( '' === $url ) {
			delete_post_meta( $post_id, '_webring_website_url_sanitized' );

			return;
		}

		$sanitized = $this->normalize_url( $url );

		if ( '' === $sanitized ) {
			delete_post_meta( $post_id, '_webring_website_url_sanitized' );

			return;
		}

		update_post_meta( $post_id, '_webring_website_url_sanitized', $sanitized );
	}

	/**
	 * Normalizes a URL by trimming it and handling bare domains.
	 *
	 * @param string $url The URL to normalize.
	 *
	 * @return string The normalized URL.
	 */
	public function normalize_url( $url ): string {
		$url = trim( (string) $url );

		if ( '' === $url ) {
			return '';
		}

		// Accept bare domains too.
		if ( ! preg_match( '#^[a-z][a-z0-9+\-.]*://#i', $url ) ) {
			$url = 'https://' . $url;
		}

		$parts = wp_parse_url( $url );

		if ( empty( $parts['host'] ) ) {
			return '';
		}

		$host = strtolower( $parts['host'] );
		$path = '';

		if ( ! empty( $parts['path'] ) && '/' !== $parts['path'] ) {
			$path = untrailingslashit( $parts['path'] );
		}

		return $host . $path;
	}
}
