<?php
/**
 * Front page.
 *
 * Header and footer come from the theme root. The sections are the
 * Gutenberg blocks saved on the Home page.
 *
 * @package Psychwire_Assessment
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="content" class="site-main" tabindex="-1">
	<?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	?>
</main>
<?php
get_footer();
