<?php
/**
 * Front-end performance.
 *
 * Logged-out page views are served from a static HTML file so Apache does
 * not boot WordPress. Theme CSS is one file. Emoji script is not printed.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Portrait width used on the home page.
 *
 * The photo is about 448px wide on a desktop and about 366px on a phone.
 * This file covers both without sending the 1024px original.
 */
function psychwire_assessment_register_image_sizes(): void {
	add_image_size( 'psychwire-portrait', 480, 0, false );
}
add_action( 'after_setup_theme', 'psychwire_assessment_register_image_sizes' );

/**
 * WordPress emoji detection is unused on this page.
 */
function psychwire_assessment_disable_emoji(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'psychwire_assessment_disable_emoji' );

/**
 * Build one front-end stylesheet and return its URL.
 */
function psychwire_assessment_screen_stylesheet(): string {
	$files = array(
		'css/normalize.css',
		'css/root.css',
		'css/header.css',
		'css/footer.css',
	);

	if ( class_exists( 'BlockRegistry' ) ) {
		$files = array_merge( $files, BlockRegistry::css_relative_paths() );
	}

	$stamp  = 0;
	$chunks = array();

	foreach ( $files as $relative ) {
		$file = get_template_directory() . '/assets/' . $relative;

		if ( ! is_readable( $file ) ) {
			continue;
		}

		$stamp    = max( $stamp, (int) filemtime( $file ) );
		$chunks[] = (string) file_get_contents( $file );
	}

	if ( ! $chunks ) {
		return '';
	}

	$dir  = WP_CONTENT_DIR . '/cache/psychwire';
	$path = $dir . '/screen.css';

	if ( ! is_readable( $path ) || (int) filemtime( $path ) < $stamp ) {
		psychwire_assessment_protect_cache_dir();
		file_put_contents( $path, implode( "\n", $chunks ) );
		psychwire_assessment_flush_page_cache();
	}

	return add_query_arg( 'ver', (string) $stamp, content_url( 'cache/psychwire/screen.css' ) );
}

/**
 * Path of the cached front page.
 */
function psychwire_assessment_page_cache_file(): string {
	return WP_CONTENT_DIR . '/cache/psychwire/index.html';
}

/**
 * Delete the cached front page.
 */
function psychwire_assessment_flush_page_cache(): void {
	$file = psychwire_assessment_page_cache_file();

	if ( is_readable( $file ) ) {
		unlink( $file );
	}
}
add_action( 'save_post', 'psychwire_assessment_flush_page_cache' );
add_action( 'customize_save', 'psychwire_assessment_flush_page_cache' );
add_action( 'switch_theme', 'psychwire_assessment_flush_page_cache' );
add_action( 'wp_update_nav_menu', 'psychwire_assessment_flush_page_cache' );

/**
 * Clear the saved homepage when its address or which page is shown changes.
 *
 * @param string $option Option name.
 */
function psychwire_assessment_flush_page_cache_on_option( string $option ): void {
	$watched = array(
		'home',
		'siteurl',
		'page_on_front',
		'show_on_front',
		'blogname',
		'blogdescription',
	);

	if ( in_array( $option, $watched, true ) ) {
		psychwire_assessment_flush_page_cache();
	}
}
add_action( 'updated_option', 'psychwire_assessment_flush_page_cache_on_option' );

/**
 * Keep the cache directory from listing files or running PHP.
 */
function psychwire_assessment_protect_cache_dir(): void {
	$dir = WP_CONTENT_DIR . '/cache/psychwire';
	wp_mkdir_p( $dir );

	$rules = "Options -Indexes\n<FilesMatch \"\\.php$\">\nRequire all denied\n</FilesMatch>\n";
	$file  = $dir . '/.htaccess';

	if ( ! is_readable( $file ) || file_get_contents( $file ) !== $rules ) {
		file_put_contents( $file, $rules );
	}
}

/**
 * Send the same response headers on a normal page view and on a cache hit.
 */
function psychwire_assessment_security_headers(): void {
	if ( headers_sent() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
}
add_action( 'send_headers', 'psychwire_assessment_security_headers' );

/**
 * Cache the public front page for visitors who are not logged in.
 */
function psychwire_assessment_start_page_cache(): void {
	if ( is_admin() || wp_doing_ajax() || is_user_logged_in() || is_customize_preview() ) {
		return;
	}

	if ( ! is_front_page() || 'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) {
		return;
	}

	if ( ! empty( $_GET ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	ob_start( 'psychwire_assessment_store_page_cache' );
}
add_action( 'template_redirect', 'psychwire_assessment_start_page_cache' );

/**
 * Write a successful front-page response to disk.
 *
 * @param string $html Full HTML document.
 */
function psychwire_assessment_store_page_cache( string $html ): string {
	if ( is_user_logged_in() || is_customize_preview() || ! is_front_page() ) {
		return $html;
	}

	if ( http_response_code() !== 200 || ! str_contains( $html, '</html>' ) || ! str_contains( $html, 'id="content"' ) ) {
		return $html;
	}

	$file = psychwire_assessment_page_cache_file();
	psychwire_assessment_protect_cache_dir();

	$tmp = $file . '.tmp';
	if ( false !== file_put_contents( $tmp, $html ) ) {
		rename( $tmp, $file );
	}

	return $html;
}
