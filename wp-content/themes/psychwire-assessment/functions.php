<?php
/**
 * Psychwire Assessment theme.
 *
 * This file only loads include files. No functions are defined here.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

$includes = array(
	'inc/class-block-registry.php',
	'inc/content.php',
	'inc/markup.php',
	'inc/frontend.php',
	'inc/performance.php',
	'inc/assets.php',
	'inc/blocks.php',
	'inc/seo.php',
);

foreach ( $includes as $file ) {
	$filepath = get_template_directory() . '/' . $file;

	if ( file_exists( $filepath ) ) {
		require_once $filepath;
	}
}

if ( ! isset( $content_width ) ) {
	$content_width = 1280;
}

/**
 * Basic theme supports, menus, and the block editor.
 */
function psychwire_assessment_setup(): void {
	load_theme_textdomain( 'psychwire-assessment', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_editor_style(
		array(
			'assets/css/normalize.css',
			'assets/css/root.css',
			'assets/css/header.css',
			'assets/css/footer.css',
			'assets/css/general/intro.css',
			'assets/css/general/courses.css',
			'assets/css/general/contact.css',
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 54,
			'width'       => 162,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary', 'psychwire-assessment' ),
			'footer'  => __( 'Footer', 'psychwire-assessment' ),
		)
	);
}
add_action( 'after_setup_theme', 'psychwire_assessment_setup' );

/**
 * Style the Contact item when a Primary menu is assigned.
 *
 * @param array<string, string> $atts  Link attributes.
 * @param WP_Post               $item  Menu item.
 * @param stdClass              $args  Menu arguments.
 * @return array<string, string>
 */
function psychwire_assessment_nav_link_attributes( array $atts, $item, $args ): array {
	if ( ! isset( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $atts;
	}

	$url   = isset( $item->url ) ? (string) $item->url : '';
	$title = isset( $item->title ) ? strtolower( wp_strip_all_tags( (string) $item->title ) ) : '';

	if ( str_contains( $url, '#contact' ) || 'contact' === $title ) {
		$atts['class'] = trim( ( $atts['class'] ?? '' ) . ' button button--light' );
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'psychwire_assessment_nav_link_attributes', 10, 3 );

/**
 * Courses and Contact links used until a Primary menu is assigned.
 */
function psychwire_assessment_primary_fallback(): void {
	?>
	<ul>
		<li>
			<a href="#courses"><?php esc_html_e( 'Courses', 'psychwire-assessment' ); ?></a>
		</li>
		<li>
			<a class="button button--light" href="#contact">
				<?php esc_html_e( 'Contact', 'psychwire-assessment' ); ?>
				<?php echo psychwire_assessment_icon_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</li>
	</ul>
	<?php
}
