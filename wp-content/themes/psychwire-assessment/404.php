<?php
/**
 * Unknown addresses.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="content" class="site-main not-found" tabindex="-1">
	<div class="not-found__inner">
		<p class="eyebrow"><?php esc_html_e( '404', 'psychwire-assessment' ); ?></p>
		<h1><?php esc_html_e( 'This page is not available', 'psychwire-assessment' ); ?></h1>
		<p><?php esc_html_e( 'The address may be mistyped, or the page may have been moved.', 'psychwire-assessment' ); ?></p>
		<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Back to the homepage', 'psychwire-assessment' ); ?>
			<?php echo psychwire_assessment_icon_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</div>
</main>
<?php
get_footer();
