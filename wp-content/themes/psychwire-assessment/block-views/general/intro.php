<?php
/**
 * Introduction and about section.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

$defaults = psychwire_assessment_get_person();
$attrs    = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();
$image_id = isset( $attrs['imageId'] ) ? (int) $attrs['imageId'] : 0;
$photo    = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';

if ( ! $photo && ! empty( $attrs['imageUrl'] ) && is_string( $attrs['imageUrl'] ) ) {
	$photo = $attrs['imageUrl'];
}

if ( ! $photo ) {
	$photo = psychwire_assessment_asset_uri( 'images/portrait.png' );
}

$buttons = array();
if ( ! empty( $attrs['buttons'] ) && is_array( $attrs['buttons'] ) ) {
	foreach ( $attrs['buttons'] as $button ) {
		if ( ! is_array( $button ) ) {
			continue;
		}
		$text = psychwire_assessment_plain( $button['text'] ?? '', '' );
		if ( '' === $text ) {
			continue;
		}
		$buttons[] = array(
			'text' => $text,
			'url'  => psychwire_assessment_plain( $button['url'] ?? '', '#' ),
		);
	}
}

if ( ! $buttons ) {
	$buttons[] = array(
		'text' => psychwire_assessment_plain( $attrs['buttonText'] ?? '', __( 'Explore courses', 'psychwire-assessment' ) ),
		'url'  => '#courses',
	);
}
?>
<section class="intro" aria-labelledby="intro-heading">
	<div class="intro__inner">
		<div class="intro__copy">
			<p class="eyebrow"><?php echo esc_html( psychwire_assessment_plain( $attrs['eyebrow'] ?? '', $defaults['locality'] . ', ' . $defaults['region'] ) ); ?></p>
			<h1 id="intro-heading"><?php echo esc_html( psychwire_assessment_plain( $attrs['name'] ?? '', $defaults['name'] ) ); ?></h1>
			<p class="intro__role"><?php echo esc_html( psychwire_assessment_plain( $attrs['jobTitle'] ?? '', $defaults['job_title'] ) ); ?></p>
			<p class="intro__bio"><?php echo esc_html( psychwire_assessment_plain( $attrs['bio'] ?? '', $defaults['bio'] ) ); ?></p>
			<div class="intro__buttons">
				<?php foreach ( $buttons as $button ) : ?>
					<a class="button" href="<?php echo psychwire_assessment_esc_href( $button['url'] ); ?>"<?php echo psychwire_assessment_external_rel( $button['url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $button['text'] ); ?>
						<?php echo psychwire_assessment_icon_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="intro__media">
			<?php
			$alt = psychwire_assessment_plain( $attrs['imageAlt'] ?? '', $defaults['image_alt'] );
			if ( $image_id ) {
				echo wp_get_attachment_image(
					$image_id,
					'psychwire-portrait',
					false,
					array(
						'class'    => 'intro__photo',
						'alt'      => $alt,
						'sizes'    => '(min-width: 1100px) 448px, calc(100vw - 48px)',
						'decoding' => 'async',
					)
				);
			} else {
				?>
				<img
					class="intro__photo"
					src="<?php echo esc_url( $photo ); ?>"
					width="1200"
					height="1510"
					alt="<?php echo esc_attr( $alt ); ?>"
					decoding="async"
				>
				<?php
			}
			?>
			<p class="intro__stat">
				<strong><?php echo esc_html( psychwire_assessment_plain( $attrs['statValue'] ?? '', __( '12 years', 'psychwire-assessment' ) ) ); ?></strong>
				<span><?php echo esc_html( psychwire_assessment_plain( $attrs['statLabel'] ?? '', __( 'Training practitioners', 'psychwire-assessment' ) ) ); ?></span>
			</p>
		</div>
	</div>
</section>
