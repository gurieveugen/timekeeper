<?php
/**
 * @package WordPress
 * @subpackage Base_Theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php echo wp_title( ' ', false, 'right' ) != '' ? wp_title( ' ', false, 'right' ) : get_bloginfo('name'); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );  wp_head(); ?>
</head>
<body <?php body_class("background-default"); ?>>
	<header id="header" class="header">		
			<?php
			$defaults = array(
				'theme_location' => 'primary_nav',				
				'container'      => false,
				'walker'         => new GCViewWalker()
			);
			$defaults_right = array(
				'theme_location' => 'primary_nav_right',
				'menu_class'     => 'pull-right',				
				'container'      => false,
				'walker'         => new GCViewWalker()
			);

			wp_nav_menu($defaults);
			wp_nav_menu($defaults_right);
			
			?>						
	</header><!-- /header -->
	<section class="main">
	