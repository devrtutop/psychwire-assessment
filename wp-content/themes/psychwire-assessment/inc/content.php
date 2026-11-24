<?php
/**
 * Single source for the assessment copy and the Figma interface labels.
 *
 * Visible course facts and the biography stay verbatim. Link text, the stat
 * card, and the footer line come from the Figma frame.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Saved attributes for a block on the static front page.
 *
 * @return array<string, mixed>
 */
function psychwire_assessment_saved_block_attrs( string $name ): array {
	$page_id = (int) get_option( 'page_on_front' );
	$post    = $page_id ? get_post( $page_id ) : null;

	if ( ! $post instanceof WP_Post ) {
		return array();
	}

	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( ( $block['blockName'] ?? '' ) === 'psychwire-assessment/' . $name ) {
			return is_array( $block['attrs'] ?? null ) ? $block['attrs'] : array();
		}
	}

	return array();
}

/**
 * Person details before any editor changes.
 *
 * @return array{name: string, job_title: string, email: string, bio: string, locality: string, region: string, country: string, knows_about: string, image_alt: string}
 */
function psychwire_assessment_default_person(): array {
	return array(
		'name'        => 'Dr Alex Rivera',
		'job_title'   => 'Clinical Psychologist and Trainer',
		'email'       => 'hello@example.com',
		'bio'         => 'Dr Alex Rivera is a clinical psychologist based in Sydney, Australia. For the past 12 years she has trained psychologists, counsellors and social workers in Acceptance and Commitment Therapy, a practical approach that helps people handle difficult thoughts and feelings and act on what matters to them.',
		'locality'    => 'Sydney',
		'region'      => 'Australia',
		'country'     => 'AU',
		'knows_about' => 'Acceptance and Commitment Therapy',
		'image_alt'   => 'Portrait of a smiling woman, used to represent Dr Alex Rivera.',
	);
}

/**
 * Person details, with Home page edits winning over the defaults.
 *
 * @return array{name: string, job_title: string, email: string, bio: string, locality: string, region: string, country: string, knows_about: string, image_alt: string}
 */
function psychwire_assessment_get_person(): array {
	$person = psychwire_assessment_default_person();

	$saved = psychwire_assessment_saved_block_attrs( 'intro' );
	$map   = array(
		'name'      => 'name',
		'jobTitle'  => 'job_title',
		'bio'       => 'bio',
		'imageAlt'  => 'image_alt',
	);

	foreach ( $map as $attribute => $key ) {
		if ( ! empty( $saved[ $attribute ] ) && is_string( $saved[ $attribute ] ) ) {
			$person[ $key ] = trim( wp_strip_all_tags( $saved[ $attribute ] ) );
		}
	}

	$contact = psychwire_assessment_saved_block_attrs( 'contact' );
	if ( ! empty( $contact['email'] ) && is_string( $contact['email'] ) ) {
		$email = sanitize_email( wp_strip_all_tags( $contact['email'] ) );

		if ( is_email( $email ) ) {
			$person['email'] = $email;
		}
	}

	return $person;
}

/**
 * The assessment courses. `url` is the placeholder from the brief.
 *
 * @return array<int, array{id: string, number: string, name: string, description: string, length: string, url: string, link_label: string, workload: string, repeat: string}>
 */
function psychwire_assessment_default_courses(): array {
	return array(
		array(
			'id'          => 'introduction',
			'number'      => '01',
			'name'        => 'Introduction to Acceptance and Commitment Therapy',
			'description' => 'The core ideas and skills, for practitioners new to the approach.',
			'length'      => '6 hours, online',
			'url'         => '#',
			'link_label'  => 'View introductory course',
			'workload'    => 'PT6H',
			'repeat'      => '',
		),
		array(
			'id'          => 'anxiety',
			'number'      => '02',
			'name'        => 'Acceptance and Commitment Therapy for Anxiety',
			'description' => 'How to apply the approach with clients experiencing anxiety.',
			'length'      => '4 hours, online',
			'url'         => '#',
			'link_label'  => 'View anxiety course',
			'workload'    => 'PT4H',
			'repeat'      => '',
		),
		array(
			'id'          => 'supervision',
			'number'      => '03',
			'name'        => 'Acceptance and Commitment Therapy Supervision',
			'description' => 'Monthly group sessions to discuss cases and build confidence.',
			'length'      => '1.5 hours a month, online',
			'url'         => '#',
			'link_label'  => 'View supervision course',
			'workload'    => 'PT1H30M',
			'repeat'      => 'P1M',
		),
	);
}

/**
 * Course fields the block editor stores. ISO durations stay on the PHP side.
 *
 * @return array<int, array{number: string, name: string, description: string, length: string, url: string, linkLabel: string}>
 */
function psychwire_assessment_courses_for_editor(): array {
	$courses = array();

	foreach ( psychwire_assessment_default_courses() as $course ) {
		$courses[] = array(
			'number'      => $course['number'],
			'name'        => $course['name'],
			'description' => $course['description'],
			'length'      => $course['length'],
			'url'         => $course['url'],
			'linkLabel'   => $course['link_label'],
		);
	}

	return $courses;
}

/**
 * One course, filled from the matching default when a field is empty.
 *
 * Courses added past the original three get their own id and no invented duration.
 *
 * @param array<string, mixed> $course Editor or default course.
 * @return array{id: string, number: string, name: string, description: string, length: string, url: string, link_label: string, workload: string, repeat: string}
 */
function psychwire_assessment_normalize_course( array $course, int $index ): array {
	$defaults = psychwire_assessment_default_courses();
	$fallback = $defaults[ $index ] ?? array(
		'id'          => 'course-' . ( $index + 1 ),
		'number'      => str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ),
		'name'        => '',
		'description' => '',
		'length'      => '',
		'url'         => '#',
		'link_label'  => 'View course',
		'workload'    => '',
		'repeat'      => '',
	);

	$link_label = $course['linkLabel'] ?? ( $course['link_label'] ?? '' );

	return array(
		'id'          => $fallback['id'],
		'number'      => psychwire_assessment_plain( $course['number'] ?? '', $fallback['number'] ),
		'name'        => psychwire_assessment_plain( $course['name'] ?? '', $fallback['name'] ),
		'description' => psychwire_assessment_plain( $course['description'] ?? '', $fallback['description'] ),
		'length'      => psychwire_assessment_plain( $course['length'] ?? '', $fallback['length'] ),
		'url'         => psychwire_assessment_plain( $course['url'] ?? '', $fallback['url'] ),
		'link_label'  => psychwire_assessment_plain( $link_label, $fallback['link_label'] ),
		'workload'    => $fallback['workload'],
		'repeat'      => $fallback['repeat'],
	);
}

/**
 * Courses for the visible page and for JSON-LD.
 *
 * @param array<int, mixed>|null $courses Editor attributes, or null to read the saved Home page.
 * @return array<int, array{id: string, number: string, name: string, description: string, length: string, url: string, link_label: string, workload: string, repeat: string}>
 */
function psychwire_assessment_get_courses( ?array $courses = null ): array {
	if ( null === $courses ) {
		$saved   = psychwire_assessment_saved_block_attrs( 'course-list' );
		$courses = ( ! empty( $saved['courses'] ) && is_array( $saved['courses'] ) ) ? $saved['courses'] : null;
	}

	if ( null === $courses ) {
		return psychwire_assessment_default_courses();
	}

	$normalized = array();

	foreach ( $courses as $index => $course ) {
		if ( is_array( $course ) ) {
			$normalized[] = psychwire_assessment_normalize_course( $course, (int) $index );
		}
	}

	return $normalized ? $normalized : psychwire_assessment_default_courses();
}

/**
 * Block-editor defaults. The biography and courses are defined once above.
 *
 * @return array<string, mixed>
 */
function psychwire_assessment_editor_copy(): array {
	$person = psychwire_assessment_default_person();
	$place  = $person['locality'] . ', ' . $person['region'];

	return array(
		'eyebrow'        => $place,
		'name'           => $person['name'],
		'jobTitle'       => $person['job_title'],
		'bio'            => $person['bio'],
		'buttonText'     => 'Explore courses',
		'statValue'      => '12 years',
		'statLabel'      => 'Training practitioners',
		'imageAlt'       => $person['image_alt'],
		'coursesHeading' => 'Courses',
		'subheading'     => $person['knows_about'],
		'moreText'       => 'Load more courses',
		'courses'        => psychwire_assessment_courses_for_editor(),
		'contactHeading' => 'Contact',
		'location'       => $place,
		'email'          => $person['email'],
	);
}

/**
 * Plain text with a fallback when the editor value is empty.
 */
function psychwire_assessment_plain( $value, string $fallback ): string {
	$text = is_string( $value ) ? trim( wp_strip_all_tags( $value ) ) : '';

	return '' !== $text ? $text : $fallback;
}

/**
 * First sentence of the biography, used as the meta description.
 */
function psychwire_assessment_get_meta_description(): string {
	$bio = psychwire_assessment_get_person()['bio'];
	$end = strpos( $bio, '.' );

	if ( false === $end ) {
		return $bio;
	}

	return substr( $bio, 0, $end + 1 );
}

/**
 * Document title required by the assessment.
 */
function psychwire_assessment_get_document_title(): string {
	$person = psychwire_assessment_get_person();

	return $person['name'] . ' | ' . $person['job_title'];
}
