<?php
/**
 * Front-end styles.
 *
 * Block CSS and JS are enqueued from inc/blocks.php through BlockRegistry.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load the shared theme stylesheet. Block themes do not enqueue style.css on their own.
 */
function psychwire_assessment_enqueue_styles(): void {
	$theme   = wp_get_theme();
	$version = $theme->get( 'Version' );
	$screen  = psychwire_assessment_screen_stylesheet();

	if ( $screen ) {
		wp_enqueue_style( 'psychwire-assessment-screen', $screen, array(), null );
	} else {
		$base = get_template_directory_uri() . '/assets/css/';
		wp_enqueue_style( 'psychwire-assessment-normalize', $base . 'normalize.css', array(), $version );
		wp_enqueue_style( 'psychwire-assessment-root', $base . 'root.css', array( 'psychwire-assessment-normalize' ), $version );
		wp_enqueue_style( 'psychwire-assessment-header', $base . 'header.css', array( 'psychwire-assessment-root' ), $version );
		wp_enqueue_style( 'psychwire-assessment-footer', $base . 'footer.css', array( 'psychwire-assessment-root' ), $version );
	}

	wp_enqueue_script(
		'psychwire-assessment-back-to-top',
		get_theme_file_uri( 'assets/js/general/back-to-top.js' ),
		array(),
		$version,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'psychwire_assessment_enqueue_styles' );

/**
 * Styles for the 404 template only.
 */
function psychwire_assessment_enqueue_404_styles(): void {
	if ( ! is_404() ) {
		return;
	}

	$path = get_template_directory() . '/assets/css/404.css';

	wp_enqueue_style(
		'psychwire-assessment-404',
		get_theme_file_uri( 'assets/css/404.css' ),
		array( 'psychwire-assessment-screen' ),
		(string) filemtime( $path )
	);
}
add_action( 'wp_enqueue_scripts', 'psychwire_assessment_enqueue_404_styles' );
