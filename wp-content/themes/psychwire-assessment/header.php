<?php
/**
 * Document shell and site header.
 *
 * The header markup lives in this file. The logo is the WordPress Site Logo.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preload" href="<?php echo esc_url( get_theme_file_uri( 'assets/fonts/inter-latin-400.woff2' ) ); ?>" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="<?php echo esc_url( get_theme_file_uri( 'assets/fonts/inter-latin-500.woff2' ) ); ?>" as="font" type="font/woff2" crossorigin>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="wp-site-blocks">
	<header class="site-header">
		<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'psychwire-assessment' ); ?></a>
		<div class="site-header__bar" id="top" tabindex="-1">
			<div class="site-logo">
				<?php the_custom_logo(); ?>
			</div>
			<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'psychwire-assessment' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => '',
						'fallback_cb'    => 'psychwire_assessment_primary_fallback',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		</div>
	</header>
