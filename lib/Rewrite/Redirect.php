<?php
/**
 * Redirect Class
 *
 * @package WebringManager
 */

namespace WebringManager\Rewrite;

/**
 * Redirect Class
 *
 * @phpstan-type WP_Post_Row object{
 *   ID: int,
 *   menu_order: string,
 *   post_date: string
 * }
 *
 * @package WebringManager
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

		// The post meta always has a trailing slash.
		$domain = trailingslashit( $domain );

		if ( ! $action || ! $domain ) {
			return;
		}

		if ( ! in_array( $action, $this->allowed_actions, true ) ) {
			return;
		}

		$target_url = $this->get_new_webring_site( $action, $domain, $category );
		if ( ! $target_url ) {
			wp_die( esc_html__( 'Target site not found', 'webring-manager' ), 'WebringManager', 404 );
		}

		wp_redirect( $target_url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	}

	/**
	 * Retrieves the URL of the next, previous, or random site in a webring.
	 *
	 * @param string  $action   The action to perform: 'next', 'prev' or 'random'.
	 * @param string  $url      The current site's domain.
	 * @param ?string $category Optional. The category slug to filter sites by. Default null.
	 *
	 * @return string The URL of the new site in the webring, or an empty string if no site is found.
	 */
	public function get_new_webring_site( string $action, string $url, ?string $category ): string {

		// phpcs:ignore Generic.Commenting.DocComment.MissingShort
		/** @var WP_Post_Row $current_site */
		$current_site = $this->get_site_by_domain( $url );
		if ( ! $current_site ) {
			wp_die( 'Webring site not found', 'WebringManager', 404 );
		}

		$current_site_url_sanitized = get_post_meta( $current_site->ID, '_webring_website_url_sanitized', true );

		$term_tax_id = null;
		if ( $category ) {
			// phpcs:ignore Generic.Commenting.DocComment.MissingShort
			/** @var \WP_Term $term */
			$term = get_term_by( 'slug', $category, 'webring_category' );
			if ( ! $term ) {
				return '';
			}
			$term_tax_id = $term->term_taxonomy_id;
		}

		$sites = $this->query_candidate_sites(
			$action,
			$current_site,
			$current_site_url_sanitized,
			$term_tax_id
		);

		if ( empty( $sites ) ) {
			return '';
		}

		$url = trim( get_post_meta( $sites[0], 'webring_website_url', true ) );
		if ( ! $url ) {
			return '';
		}

		if ( ! preg_match( '#^https?://#', $url ) ) {
			$url = 'https://' . $url;
		}

		return esc_url_raw( $url );
	}

	/**
	 * Builds and executes the query for candidate site IDs.
	 *
	 * @phpstan-param WP_Post_Row $current_site
	 *
	 * @param string   $action                     The navigation action.
	 * @param object   $current_site               The current site object.
	 * @param string   $current_site_url_sanitized The sanitized current site URL.
	 * @param int|null $term_tax_id                Optional term taxonomy ID when filtering by category.
	 *
	 * @return array<int> List of matching post IDs.
	 */
	private function query_candidate_sites( string $action, object $current_site, string $current_site_url_sanitized, ?int $term_tax_id = null ): array {
		$clause_info = $this->get_navigation_clauses( $action, $current_site );

		if ( null === $clause_info ) {
			return [];
		}

		$main_sites = $this->run_candidate_query(
			$current_site_url_sanitized,
			$clause_info['where_clause'],
			$term_tax_id ? $clause_info['term_order_clause'] : $clause_info['order_clause'],
			$term_tax_id
		);

		if ( ! empty( $main_sites ) || 'random' === $action ) {
			return $main_sites;
		}

		if ( 'prev' !== $action && 'next' !== $action ) {
			return [];
		}

		return $this->run_candidate_query(
			$current_site_url_sanitized,
			'',
			$term_tax_id ? $clause_info['term_order_clause'] : $clause_info['order_clause'],
			$term_tax_id
		);
	}

	/**
	 * Returns SQL clause data for the requested navigation action.
	 *
	 * @phpstan-param WP_Post_Row $current_site
	 *
	 * @param string $action       The navigation action.
	 * @param object $current_site The current site object.
	 *
	 * @return array<string, string>|null
	 */
	private function get_navigation_clauses( string $action, object $current_site ): ?array {
		if ( 'prev' === $action ) {
			return [
				'where_clause'      => $this->build_boundary_clause( '<=', $current_site ),
				'order_clause'      => 'ORDER BY p.menu_order DESC, p.post_date DESC, p.ID DESC',
				'term_order_clause' => 'ORDER BY tr.term_order DESC, p.menu_order DESC, p.post_date DESC, p.ID DESC',
			];
		}

		if ( 'next' === $action ) {
			return [
				'where_clause'      => $this->build_boundary_clause( '>=', $current_site ),
				'order_clause'      => 'ORDER BY p.menu_order ASC, p.post_date ASC, p.ID ASC',
				'term_order_clause' => 'ORDER BY tr.term_order ASC, p.menu_order ASC, p.post_date ASC, p.ID ASC',
			];
		}

		if ( 'random' === $action ) {
			return [
				'where_clause'      => '',
				'order_clause'      => 'ORDER BY RAND()',
				'term_order_clause' => 'ORDER BY RAND()',
			];
		}

		return null;
	}

	/**
	 * Builds the boundary clause for prev/next navigation.
	 *
	 * @phpstan-param WP_Post_Row $current_site
	 *
	 * @param string $operator     Comparison operator.
	 * @param object $current_site The current site object.
	 *
	 * @return string
	 */
	private function build_boundary_clause( string $operator, object $current_site ): string {
		global $wpdb;

		if ( ! in_array( $operator, [ '>=', '<=' ], true ) ) {
			return '';
		}

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->prepare(
			'AND p.menu_order ' . $operator . ' %d AND p.post_date ' . $operator . ' %s ',
			$current_site->menu_order,
			$current_site->post_date
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Executes the candidate site query.
	 *
	 * @param string   $current_site_url_sanitized The sanitized current site URL.
	 * @param string   $where_clause               Optional WHERE fragment.
	 * @param string   $order_clause               ORDER BY fragment.
	 * @param int|null $term_tax_id                Optional term taxonomy ID.
	 *
	 * @return array<int>
	 */
	private function run_candidate_query( string $current_site_url_sanitized, string $where_clause, string $order_clause, ?int $term_tax_id = null ): array {
		global $wpdb;

		$sql = "
			SELECT p.ID
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
		";

		if ( $term_tax_id ) {
			$sql .= $wpdb->prepare(
				"
				INNER JOIN {$wpdb->term_relationships} tr ON (
					p.ID = tr.object_id
					AND tr.term_taxonomy_id = %d
				)
				",
				$term_tax_id
			);
		}

		$sql .= "
			WHERE p.post_type = 'webring_website'
			  AND p.post_status = 'publish'
		";

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql .= $wpdb->prepare(
			"
			  AND pm.meta_key = '_webring_website_url_sanitized'
			  AND pm.meta_value != %s
			  {$where_clause}
			{$order_clause}
			LIMIT 1
			",
			$current_site_url_sanitized
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		// phpcs:ignore WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB
		return $wpdb->get_col( $sql );
	}

	/**
	 * Retrieves the site ID based on the given domain name.
	 *
	 * This function queries the database to find a published post of type 'webring_website'
	 * matching the provided domain name.
	 *
	 * @param string $domain The domain name to search for. The domain is normalized to lowercase.
	 *
	 * @return object Post objects of the matching sites, or null if no site is found.
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
				INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
				WHERE p.post_type = 'webring_website'
				  AND p.post_status = 'publish'
				  AND pm.meta_key = '_webring_website_url_sanitized'
				  AND pm.meta_value = %s
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
