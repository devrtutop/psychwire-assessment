<?php
/**
 * Front-end behaviour that is not theme support.
 *
 * Theme supports, menus, and the logo live in functions.php.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

/**
 * The core skip link is injected with JavaScript. This theme prints its own
 * skip link in the header so keyboard users reach it with JavaScript off.
 */
function psychwire_assessment_remove_scripted_skip_link(): void {
	remove_action( 'wp_footer', 'the_block_template_skip_link' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_block_template_skip_link' );
}
add_action( 'after_setup_theme', 'psychwire_assessment_remove_scripted_skip_link' );
