<?php
/**
 * Website Class
 *
 * @package WebringManager
 */

namespace WebringManager\PostType;

/**
 * Website Class
 *
 * @package WebringManager
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
		add_filter( 'manage_webring_website_posts_columns', [ $this, 'add_webring_category_column' ] );
		add_action( 'manage_webring_website_posts_custom_column', [ $this, 'display_webring_category_column' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'add_webring_category_filter' ] );
	}

	/**
	 * Registers the `webring_website` post type.
	 */
	public function register_post_type(): void {
		register_post_type(
			'webring_website',
			[
				'labels'                => [
					'name'                  => __( 'Websites', 'webring-manager' ),
					'singular_name'         => __( 'Website', 'webring-manager' ),
					'all_items'             => __( 'All Websites', 'webring-manager' ),
					'archives'              => __( 'Website Archives', 'webring-manager' ),
					'attributes'            => __( 'Website Attributes', 'webring-manager' ),
					'insert_into_item'      => __( 'Insert into Website', 'webring-manager' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Website', 'webring-manager' ),
					'featured_image'        => _x( 'Featured Image', 'webring-manager_website', 'webring-manager' ),
					'set_featured_image'    => _x( 'Set featured image', 'webring_website', 'webring-manager' ),
					'remove_featured_image' => _x( 'Remove featured image', 'webring_website', 'webring-manager' ),
					'use_featured_image'    => _x( 'Use as featured image', 'webring_website', 'webring-manager' ),
					'filter_items_list'     => __( 'Filter Websites list', 'webring-manager' ),
					'items_list_navigation' => __( 'Websites list navigation', 'webring-manager' ),
					'items_list'            => __( 'Websites list', 'webring-manager' ),
					'new_item'              => __( 'New Website', 'webring-manager' ),
					'add_new'               => __( 'Add New', 'webring-manager' ),
					'add_new_item'          => __( 'Add New Website', 'webring-manager' ),
					'edit_item'             => __( 'Edit Website', 'webring-manager' ),
					'view_item'             => __( 'View Website', 'webring-manager' ),
					'view_items'            => __( 'View Websites', 'webring-manager' ),
					'search_items'          => __( 'Search Websites', 'webring-manager' ),
					'not_found'             => __( 'No Websites found', 'webring-manager' ),
					'not_found_in_trash'    => __( 'No Websites found in trash', 'webring-manager' ),
					'parent_item_colon'     => __( 'Parent Website:', 'webring-manager' ),
					'menu_name'             => __( 'Webring', 'webring-manager' ),
				],
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_nav_menus'     => true,
				'supports'              => [
					'custom-fields',
					'editor',
					'thumbnail',
					'title',
				],
				'taxonomies'            => [
					'webring_category',
				],
				'has_archive'           => true,
				'rewrite'               => true,
				'query_var'             => true,
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
	 * @param array<string, array<int, string|bool>> $messages Post updated messages.
	 *
	 * @return array<string, array<int, string|bool>> Messages for the `webring_website` post type.
	 */
	public function updated_messages( array $messages ): array {
		global $post;

		// phpcs:ignore Generic.Commenting.DocComment.MissingShort
		/** @var string $permalink */
		$permalink = get_permalink( $post );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$messages['webring_website'] = [
			0  => '',
			// Unused. Messages start at index 1.
			/* translators: %s: post permalink */
			1  => sprintf( __( 'Website updated. <a target="_blank" href="%s">View Website</a>', 'webring-manager' ), esc_url( $permalink ) ),
			2  => __( 'Custom field updated.', 'webring-manager' ),
			3  => __( 'Custom field deleted.', 'webring-manager' ),
			4  => __( 'Website updated.', 'webring-manager' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Website restored to revision from %s', 'webring-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s: post permalink */
			6  => sprintf( __( 'Website published. <a href="%s">View Website</a>', 'webring-manager' ), esc_url( $permalink ) ),
			7  => __( 'Website saved.', 'webring-manager' ),
			/* translators: %s: post permalink */
			8  => sprintf( __( 'Website submitted. <a target="_blank" href="%s">Preview Website</a>', 'webring-manager' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			9  => sprintf( __( 'Website scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Website</a>', 'webring-manager' ), date_i18n( __( 'M j, Y @ G:i', 'webring-manager' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			/* translators: %s: post permalink */
			10 => sprintf( __( 'Website draft updated. <a target="_blank" href="%s">Preview Website</a>', 'webring-manager' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		];

		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $messages;
	}

	/**
	 * Sets the bulk post updated messages for the `webring_website` post type.
	 *
	 * @param array<string, array<string, string>> $bulk_messages Arrays of messages, each keyed by the corresponding
	 *                                                            post type. Messages are keyed with 'updated',
	 *                                                            'locked', 'deleted', 'trashed', and 'untrashed'.
	 * @param int[]                                $bulk_counts   Array of item counts for each message, used to build
	 *                                                            internationalized strings.
	 *
	 * @return array<string, array<string, string>> Bulk messages for the `webring_website` post type.
	 */
	public function bulk_updated_messages( array $bulk_messages, array $bulk_counts ): array {
		$bulk_messages['webring_website'] = [
			/* translators: %s: Number of Websites. */
			'updated'   => _n( '%s Website updated.', '%s Websites updated.', $bulk_counts['updated'], 'webring-manager' ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Website not updated, somebody is editing it.', 'webring-manager' ) :
				/* translators: %s: Number of Websites. */
				_n( '%s Website not updated, somebody is editing it.', '%s Websites not updated, somebody is editing them.', $bulk_counts['locked'], 'webring-manager' ),
			/* translators: %s: Number of Websites. */
			'deleted'   => _n( '%s Website permanently deleted.', '%s Websites permanently deleted.', $bulk_counts['deleted'], 'webring-manager' ),
			/* translators: %s: Number of Websites. */
			'trashed'   => _n( '%s Website moved to the Trash.', '%s Websites moved to the Trash.', $bulk_counts['trashed'], 'webring-manager' ),
			/* translators: %s: Number of Websites. */
			'untrashed' => _n( '%s Website restored from the Trash.', '%s Websites restored from the Trash.', $bulk_counts['untrashed'], 'webring-manager' ),
		];

		return $bulk_messages;
	}

	/**
	 * Adds the Webring Category column to the Websites list table.
	 *
	 * @param array<string, string> $columns The columns array.
	 *
	 * @return array<string, string> Modified columns array.
	 */
	public function add_webring_category_column( array $columns ): array {
		$new_columns = [];
		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;
			if ( 'title' === $key ) {
				$new_columns['webring_category'] = __( 'Webring Category', 'webring-manager' );
			}
		}

		return $new_columns;
	}

	/**
	 * Displays the Webring Category in the custom column.
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int    $post_id     The ID of the post.
	 */
	public function display_webring_category_column( string $column_name, int $post_id ): void {
		if ( 'webring_category' === $column_name ) {
			$terms = get_the_terms( $post_id, 'webring_category' );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$term_names = [];
				foreach ( $terms as $term ) {
					$term_names[] = esc_html( $term->name );
				}
				echo implode( ', ', $term_names ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo esc_html__( 'No Category', 'webring-manager' );
			}
		}
	}

	/**
	 * Adds a dropdown filter for Webring Categories on the Websites administration page.
	 *
	 * @param string $post_type The current post type.
	 */
	public function add_webring_category_filter( string $post_type ): void {
		if ( 'webring_website' === $post_type ) {
			$taxonomy = 'webring_category';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$selected = isset( $_GET[ $taxonomy ] ) ? sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ) ) : '';
			$args     = [
				'show_option_all' => __( 'All Webring Categories', 'webring-manager' ),
				'taxonomy'        => $taxonomy,
				'name'            => $taxonomy,
				'orderby'         => 'name',
				'selected'        => $selected,
				'value_field'     => 'slug',
				'hierarchical'    => true,
			];
			wp_dropdown_categories( $args );
		}
	}
}
