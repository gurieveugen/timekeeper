<?php
/**
 * @package WordPress
 * @subpackage Base_Theme
 */
?>

<?php if ( have_posts() ) : ?>

<div class="posts-holder">
<?php while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
			<div class="entry-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div>
			<?php endif; ?>
	
			<h1><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	
			<div class="entry-meta">
				<?php theme_entry_meta(); ?>
				<?php edit_post_link( __( 'Edit', 'theme' ), '<span class="edit-link">', '</span>' ); ?>
			</div>
		</header>
	
		<div class="entry-content">
			<?php
				if(strpos($post->post_content, '<!--more-->'))
					the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'theme' ) );
				else {
					the_excerpt();
				}
				
				wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'theme' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) );
			?>
		</div>
	
		<footer class="entry-meta">
			<?php if ( comments_open() && ! is_single() ) : ?>
				<div class="comments-link">
					<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a comment', 'theme' ) . '</span>', __( 'One comment so far', 'theme' ), __( 'View all % comments', 'theme' ) ); ?>
				</div>
			<?php endif; ?>
		</footer>
	</article><!-- #post -->

<?php endwhile; ?>
</div> <!-- .posts-holder -->
	
<?php theme_paging_nav(); ?>

<?php else: ?>
	
	<h1 class="page-title"><?php _e( 'Nothing Found', 'theme' ); ?></h1>
	
	<div class="page-content">

		<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'theme' ); ?></p>
		<?php get_search_form(); ?>

	</div><!-- .page-content -->
	
<?php endif; ?>