<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * @package WebringManager
 *
 *  The following variables are exposed to the file:
 *
 * @var array    $attributes The block attributes.
 * @var string   $content    The block default content.
 * @var WP_Block $block      The block instance.
 *
 * @see     https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

$args = [
	'post_type'   => 'webring_website',
	'post_status' => 'publish',
];

if ( ! empty( $attributes['categories'] ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$args['tax_query'] = [
		[
			'taxonomy' => 'webring_category',
			'field'    => 'term_id',
			'terms'    => array_column( $attributes['categories'], 'id' ),
		],
	];
}

$query            = new WP_Query();
$webring_websites = $query->query( $args );

if ( empty( $webring_websites ) ) {
	if ( ! wp_is_serving_rest_request() ) {
		return;
	}

	printf(
		'<div class="components-placeholder"><div class="components-placeholder__fieldset">%s</div></div>',
		esc_html__( 'No websites found. Try to change your filters', 'webring-manager' ),
	);

	return;
} else {
	if ( isset( $attributes['displayFeaturedImage'] ) && $attributes['displayFeaturedImage'] ) {
		update_post_thumbnail_cache( $query );
	}

	$list_items_markup = '';

	foreach ( $webring_websites as $website ) {
		$website_link  = get_post_meta( $website, 'webring_website_url' );
		$website_title = get_the_title( $website );

		if ( ! $website_title ) {
			$website_title = esc_html__( '(no title)', 'webring-manager' );
		}

		$list_items_markup .= '<li>';

		if ( $attributes['displayFeaturedImage'] && has_post_thumbnail( $website ) ) {
			$image_style = '';
			if ( isset( $attributes['featuredImageSizeWidth'] ) ) {
				$image_style .= sprintf( 'max-width:%spx;', $attributes['featuredImageSizeWidth'] );
			}
			if ( isset( $attributes['featuredImageSizeHeight'] ) ) {
				$image_style .= sprintf( 'max-height:%spx;', $attributes['featuredImageSizeHeight'] );
			}

			$image_classes = 'wp-block-webring-manager-website-list__featured-image';
			if ( isset( $attributes['featuredImageAlign'] ) ) {
				$image_classes .= ' align' . $attributes['featuredImageAlign'];
			}

			$featured_image = get_the_post_thumbnail(
				$website,
				$attributes['featuredImageSizeSlug'],
				[
					'style' => esc_attr( $image_style ),
				]
			);
			if ( $attributes['addLinkToFeaturedImage'] ) {
				$featured_image = sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( $website_link ),
					esc_attr( $website_title ),
					$featured_image
				);
			}
			$list_items_markup .= sprintf(
				'<div class="%1$s">%2$s</div>',
				esc_attr( $image_classes ),
				$featured_image
			);
		}

		$list_items_markup .= sprintf(
			'<a class="wp-block-webring-manager-website-list__post-title" href="%1$s">%2$s</a>',
			esc_url( $website_link ),
			esc_html( $website_title )
		);

		$list_items_markup .= "</li>\n";
	}

	$classes = [ 'wp-block-webring-manager-website-list__list' ];
	if ( isset( $attributes['postLayout'] ) && 'grid' === $attributes['postLayout'] ) {
		$classes[] = 'is-grid';
	}
	if ( isset( $attributes['columns'] ) && 'grid' === $attributes['postLayout'] ) {
		$classes[] = 'columns-' . $attributes['columns'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}

	$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => implode( ' ', $classes ) ] );

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	printf(
		'<ul %1$s>%2$s</ul>',
		$wrapper_attributes,
		$list_items_markup
	);
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
}
