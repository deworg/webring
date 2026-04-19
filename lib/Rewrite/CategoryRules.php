<?php
/**
 * CategoryRules Class
 *
 * @package WebringManager
 */

namespace WebringManager\Rewrite;

/**
 * CategoryRules Class
 *
 * @package WebringManager
 */
class CategoryRules {
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
		// /webring/{category-slug}/{next|prev|random}/{domain.tld/some/optional-path}
		add_rewrite_rule(
			'^webring/(.*)/(prev|next|random)/(.*)$',
			'index.php?webring_category=$matches[1]&webring_action=$matches[2]&webring_domain=$matches[3]',
			'top'
		);
	}
}
