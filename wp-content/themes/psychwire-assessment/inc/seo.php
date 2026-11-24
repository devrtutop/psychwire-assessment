<?php
/**
 * Title, meta description, language, and JSON-LD.
 *
 * Person and Course nodes are built from inc/content.php, the same data the
 * visible page uses.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Mark the public pages as Australian English.
 *
 * @param string $output Existing language attributes.
 */
function psychwire_assessment_language_attributes( string $output ): string {
	if ( is_admin() ) {
		return $output;
	}

	return 'lang="en-AU" dir="ltr"';
}
add_filter( 'language_attributes', 'psychwire_assessment_language_attributes' );

/**
 * Front-page title: "Dr Alex Rivera | Clinical Psychologist and Trainer".
 *
 * @param array<string, string> $parts Document title parts.
 * @return array<string, string>
 */
function psychwire_assessment_document_title_parts( array $parts ): array {
	if ( ! is_front_page() ) {
		return $parts;
	}

	$person = psychwire_assessment_get_person();

	return array(
		'title'   => $person['name'],
		'tagline' => $person['job_title'],
	);
}
add_filter( 'document_title_parts', 'psychwire_assessment_document_title_parts' );

/**
 * Use the separator from the assessment title.
 */
function psychwire_assessment_document_title_separator(): string {
	if ( ! is_front_page() ) {
		return '-';
	}

	return '|';
}
add_filter( 'document_title_separator', 'psychwire_assessment_document_title_separator' );

/**
 * Title for unknown addresses.
 *
 * @param array<string, string> $parts Document title parts.
 * @return array<string, string>
 */
function psychwire_assessment_404_title( array $parts ): array {
	if ( ! is_404() ) {
		return $parts;
	}

	$parts['title'] = __( 'Page not found', 'psychwire-assessment' );

	return $parts;
}
add_filter( 'document_title_parts', 'psychwire_assessment_404_title' );

/**
 * Publish pages only.
 *
 * WordPress writes /robots.txt and /wp-sitemap.xml. Author archives,
 * categories, and blog posts are not part of this site.
 *
 * @param WP_Sitemaps_Provider|false $provider Provider instance.
 * @param string                     $name     Provider name.
 * @return WP_Sitemaps_Provider|false
 */
function psychwire_assessment_sitemap_provider( $provider, string $name ) {
	if ( in_array( $name, array( 'users', 'taxonomies' ), true ) ) {
		return false;
	}

	return $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'psychwire_assessment_sitemap_provider', 10, 2 );

/**
 * Keep the sitemap to pages.
 *
 * @param array<string, WP_Post_Type> $post_types Post types in the sitemap.
 * @return array<string, WP_Post_Type>
 */
function psychwire_assessment_sitemap_post_types( array $post_types ): array {
	unset( $post_types['post'] );

	return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'psychwire_assessment_sitemap_post_types' );

/**
 * Print the meta description and JSON-LD graph.
 */
function psychwire_assessment_seo_head(): void {
	if ( ! is_front_page() ) {
		return;
	}

	$person    = psychwire_assessment_get_person();
	$courses   = psychwire_assessment_get_courses();
	$home      = home_url( '/' );
	$person_id = $home . '#person';
	$image     = psychwire_assessment_asset_uri( 'images/portrait.png' );

	printf(
		'<meta name="description" content="%s" />' . "\n",
		esc_attr( psychwire_assessment_get_meta_description() )
	);

	$course_nodes = array();
	$course_ids   = array();

	foreach ( $courses as $course ) {
		$course_id    = $home . '#course-' . $course['id'];
		$course_ids[] = array( '@id' => $course_id );

		$instance = array(
			'@type'      => 'CourseInstance',
			'courseMode' => 'online',
		);

		if ( '' !== $course['workload'] ) {
			$instance['courseWorkload'] = $course['workload'];
		}

		if ( '' !== $course['repeat'] ) {
			$instance['courseSchedule'] = array(
				'@type'           => 'Schedule',
				'repeatFrequency' => $course['repeat'],
				'duration'        => $course['workload'],
			);
		}

		$course_nodes[] = array(
			'@type'             => 'Course',
			'@id'               => $course_id,
			'name'              => $course['name'],
			'description'       => $course['description'],
			'provider'          => array( '@id' => $person_id ),
			'hasCourseInstance' => $instance,
		);
	}

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array_merge(
			array(
				array(
					'@type'       => 'WebPage',
					'@id'         => $home . '#webpage',
					'url'         => $home,
					'name'        => psychwire_assessment_get_document_title(),
					'description' => psychwire_assessment_get_meta_description(),
					'inLanguage'  => 'en-AU',
					'about'       => array( '@id' => $person_id ),
					'mainEntity'  => array( '@id' => $person_id ),
					'hasPart'     => $course_ids,
				),
				array(
					'@type'       => 'Person',
					'@id'         => $person_id,
					'name'        => $person['name'],
					'jobTitle'    => $person['job_title'],
					'email'       => 'mailto:' . $person['email'],
					'url'         => $home,
					'image'       => $image,
					'description' => $person['bio'],
					'knowsAbout'  => $person['knows_about'],
					'address'     => array(
						'@type'           => 'PostalAddress',
						'addressLocality' => $person['locality'],
						'addressCountry'  => $person['country'],
					),
				),
			),
			$course_nodes
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'psychwire_assessment_seo_head', 1 );
