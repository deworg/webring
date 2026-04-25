<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * @package WebringManager
 *
 *  The following variables are exposed to the file:
 *
 * @var array{
 *   webringName: string|null,
 *   showCopyInstructions: bool|null,
 *   showCopyButton: bool|null,
 *   showCustomizationInstructions: bool|null,
 *   enableSyntaxHighlighting: bool|null,
 *   syntaxHighlightingTheme: string|null,
 *   wrapLines: bool|null
 * } $attributes The block attributes.
 * @var string   $content    The block default content.
 * @var WP_Block $block      The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

$webring_name                    = $attributes['webringName'] ?? 'Webring';
$show_copy_instructions          = $attributes['showCopyInstructions'] ?? true;
$show_copy_button                = $attributes['showCopyButton'] ?? true;
$show_customization_instructions = $attributes['showCustomizationInstructions'] ?? false;
$enable_syntax_highlighting      = ! empty( $attributes['enableSyntaxHighlighting'] );
$syntax_highlighting_theme       = $attributes['syntaxHighlightingTheme'] ?? 'prism';
$wrap_lines                      = $attributes['wrapLines'] ?? false;

// Unique ID per block instance.
$block_id = wp_unique_id( 'code-block-' );

?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $show_copy_instructions ) : ?>
	<p><?php echo wp_kses_post( __( 'Copy this snippet for the webring to your website and replace <code>YOUR-DOMAIN.TLD</code> with your own domain name from the webring:', 'webring-manager' ) ); ?></p>
	<?php endif; ?>
	<div class="<?php echo $enable_syntax_highlighting ? 'prism-enabled ' . esc_attr( $syntax_highlighting_theme ) : ''; ?> <?php echo $wrap_lines ? 'prism-wrap-lines' : ''; ?>">
	<pre class="webring-html-snippet"><code id="html-snippet-<?php echo esc_attr( $block_id ); ?>" class="language-markup"><?php // phpcs:ignore Squiz.PHP.EmbeddedPhp
		echo esc_html(
			sprintf(
				"<nav aria-label=\"%s\">\n\t%s\n\t%s\n\t%s\n\t%s\n</nav>",
				esc_html__( 'Webring navigation', 'webring-manager' ),
				sprintf(
				// translators: %1$s: The webring URL for `prev`, %2$s: The `prev` link text.
					'<a href="%1$s">%2$s</a>',
					esc_url( get_home_url( null, '/webring' ) . '/prev/YOUR-DOMAIN.TLD' ),
					esc_attr_x( 'prev', 'webring prev link text', 'webring-manager' ),
				),
				sprintf(
				// translators: %1$s: The webring URL for `index`, %2$s: The `index` link text.
					'<a href="%1$s">%2$s</a>',
					esc_url( get_home_url( null, '/webring' ) ),
					esc_attr( $webring_name ),
				),
				sprintf(
				// translators: %1$s: The webring URL for `random`, %2$s: The `random` link text.
					'<a href="%1$s">%2$s</a>',
					esc_url( get_home_url( null, '/webring' ) . '/random/YOUR-DOMAIN.TLD' ),
					esc_attr_x( 'random', 'webring random link text', 'webring-manager' ),
				),
				sprintf(
				// translators: %1$s: The webring URL for `next`, %2$s: The `next` link text.
					'<a href="%1$s">%2$s</a>',
					esc_url( get_home_url( null, '/webring' ) . '/next/YOUR-DOMAIN.TLD' ),
					esc_attr_x( 'next', 'webring next link text', 'webring-manager' ),
				)
			)
		);
		// phpcs:ignore Squiz.PHP.EmbeddedPhp ?></code></pre>
	</div>
	<?php if ( $show_copy_button ) : ?>
	<button
		class="copy-code-button"
		data-target="html-snippet-<?php echo esc_attr( $block_id ); ?>"
		type="button"
	>
		<?php echo esc_html__( 'Copy HTML snippet', 'webring-manager' ); ?>
	</button>
	<?php endif; ?>
	<?php if ( $show_customization_instructions ) : ?>
	<p>
		<?php echo wp_kses_post( __( 'You can also add similar links using the functionality of your website CMS (e.g. using a WordPress navigation menu). Make sure to keep the <code>prev/</code>, <code>next/</code> and <code>random/</code> parts of the URLs.', 'webring-manager' ) ); ?>
	</p>
	<?php endif; ?>
</div>
