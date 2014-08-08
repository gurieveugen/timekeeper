<?php
/**
 * Template name: Sign up
 */
?>
<?php
$auth = new Authorization($_POST);
$auth->signUp();
?>
<?php get_header('form'); ?>

<div class="form form-small">
	<form accept-charset="utf-8" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
		<h1><?php _E('Sign up'); ?></h1>
		<div class="bs-callout bs-callout-danger <?php echo $auth->getMsgClass(); ?>">
			<h4>Error</h4>
			<p>
				<?php echo $auth->getMsg(); ?>
			</p>
		</div>		
		<input type="text" name="user_login" required placeholder="<?php _E('Username'); ?>">
		<div class="ctrl-group">
			<input type="email" name="user_email" required placeholder="<?php _E('Email'); ?>">
		</div>
		<div class="ctrl-group">
			<input type="password" name="user_password" required placeholder="<?php _E('Password'); ?>">	
		</div>		
		<div class="ctrl-group">			
			<button class="btn btn-blue" type="submit" name="done"><?php _E('Register'); ?></button>	
			<a href="<?php echo TimeKeeper::getPageLink(TimeKeeper::PAGE_SIGN_IN); ?>" class="btn btn-default"><?php _E('Sign in'); ?></a>			
		</div>
		<div class="clear"></div>		
	</form>	
</div>
<a href="http://www.gurievcreative.com" class="logo" ><img src="<?php echo TDU; ?>/images/logo.png" alt="Guriev Creative" ></a>
<?php get_footer(); ?>
