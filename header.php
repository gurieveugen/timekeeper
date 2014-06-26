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
<body class="background-default">
	<header id="header" class="header">		
			<?php
			$defaults = array(
				'menu'       => 'primary_nav',				
				'container'  => false,
				'walker'     => new GCViewWalker()
			);

			wp_nav_menu( $defaults );
			?>			
			<ul class="pull-right">
				<li class="menu-item menu-item-type-custom menu-item-object-custom" id="menu-item-19"><a href="/wp-login.php?action=logout&amp;_wpnonce=c24e6bbc9d"><i class="fa fa-power-off "></i> <span>Logout</span> </a></li>
			</ul>	
	</header><!-- /header -->
	<section class="main">
	