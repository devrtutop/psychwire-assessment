<?php
/**
 * Courses section. Every saved course is printed on the server.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

$attrs   = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();
$courses = psychwire_assessment_get_courses(
	( ! empty( $attrs['courses'] ) && is_array( $attrs['courses'] ) ) ? $attrs['courses'] : null
);
$heading = psychwire_assessment_plain( $attrs['heading'] ?? '', __( 'Courses', 'psychwire-assessment' ) );
$subhead = psychwire_assessment_plain( $attrs['subheading'] ?? '', psychwire_assessment_get_person()['knows_about'] );
$more    = psychwire_assessment_plain( $attrs['moreText'] ?? '', __( 'Load more courses', 'psychwire-assessment' ) );
?>
<section class="courses" id="courses" aria-labelledby="courses-heading">
	<div class="courses__inner">
		<header class="courses__heading">
			<h2 id="courses-heading"><?php echo esc_html( $heading ); ?></h2>
			<p><?php echo esc_html( $subhead ); ?></p>
		</header>
		<ol class="course-list">
			<?php foreach ( $courses as $course ) : ?>
				<li>
					<article class="course-card">
						<p class="course-card__meta">
							<span class="course-card__number"><?php echo esc_html( $course['number'] ); ?></span>
							<span><?php echo esc_html( $course['length'] ); ?></span>
						</p>
						<h3><?php echo esc_html( $course['name'] ); ?></h3>
						<p class="course-card__summary"><?php echo esc_html( $course['description'] ); ?></p>
						<a class="course-card__link" href="<?php echo psychwire_assessment_esc_href( $course['url'] ); ?>"<?php echo psychwire_assessment_external_rel( $course['url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<span><?php echo esc_html( $course['link_label'] ); ?></span>
							<?php echo psychwire_assessment_icon_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</article>
				</li>
			<?php endforeach; ?>
		</ol>
		<p class="courses__more">
			<a class="button button--compact" href="#courses"><?php echo esc_html( $more ); ?></a>
		</p>
	</div>
</section>
