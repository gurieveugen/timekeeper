<?php
/**
 *
 * @package WordPress
 * @subpackage TimeKeeper
 */
get_header('clear'); 
the_post();
?>
<a  class="logo" href="http://www.gurievcreative.com"><img  src="http://time.loc/wp-content/themes/timekeeper/images/logo.png" alt="Guriev Creative"></a>
<article class="page">
	<h1><?php the_title(); ?></h1>		
	<div class="content">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'theme' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
		<?php edit_post_link( __( 'Edit', 'theme' ), '<span class="edit-link">', '</span>' ); ?>
	</div>
	<div class="comments">
		<?php comments_template(); ?>
	</div>
</article>
<?php get_footer(); ?>
