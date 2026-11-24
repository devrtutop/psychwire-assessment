<?php
/**
 * Send the saved homepage without loading WordPress.
 *
 * Apache routes anonymous visits here so the response still has the
 * same security headers as a normal page view.
 *
 * @package Psychwire_Assessment
 */

$file = dirname( __DIR__, 2 ) . '/cache/psychwire/index.html';

if ( ! is_readable( $file ) ) {
	http_response_code( 404 );
	header( 'Content-Type: text/plain; charset=UTF-8' );
	echo 'Not found';
	exit;
}

header( 'Content-Type: text/html; charset=UTF-8' );
header( 'X-Content-Type-Options: nosniff' );
header( 'X-Frame-Options: SAMEORIGIN' );
header( 'Referrer-Policy: strict-origin-when-cross-origin' );
header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
header( 'Cache-Control: no-cache, must-revalidate' );

readfile( $file );
