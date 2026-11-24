<?php
/**
 * Site footer and document close.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

$person = psychwire_assessment_get_person();
?>
	<footer class="site-footer">
		<div class="site-footer__inner">
			<p class="site-footer__copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Psychwire</p>
			<p class="site-footer__role"><?php echo esc_html( $person['job_title'] ); ?></p>
			<a class="back-to-top" href="#top">
				<?php esc_html_e( 'Back to top', 'psychwire-assessment' ); ?>
				<?php echo psychwire_assessment_icon_arrow( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
