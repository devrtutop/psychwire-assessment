<?php
/**
 * Default page template.
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
