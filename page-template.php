<?php
/*
 * @package WordPress
 * Template Name: Home Page
*/
?>
<?php get_header(); ?>
<article id="content">
	<?php while (have_posts()) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<h1 class="page-title"><?php the_title(); ?></h1>
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'theme' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'theme' ), '<span class="edit-link">', '</span>' ); ?>
		</footer>
	</article>
	<?php comments_template(); ?>
	<?php endwhile; ?>
</article>
<?php get_sidebar(); ?>
<?php get_footer(); ?>