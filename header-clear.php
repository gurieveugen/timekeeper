<?php
/**
 * @package WordPress
 * @subpackage TimeKeeper
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
	<section class="main">
	