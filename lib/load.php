<?php
/**
 * Main plugin file to load other classes
 *
 * @package WebringManager
 */

namespace WebringManager;

use WebringManager\Block\ManifestLoader;
use WebringManager\PostMeta\WebsiteData;
use WebringManager\PostType\Website;
use WebringManager\Rewrite\CategoryRules;
use WebringManager\Rewrite\QueryVars;
use WebringManager\Rewrite\Redirect;
use WebringManager\Rewrite\RewriteRules;
use WebringManager\Rewrite\Setup;
use WebringManager\SidebarPlugin\SidebarPluginLoader;
use WebringManager\Taxonomy\Category;

/**
 * Init function of the plugin
 */
function init() {
	// Construct all modules to initialize.
	$modules = [
		'block_manifest_loader'     => new ManifestLoader(),
		'post_meta_website_data'    => new WebsiteData(),
		'post_type_webring_website' => new Website(),
		'rewrite_category_rules'    => new CategoryRules(),
		'rewrite_query_vars'        => new QueryVars(),
		'rewrite_redirect'          => new Redirect(),
		'rewrite_setup'             => new Setup(),
		'rewrite_website_rules'     => new RewriteRules(),
		'sidebar_plugin_loader'     => new SidebarPluginLoader(),
		'taxonomy_webring_category' => new Category(),
	];

	// Initialize all modules.
	foreach ( $modules as $module ) {
		if ( is_callable( [ $module, 'init' ] ) ) {
			call_user_func( [ $module, 'init' ] );
		}
	}
}

add_action( 'plugins_loaded', 'WebringManager\init' );
