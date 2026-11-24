<?php
/**
 * Small HTML helpers shared by the theme blocks.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme asset URL.
 */
function psychwire_assessment_asset_uri( string $path ): string {
	return get_theme_file_uri( 'assets/' . ltrim( $path, '/' ) );
}

/**
 * Escape a href, including in-page hashes.
 *
 * esc_url() rejects a bare "#" and some "#fragment" values.
 */
function psychwire_assessment_esc_href( string $url ): string {
	if ( '#' === $url || preg_match( '/^#[A-Za-z0-9_-]+$/', $url ) ) {
		return esc_attr( $url );
	}

	return esc_url( $url );
}

/**
 * rel for a link that leaves this site.
 */
function psychwire_assessment_external_rel( string $url ): string {
	$safe = psychwire_assessment_esc_href( $url );

	if ( ! preg_match( '#^https?://#i', $safe ) ) {
		return '';
	}

	$host = wp_parse_url( $safe, PHP_URL_HOST );
	$home = wp_parse_url( home_url(), PHP_URL_HOST );

	if ( is_string( $host ) && is_string( $home ) && 0 === strcasecmp( $host, $home ) ) {
		return '';
	}

	return ' rel="noopener noreferrer"';
}

/**
 * Decorative arrow. Hidden from assistive tech because the link text is enough.
 */
function psychwire_assessment_icon_arrow( string $direction = 'up-right' ): string {
	if ( 'up' === $direction ) {
		return '<svg class="icon" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M8 13V3M8 3L4 7M8 3l4 4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}

	return '<svg class="icon" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M4 12L12 4M7 4h5v5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}
