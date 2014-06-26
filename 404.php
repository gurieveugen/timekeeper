<?php
/**
 *
 * @package WordPress
 * @subpackage Base_Theme
 */
?>
<?php get_header(); ?> 
<div id="content">
	<h1 class="page-title"><?php _e( 'Not found', 'theme' ); ?></h1>
	<div class="page-content">
		<h2><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'theme' ); ?></h2>
		<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'theme' ); ?></p>
		<?php get_search_form(); ?>
	</div>
</div>
<?php get_footer(); ?>