<?php
/**
 * RewriteRules Class
 *
 * @package webring
 */

namespace Webring\Rewrite;

/**
 * RewriteRules Class
 *
 * @package webring
 */
class RewriteRules {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'add_rewrite_rules' ] );
	}

	/**
	 * Adds custom rewrite rules for specific URL patterns to handle webring category and domain actions.
	 *
	 * @return void
	 */
	public function add_rewrite_rules() {
		// /webring/{next|prev|random}/{domain.tld}
		add_rewrite_rule(
			'^webring/(prev|next|random|jo)/([^/]+)/?$',
			'index.php?webring_action=$matches[1]&webring_domain=$matches[2]',
			'top'
		);
	}
}
