<?php
/**
 * Block registration.
 *
 * Add one BlockRegistry::add() call per section. These blocks are inserted
 * on the Home page. Header and footer live in header.php and footer.php.
 *
 * Copy defaults come from inc/content.php so the editor, the page, and
 * the JSON-LD share one source.
 *
 * Template: block-views/{template}
 * CSS:      assets/css/{file}
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

$copy = psychwire_assessment_editor_copy();

BlockRegistry::add(
	array(
		'name'        => 'intro',
		'title'       => 'Introduction',
		'description' => 'Name, title, biography, portrait, and training stat.',
		'template'    => 'general/intro.php',
		'css'         => array( 'general/intro.css' ),
		'inserter'    => true,
		'attributes'  => array(
			'eyebrow'    => array( 'type' => 'string', 'default' => $copy['eyebrow'] ),
			'name'       => array( 'type' => 'string', 'default' => $copy['name'] ),
			'jobTitle'   => array( 'type' => 'string', 'default' => $copy['jobTitle'] ),
			'bio'        => array( 'type' => 'string', 'default' => $copy['bio'] ),
			'buttonText' => array( 'type' => 'string', 'default' => $copy['buttonText'] ),
			'buttons'    => array(
				'type'    => 'array',
				'default' => array(
					array(
						'text' => $copy['buttonText'],
						'url'  => '#courses',
					),
				),
			),
			'imageId'    => array( 'type' => 'number', 'default' => 0 ),
			'imageUrl'   => array( 'type' => 'string', 'default' => '' ),
			'statValue'  => array( 'type' => 'string', 'default' => $copy['statValue'] ),
			'statLabel'  => array( 'type' => 'string', 'default' => $copy['statLabel'] ),
			'imageAlt'   => array( 'type' => 'string', 'default' => $copy['imageAlt'] ),
		),
	)
);

BlockRegistry::add(
	array(
		'name'        => 'course-list',
		'title'       => 'Course list',
		'description' => 'Courses, rendered on the server.',
		'template'    => 'general/course-list.php',
		'css'         => array( 'general/courses.css' ),
		'inserter'    => true,
		'attributes'  => array(
			'heading'    => array( 'type' => 'string', 'default' => $copy['coursesHeading'] ),
			'subheading' => array( 'type' => 'string', 'default' => $copy['subheading'] ),
			'moreText'   => array( 'type' => 'string', 'default' => $copy['moreText'] ),
			'courses'    => array(
				'type'    => 'array',
				'default' => $copy['courses'],
			),
		),
	)
);

BlockRegistry::add(
	array(
		'name'        => 'contact',
		'title'       => 'Contact',
		'description' => 'Contact heading and email.',
		'template'    => 'general/contact.php',
		'css'         => array( 'general/contact.css' ),
		'inserter'    => true,
		'attributes'  => array(
			'heading'  => array( 'type' => 'string', 'default' => $copy['contactHeading'] ),
			'location' => array( 'type' => 'string', 'default' => $copy['location'] ),
			'email'    => array( 'type' => 'string', 'default' => $copy['email'] ),
		),
	)
);

BlockRegistry::boot();
