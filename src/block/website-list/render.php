<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * @package webring
 *
 * The following variables are exposed to the file:
 *
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$query = new WP_Query(
	array(
		'post_type'      => 'webring_website',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $query->have_posts() ) : ?>
		<ul>
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				?>
				<li>
					<a href="<?php echo esc_url( get_post_meta( get_the_ID(), 'website_url', true ) ); ?>">
						<?php the_title(); ?>
					</a>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php
		wp_reset_postdata();
	else :
		?>
		<p><?php esc_html_e( 'No websites found.', 'webring' ); ?></p>
	<?php endif; ?>
</div>
