<?php
/**
 * Category Class
 *
 * @package WebringManager
 */

namespace WebringManager\Taxonomy;

/**
 * Category Class
 *
 * @package WebringManager
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
	public function register_taxonomy() :void{
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
					'name'                       => __( 'Categories', 'webring-manager' ),
					'singular_name'              => _x( 'Category', 'taxonomy general name', 'webring-manager' ),
					'search_items'               => __( 'Search Categories', 'webring-manager' ),
					'popular_items'              => __( 'Popular Categories', 'webring-manager' ),
					'all_items'                  => __( 'All Categories', 'webring-manager' ),
					'parent_item'                => __( 'Parent Category', 'webring-manager' ),
					'parent_item_colon'          => __( 'Parent Category:', 'webring-manager' ),
					'edit_item'                  => __( 'Edit Category', 'webring-manager' ),
					'update_item'                => __( 'Update Category', 'webring-manager' ),
					'view_item'                  => __( 'View Category', 'webring-manager' ),
					'add_new_item'               => __( 'Add New Category', 'webring-manager' ),
					'new_item_name'              => __( 'New Category', 'webring-manager' ),
					'separate_items_with_commas' => __( 'Separate Categories with commas', 'webring-manager' ),
					'add_or_remove_items'        => __( 'Add or remove Categories', 'webring-manager' ),
					'choose_from_most_used'      => __( 'Choose from the most used Categories', 'webring-manager' ),
					'not_found'                  => __( 'No Categories found.', 'webring-manager' ),
					'no_terms'                   => __( 'No Categories', 'webring-manager' ),
					'menu_name'                  => __( 'Categories', 'webring-manager' ),
					'items_list_navigation'      => __( 'Categories list navigation', 'webring-manager' ),
					'items_list'                 => __( 'Categories list', 'webring-manager' ),
					'most_used'                  => _x( 'Most Used', 'webring_category', 'webring-manager' ),
					'back_to_items'              => __( '&larr; Back to Categories', 'webring-manager' ),
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
	 * @param array<string, array<int, string>> $messages Post updated messages.
	 *
	 * @return array<string, array<int, string>> Messages for the `webring_category` taxonomy.
	 */
	public function updated_messages( $messages ): array {
		$messages['webring_category'] = [
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Category added.', 'webring-manager' ),
			2 => __( 'Category deleted.', 'webring-manager' ),
			3 => __( 'Category updated.', 'webring-manager' ),
			4 => __( 'Category not added.', 'webring-manager' ),
			5 => __( 'Category not updated.', 'webring-manager' ),
			6 => __( 'Categories deleted.', 'webring-manager' ),
		];

		return $messages;
	}
}
