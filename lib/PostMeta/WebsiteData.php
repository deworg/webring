<?php
/**
 * WebsiteData Class
 *
 * @package WebringManager
 */

namespace WebringManager\PostMeta;

/**
 * WebsiteData Class
 *
 * @package WebringManager
 */
class WebsiteData {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_post_meta' ] );
		add_action( 'updated_postmeta', [ $this, 'save_sanitized_url_meta' ], 99, 3 );
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
	 * @param int    $meta_id  Meta ID.
	 * @param int    $post_id  Post ID.
	 * @param string $meta_key Meta key.
	 *
	 * @return void
	 */
	public function save_sanitized_url_meta( $meta_id, $post_id, $meta_key ) {
		if ( 'webring_website_url' !== $meta_key ) {
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
		$path = trailingslashit( $parts['path'] );

		return $host . $path;
	}
}
