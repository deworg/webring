<?php
/**
 * Website Class
 *
 * @package webring
 */

namespace Webring\PostType;

/**
 * Website Class
 *
 * @package webring
 */
class Website {
	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_filter( 'post_updated_messages', [ $this, 'updated_messages' ] );
		add_filter( 'bulk_post_updated_messages', [ $this, 'bulk_updated_messages' ], 10, 2 );
	}

	/**
	 * Registers the `webring_website` post type.
	 */
	public function register_post_type() {
		register_post_type(
			'webring_website',
			[
				'labels'                => [
					'name'                  => __( 'Websites', 'webring' ),
					'singular_name'         => __( 'Website', 'webring' ),
					'all_items'             => __( 'All Websites', 'webring' ),
					'archives'              => __( 'Website Archives', 'webring' ),
					'attributes'            => __( 'Website Attributes', 'webring' ),
					'insert_into_item'      => __( 'Insert into Website', 'webring' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Website', 'webring' ),
					'featured_image'        => _x( 'Featured Image', 'webring_website', 'webring' ),
					'set_featured_image'    => _x( 'Set featured image', 'webring_website', 'webring' ),
					'remove_featured_image' => _x( 'Remove featured image', 'webring_website', 'webring' ),
					'use_featured_image'    => _x( 'Use as featured image', 'webring_website', 'webring' ),
					'filter_items_list'     => __( 'Filter Websites list', 'webring' ),
					'items_list_navigation' => __( 'Websites list navigation', 'webring' ),
					'items_list'            => __( 'Websites list', 'webring' ),
					'new_item'              => __( 'New Website', 'webring' ),
					'add_new'               => __( 'Add New', 'webring' ),
					'add_new_item'          => __( 'Add New Website', 'webring' ),
					'edit_item'             => __( 'Edit Website', 'webring' ),
					'view_item'             => __( 'View Website', 'webring' ),
					'view_items'            => __( 'View Websites', 'webring' ),
					'search_items'          => __( 'Search Websites', 'webring' ),
					'not_found'             => __( 'No Websites found', 'webring' ),
					'not_found_in_trash'    => __( 'No Websites found in trash', 'webring' ),
					'parent_item_colon'     => __( 'Parent Website:', 'webring' ),
					'menu_name'             => __( 'Webring', 'webring' ),
				],
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_nav_menus'     => true,
				'supports'              => [ 'title' ],
				'has_archive'           => true,
				'rewrite'               => true,
				'query_var'             => true,
				'menu_position'         => null,
				'menu_icon'             => 'dashicons-share-alt',
				'show_in_rest'          => true,
				'rest_base'             => 'webring_website',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			]
		);
	}

	/**
	 * Sets the post updated messages for the `webring_website` post type.
	 *
	 * @param array $messages Post updated messages.
	 *
	 * @return array Messages for the `webring_website` post type.
	 */
	public function updated_messages( array $messages ): array {
		global $post;

		$permalink = get_permalink( $post );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$messages['webring_website'] = [
			0  => '',
			// Unused. Messages start at index 1.
			/* translators: %s: post permalink */
			1  => sprintf( __( 'Website updated. <a target="_blank" href="%s">View Website</a>', 'webring' ), esc_url( $permalink ) ),
			2  => __( 'Custom field updated.', 'webring' ),
			3  => __( 'Custom field deleted.', 'webring' ),
			4  => __( 'Website updated.', 'webring' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Website restored to revision from %s', 'webring' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s: post permalink */
			6  => sprintf( __( 'Website published. <a href="%s">View Website</a>', 'webring' ), esc_url( $permalink ) ),
			7  => __( 'Website saved.', 'webring' ),
			/* translators: %s: post permalink */
			8  => sprintf( __( 'Website submitted. <a target="_blank" href="%s">Preview Website</a>', 'webring' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			9  => sprintf( __( 'Website scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Website</a>', 'webring' ), date_i18n( __( 'M j, Y @ G:i', 'webring' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			/* translators: %s: post permalink */
			10 => sprintf( __( 'Website draft updated. <a target="_blank" href="%s">Preview Website</a>', 'webring' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		];
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $messages;
	}

	/**
	 * Sets the bulk post updated messages for the `webring_website` post type.
	 *
	 * @param array $bulk_messages  Arrays of messages, each keyed by the corresponding post type. Messages are
	 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 * @param int[] $bulk_counts    Array of item counts for each message, used to build internationalized strings.
	 *
	 * @return array Bulk messages for the `webring_website` post type.
	 */
	public function bulk_updated_messages( array $bulk_messages, array $bulk_counts ): array {
		$bulk_messages['webring_website'] = [
			/* translators: %s: Number of Websites. */
			'updated'   => _n( '%s Website updated.', '%s Websites updated.', $bulk_counts['updated'], 'webring' ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Website not updated, somebody is editing it.', 'webring' ) :
				/* translators: %s: Number of Websites. */
				_n( '%s Website not updated, somebody is editing it.', '%s Websites not updated, somebody is editing them.', $bulk_counts['locked'], 'webring' ),
			/* translators: %s: Number of Websites. */
			'deleted'   => _n( '%s Website permanently deleted.', '%s Websites permanently deleted.', $bulk_counts['deleted'], 'webring' ),
			/* translators: %s: Number of Websites. */
			'trashed'   => _n( '%s Website moved to the Trash.', '%s Websites moved to the Trash.', $bulk_counts['trashed'], 'webring' ),
			/* translators: %s: Number of Websites. */
			'untrashed' => _n( '%s Website restored from the Trash.', '%s Websites restored from the Trash.', $bulk_counts['untrashed'], 'webring' ),
		];

		return $bulk_messages;
	}
}
