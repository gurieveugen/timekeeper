<?php
/**
 * Template name: Sign in
 */
?>
<?php
$auth = new Authorization($_POST);
$auth->signIn();
?>
<?php get_header('form'); ?>

<div class="form form-small">
	<form accept-charset="utf-8" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
		<h1><?php _E('Sign in'); ?></h1>
		<div class="bs-callout bs-callout-danger <?php echo $auth->getMsgClass(); ?>">
			<h4>Error</h4>
			<p>
				<?php echo $auth->getMsg(); ?>
			</p>
		</div>		
		<input type="text" name="user_login" required placeholder="Username" value="<?php echo $auth->getUserName(); ?>">
		<div class="ctrl-group">
			<input type="password" name="user_password" required placeholder="Password" <?php echo $auth->getMsgClass() != 'hide' ? 'autofocus' : ''; ?>>	
		</div>
		<div class="ctrl-group">			
			<button class="btn btn-blue" type="submit" name="done"><?php _E('Log in'); ?></button>	
			<a href="<?php echo TimeKeeper::getPageLink(TimeKeeper::PAGE_SIGN_UP); ?>" class="btn btn-default"><?php _E('Sign up'); ?></a>
			<label for="remember" class="pull-right"><input type="checkbox" value="forever" id="remember" name="remember"> <?php _E('Remember Me'); ?></label>			
		</div>
		<div class="clear"></div>		
	</form>	
</div>
<a href="http://www.gurievcreative.com" class="logo" ><img src="<?php echo TDU; ?>/images/logo.png" alt="Guriev Creative" ></a>
<?php get_footer(); ?>
