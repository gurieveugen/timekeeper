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
	</div>
	<div class="entry-meta">
		<?php themeEntryMeta(); ?>
		<?php edit_post_link( 'Edit' , '<span class="edit-link">', '</span>' ); ?>
	</div>
	<div id="nav-below" class="navigation nav-single">
		<span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous Entry: %title', 'theme' ) ); ?></span>
		<span class="nav-next"><?php next_post_link( '%link', __( 'Next Entry: %title <span class="meta-nav">&rarr;</span>', 'theme' ) ); ?></span>
	</div>
	<div class="comments">
		<?php comments_template(); ?>
	</div>
</article>
<?php get_footer(); ?>