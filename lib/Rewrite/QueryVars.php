<?php
/**
 * QueryVars Class
 *
 * @package WebringManager
 */

namespace WebringManager\Rewrite;

/**
 * QueryVars Class
 *
 * @package WebringManager
 */
class QueryVars {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'query_vars', [ $this, 'query_vars' ] );
	}

	/**
	 * Modifies the list of query variables to include custom variables used for webring functionality.
	 *
	 * @param array<int, string> $vars An array of existing query variables.
	 *
	 * @return array<int, string> The array of query variables, including custom webring variables.
	 */
	public function query_vars( $vars ): array {
		$vars[] = 'webring_action';
		$vars[] = 'webring_domain';
		$vars[] = 'webring_category';

		return $vars;
	}
}
