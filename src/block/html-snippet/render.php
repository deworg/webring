<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 * @var array    $attributes The block attributes.
 * @var string   $content    The block default content.
 * @var WP_Block $block      The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$webring_name                    = $attributes['webringName'] ?? 'webring';
$show_copy_instructions          = $attributes['showCopyInstructions'] ?? true;
$show_copy_button                = $attributes['showCopyButton'] ?? true;
$show_customization_instructions = $attributes['showCustomizationInstructions'] ?? false;
$enable_syntax_highlighting      = ! empty( $attributes['enableSyntaxHighlighting'] );
$syntax_highlighting_theme       = $attributes['syntaxHighlightingTheme'] ?? 'prism';
$wrap_lines                      = $attributes['wrapLines'] ?? false;

// Unique ID per block instance.
$block_id = wp_unique_id( 'code-block-' );

?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php if ( $show_copy_instructions ) : ?>
	<p><?php echo wp_kses_post( __( 'Copy this snippet for the webring to your website and replace <code>YOUR-DOMAIN.TLD</code> with your own domain name from the webring:', 'webring' ) ); ?></p>
	<?php endif; ?>
	<div class="<?php echo $enable_syntax_highlighting ? 'prism-enabled ' . esc_attr( $syntax_highlighting_theme ) : ''; ?> <?php echo $wrap_lines ? 'prism-wrap-lines' : ''; ?>">
	<pre class="webring-html-snippet"><code id="html-snippet-<?php echo esc_attr( $block_id ) ?>" class="language-markup"><?php
		echo esc_html(
			sprintf(
				"<nav aria-label=\"%s\">\n\t%s\n\t%s\n\t%s\n\t%s\n</nav>",
				esc_html__( 'Webring navigation', 'webring' ),
				sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( get_home_url( '/webring' ) . '/prev/YOUR-DOMAIN.TLD' ),
					sprintf(
						esc_html__( 'Visit the previous website in the %s', 'webring' ),
						$webring_name
					),
					esc_attr_x( '←', 'webring prev link text', 'webring' ),
				),
				sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( get_home_url( '/webring' ) ),
					sprintf(
						esc_html__( 'Visit the %s', 'webring' ),
						$webring_name
					),
					esc_attr( $webring_name ),
				),
				sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( get_home_url( '/webring' ) . '/random/YOUR-DOMAIN.TLD' ),
					sprintf(
						esc_html__( 'Visit a random website from the %s', 'webring' ),
						$webring_name
					),
					esc_attr__( 'random', 'webring' ),
				),
				sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( get_home_url( '/webring' ) . '/next/YOUR-DOMAIN.TLD' ),
					sprintf(
						esc_html__( 'Visit the next website in the %s', 'webring' ),
						$webring_name
					),
					esc_attr_x( '→', 'webring next link text', 'webring' ),
				)
			)
		);
		?></code></pre>
	</div>
	<script>Prism.highlightAll();console.log('highlighted')</script>
	<?php if ( $show_copy_button ) : ?>
	<button
		class="copy-code-button"
		data-target="html-snippet-<?php echo esc_attr( $block_id ); ?>"
		type="button"
	>
		<?php echo esc_html__( 'Copy HTML snippet', 'webring' ); ?>
	</button>
	<?php endif; ?>
	<?php if ( $show_customization_instructions ) : ?>
	<p>
		<?php echo wp_kses_post( __( 'You can also add similar links using the functionality of your website CMS (e.g. using a WordPress navigation menu). Make sure to keep the <code>prev/</code>, <code>next/</code> and <code>random/</code> parts of the URLs.', 'webring' ) ); ?>
	</p>
	<?php endif; ?>
</div>
