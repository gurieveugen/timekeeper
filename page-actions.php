<?php
/**
 * Template name: Actions
 */
$actions = new Actions($_GET);
$error   = $actions->getError();

get_header('clear'); 
extract($error);
?>
<a  class="logo" href="http://www.gurievcreative.com"><img  src="http://time.loc/wp-content/themes/timekeeper/images/logo.png" alt="Guriev Creative"></a>
<article class="page text-center">
	<h1><?php echo $title; ?></h1>		
	<div class="content">
		<?php echo $msg; ?>
	</div>	
</article>
<?php get_footer(); ?>
