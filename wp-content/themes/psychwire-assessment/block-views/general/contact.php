<?php
/**
 * Contact section.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

$defaults = psychwire_assessment_get_person();
$attrs    = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();
$email = sanitize_email( psychwire_assessment_plain( $attrs['email'] ?? '', $defaults['email'] ) );

if ( ! is_email( $email ) ) {
	$email = sanitize_email( $defaults['email'] );
}
?>
<section class="contact" id="contact" aria-labelledby="contact-heading">
	<div class="contact__inner">
		<div class="contact__copy">
			<h2 id="contact-heading"><?php echo esc_html( psychwire_assessment_plain( $attrs['heading'] ?? '', __( 'Contact', 'psychwire-assessment' ) ) ); ?></h2>
			<p><?php echo esc_html( psychwire_assessment_plain( $attrs['location'] ?? '', $defaults['locality'] . ', ' . $defaults['region'] ) ); ?></p>
		</div>
		<p class="contact__email">
			<a href="<?php echo esc_url( 'mailto:' . $email ); ?>">
				<span><?php echo esc_html( $email ); ?></span>
				<?php echo psychwire_assessment_icon_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</p>
	</div>
</section>
