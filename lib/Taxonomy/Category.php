<?php
/**
 * Category Class
 *
 * @package webring
 */

namespace Webring\Taxonomy;

/**
 * Category Class
 *
 * @package webring
 */
class Category {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
		add_filter( 'term_updated_messages', [ $this, 'updated_messages' ] );
	}

	/**
	 * Registers the `webring_category` taxonomy,
	 * for use with 'webring_website'.
	 */
	public function register_taxonomy() {
		register_taxonomy(
			'webring_category',
			[ 'webring_website' ],
			[
				'hierarchical'          => true,
				'public'                => true,
				'show_in_nav_menus'     => true,
				'show_ui'               => true,
				'show_admin_column'     => false,
				'query_var'             => true,
				'rewrite'               => [
					'hierarchical' => true,
				],
				'capabilities'          => [
					'manage_terms' => 'edit_posts',
					'edit_terms'   => 'edit_posts',
					'delete_terms' => 'edit_posts',
					'assign_terms' => 'edit_posts',
				],
				'labels'                => [
					'name'                       => __( 'Categories', 'webring' ),
					'singular_name'              => _x( 'Category', 'taxonomy general name', 'webring' ),
					'search_items'               => __( 'Search Categories', 'webring' ),
					'popular_items'              => __( 'Popular Categories', 'webring' ),
					'all_items'                  => __( 'All Categories', 'webring' ),
					'parent_item'                => __( 'Parent Category', 'webring' ),
					'parent_item_colon'          => __( 'Parent Category:', 'webring' ),
					'edit_item'                  => __( 'Edit Category', 'webring' ),
					'update_item'                => __( 'Update Category', 'webring' ),
					'view_item'                  => __( 'View Category', 'webring' ),
					'add_new_item'               => __( 'Add New Category', 'webring' ),
					'new_item_name'              => __( 'New Category', 'webring' ),
					'separate_items_with_commas' => __( 'Separate Categories with commas', 'webring' ),
					'add_or_remove_items'        => __( 'Add or remove Categories', 'webring' ),
					'choose_from_most_used'      => __( 'Choose from the most used Categories', 'webring' ),
					'not_found'                  => __( 'No Categories found.', 'webring' ),
					'no_terms'                   => __( 'No Categories', 'webring' ),
					'menu_name'                  => __( 'Categories', 'webring' ),
					'items_list_navigation'      => __( 'Categories list navigation', 'webring' ),
					'items_list'                 => __( 'Categories list', 'webring' ),
					'most_used'                  => _x( 'Most Used', 'webring_category', 'webring' ),
					'back_to_items'              => __( '&larr; Back to Categories', 'webring' ),
				],
				'show_in_rest'          => true,
				'rest_base'             => 'webring_category',
				'rest_controller_class' => 'WP_REST_Terms_Controller',
			]
		);
	}

	/**
	 * Sets the post updated messages for the `webring_category` taxonomy.
	 *
	 * @param array $messages Post updated messages.
	 *
	 * @return array Messages for the `webring_category` taxonomy.
	 */
	public function updated_messages( $messages ): array {

		$messages['webring_category'] = [
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Category added.', 'webring' ),
			2 => __( 'Category deleted.', 'webring' ),
			3 => __( 'Category updated.', 'webring' ),
			4 => __( 'Category not added.', 'webring' ),
			5 => __( 'Category not updated.', 'webring' ),
			6 => __( 'Categories deleted.', 'webring' ),
		];

		return $messages;
	}
}
