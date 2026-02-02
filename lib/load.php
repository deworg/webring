<?php
/**
 * Main plugin file to load other classes
 *
 * @package Webring
 */

namespace Webring;

use Webring\Block\ManifestLoader;
use Webring\PostType\Website;
use Webring\Rewrite\CategoryRules;
use Webring\Rewrite\QueryVars;
use Webring\Rewrite\Redirect;
use Webring\Rewrite\RewriteRules;
use Webring\Rewrite\Setup;
use Webring\Taxonomy\Category;

/**
 * Init function of the plugin
 */
function init() {
	// Construct all modules to initialize.
	$modules = [
		'block_manifest_loader'     => new ManifestLoader(),
		'post_type_webring_website' => new Website(),
		'rewrite_category_rules'    => new CategoryRules(),
		'rewrite_query_vars'        => new QueryVars(),
		'rewrite_redirect'          => new Redirect(),
		'rewrite_setup'             => new Setup(),
		'rewrite_website_rules'     => new RewriteRules(),
		'taxonomy_webring_category' => new Category(),
	];

	// Initialize all modules.
	foreach ( $modules as $module ) {
		if ( is_callable( [ $module, 'init' ] ) ) {
			call_user_func( [ $module, 'init' ] );
		}
	}
}

add_action( 'plugins_loaded', 'Webring\init' );
