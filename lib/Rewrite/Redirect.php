<?php
/**
 * Redirect Class
 *
 * @package webring
 */

namespace Webring\Rewrite;

/**
 * Redirect Class
 *
 * @package webring
 */
class Redirect {
	/**
	 * An array defining the actions that are permitted.
	 *
	 * @var string[] A list of allowed actions.
	 */
	public array $allowed_actions = [ 'prev', 'next', 'random' ];

	/**
	 * Initializes the Class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'template_redirect', [ $this, 'redirect' ] );
	}

	/**
	 * Handles redirection based on query variables and predefined actions.
	 *
	 * @return void
	 */
	public function redirect() {
		$action   = get_query_var( 'webring_action' );
		$domain   = get_query_var( 'webring_domain' );
		$category = get_query_var( 'webring_category' );

		if ( ! $action || ! $domain ) {
			return;
		}

		if ( ! in_array( $action, $this->allowed_actions, true ) ) {
			return;
		}

		$target_url = $this->get_new_webring_site( $action, $domain, $category );
		if ( ! $target_url ) {
			wp_die( esc_html__( 'Target site not found', 'webring' ), 'Webring', 404 );
		}

		wp_redirect( $target_url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	}

	/**
	 * Retrieves the URL of the next, previous or random site in a webring.
	 *
	 * @param string      $action   The action to perform: 'next', 'prev' or 'random'.
	 * @param string      $domain   The current site's domain.
	 * @param string|null $category Optional. The category slug to filter sites by. Default null.
	 *
	 * @return string The URL of the new site in the webring, or an empty string if no site is found.
	 */
	public function get_new_webring_site( string $action, string $domain, string $category = null ): string {
		// phpcs:disable WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$current_site = $this->get_site_by_domain( $domain );
		if ( ! $current_site ) {
			wp_die( 'Webring site not found', 'Webring', 404 );
		}

		global $wpdb;

		if ( $category ) {
			$term = get_term_by( 'slug', $category, 'webring_category' );
			if ( ! $term ) {
				return '';
			}

			$term_tax_id = $term->term_taxonomy_id;

			if ( 'prev' === $action ) {
				$where_clause = '
					  AND p.menu_order <= %d
					  AND p.post_date <= %s
				';
				$order_clause = 'ORDER BY tr.term_order DESC, p.menu_order DESC, p.post_date DESC, p.ID DESC';
			} elseif ( 'next' === $action ) {
				$where_clause = '
					  AND p.menu_order >= %d
					  AND p.post_date >= %s
				';
				$order_clause = 'ORDER BY tr.term_order ASC, p.menu_order ASC, p.post_date ASC, p.ID ASC';
			} else {
				$where_clause = '
					  -- ignoring menu_order %d
					  -- ignoring post_date %s
				';
				$order_clause = 'ORDER BY RAND()';
			}

			$sites = $wpdb->get_col(
				$wpdb->prepare(
					"
					SELECT p.ID
					FROM {$wpdb->posts} p
					INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
					WHERE p.post_type = 'webring_website'
					  AND p.post_status = 'publish'
					  AND tr.term_taxonomy_id = %d
					  AND p.post_title != %s
					  {$where_clause}
					{$order_clause}
					LIMIT 1
					",
					$term_tax_id,
					$current_site->post_title,
					$current_site->menu_order,
					$current_site->post_date
				)
			);
			// If no site was found, we might hit the beginning or end of the webring.
			if ( empty( $sites ) ) {
				if ( 'prev' === $action ) {
					$sites = $wpdb->get_col(
						$wpdb->prepare(
							"
							SELECT p.ID
							FROM {$wpdb->posts} p
							INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
							WHERE p.post_type = 'webring_website'
							  AND p.post_status = 'publish'
							  AND tr.term_taxonomy_id = %d
							  AND p.post_title != %s
							ORDER BY tr.term_order DESC, p.menu_order DESC, p.post_date DESC, p.ID DESC
							LIMIT 1
							",
							$term_tax_id,
							$current_site->post_title
						)
					);
				} elseif ( 'next' === $action ) {
					$sites = $wpdb->get_col(
						$wpdb->prepare(
							"
							SELECT p.ID
							FROM {$wpdb->posts} p
							INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
							WHERE p.post_type = 'webring_website'
							  AND p.post_status = 'publish'
							  AND tr.term_taxonomy_id = %d
							  AND p.post_title != %s
							ORDER BY tr.term_order ASC, p.menu_order ASC, p.post_date ASC, p.ID ASC
							LIMIT 1
							",
							$term_tax_id,
							$current_site->post_title
						)
					);
				}
			}
		} else {
			if ( 'prev' === $action ) {
				$where_clause = '
					  AND p.menu_order <= %d
					  AND p.post_date <= %s
				';
				$order_clause = 'ORDER BY p.menu_order DESC, p.post_date DESC, p.ID DESC';
			} elseif ( 'next' === $action ) {
				$where_clause = '
					  AND p.menu_order >= %d
					  AND p.post_date >= %s
				';
				$order_clause = 'ORDER BY p.menu_order ASC, p.post_date ASC, p.ID ASC';
			} else {
				$where_clause = '
					  -- ignoring menu_order %d
					  -- ignoring post_date %s
				';
				$order_clause = 'ORDER BY RAND()';
			}

			$sites = $wpdb->get_col(
				$wpdb->prepare(
					"
					SELECT p.ID
					FROM {$wpdb->posts} p
					WHERE p.post_type = 'webring_website'
					  AND p.post_status = 'publish'
					  AND p.post_title != %s
					  {$where_clause}
					{$order_clause}
					LIMIT 1
					",
					$current_site->post_title,
					$current_site->menu_order,
					$current_site->post_date
				)
			);

			// If no site was found, we might hit the beginning or end of the webring.
			if ( empty( $sites ) ) {
				if ( 'prev' === $action ) {
					$sites = $wpdb->get_col(
						$wpdb->prepare(
							"
							SELECT p.ID
							FROM {$wpdb->posts} p
							WHERE p.post_type = 'webring_website'
							  AND p.post_status = 'publish'
							  AND p.post_title != %s
							ORDER BY p.menu_order DESC, p.post_date DESC, p.ID DESC
							LIMIT 1
							",
							$current_site->post_title
						)
					);
				} elseif ( 'next' === $action ) {
					$sites = $wpdb->get_col(
						$wpdb->prepare(
							"
							SELECT p.ID
							FROM {$wpdb->posts} p
							WHERE p.post_type = 'webring_website'
							  AND p.post_status = 'publish'
							  AND p.post_title != %s
							ORDER BY p.menu_order ASC, p.post_date ASC, p.ID ASC
							LIMIT 1
							",
							$current_site->post_title
						)
					);
				}
			}

			if ( empty( $sites ) ) {
				return '';
			}
		}

		if ( empty( $sites ) ) {
			return '';
		}

		// Get domain from post_title (adjust if using post_name or custom field).
		$domain = trim( get_post_field( 'post_title', $sites[0] ) );
		if ( ! $domain ) {
			return '';
		}

		// Ensure full URL.
		if ( ! preg_match( '#^https?://#', $domain ) ) {
			$domain = 'https://' . $domain;
		}

		// phpcs:enable WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber,WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return esc_url_raw( $domain );
	}

	/**
	 * Retrieves the site ID based on the given domain name.
	 *
	 * This function queries the database to find a published post of type 'webring_website'
	 * matching the provided domain name.
	 *
	 * @param string $domain The domain name to search for. The domain is normalized to lowercase.
	 *
	 * @return object|null The ID of the matching site, or null if no site is found.
	 */
	public function get_site_by_domain( string $domain ): ?object {
		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		global $wpdb;

		// Normalize domain if needed (lowercase, strip protocol).
		$domain = strtolower( $domain );

		$current_site = $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT p.*
				FROM {$wpdb->posts} p
				WHERE p.post_type = 'webring_website'
				  AND p.post_status = 'publish'
				  AND p.post_title = %s
				",
				$domain
			)
		);

		if ( empty( $current_site ) ) {
			return null;
		}

		// phpcs:enable WordPress.DB.DirectDatabaseQuery

		return $current_site;
	}
}
