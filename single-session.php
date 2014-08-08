<?php
$msg         = '';
$msg_class   = 'hide';
$session     = new Session($post->ID);
$start_time  = $session->getStartTime();
$stop_time   = $session->getStopTime();
$spend_time  = $session->getSpendTime();
$project     = $session->getProject();
$project_url = get_term_link($project, $project->taxonomy);

// =========================================================
// REDIREECT IF USER NOT AUTHOR THIS SESSION
// =========================================================
if(!$session->isOwner()) 
	wp_redirect(
		add_query_arg(
			'action', 
			'accessDeniedSession', 
			TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)
		)
	);



if(isset($_POST['done']))
{
	$filled             = arrayFill(array('title', 'description', 'start_time', 'stop_time', 'spend_time'), $_POST);
	$post->post_title   = $filled['title'];
	$post->post_content = $filled['description'];	

	if(!isValidDateTime($filled['start_time'])) $filled['start_time'] = $start_time;
	if(!isValidDateTime($filled['stop_time'])) $filled['stop_time']   = $stop_time;	
	
	$session->setStartTime($filled['start_time']);
	$session->setStopTime($filled['stop_time']);
	$session->setSpendTime(intval($filled['spend_time']));
	
	if(wp_update_post($post))
	{		
		wp_redirect($project_url);
	}
}

get_header('form');
?>
<div class="form form-small">
	<form accept-charset="utf-8" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
		<h1><?php _E('Session View'); ?></h1>
		<div class="bs-callout bs-callout-danger <?php echo $msg_class; ?>">
			<h4>Error</h4>
			<p>
				<?php echo $msg; ?>
			</p>
		</div>			
		<input type="text" name="title" required placeholder="<?php _E('Title'); ?>" value="<?php echo $post->post_title; ?>">
		<div class="ctrl-group">			
			<textarea name="description" id="description" cols="30" rows="10" placeholder="<?php _E('Project description'); ?>"><?php echo $post->post_content; ?></textarea>	
		</div>
		<div class="ctrl-group">
			<input type="text" name="start_time" placeholder="<?php _E('Start time'); ?>" value="<?php echo $start_time; ?>">
		</div>
		<div class="ctrl-group">
			<input type="text" name="stop_time" placeholder="<?php _E('Stop time'); ?>" value="<?php echo $stop_time; ?>">
		</div>
		<div class="ctrl-group">
			<input type="text" name="spend_time" placeholder="<?php _E('Spend time'); ?>" value="<?php echo $spend_time; ?>">
		</div>
		<div class="ctrl-group">			
			<button class="btn btn-blue" type="submit" name="done"><?php _E('Done'); ?></button>			
			<a href="<?php echo $project_url; ?>" class="btn btn-gray"><?php _E('Cancel'); ?></a>			
		</div>
	</form>	
</div>
<a href="http://www.gurievcreative.com" class="logo" ><img src="<?php echo TDU; ?>/images/logo.png" alt="Guriev Creative" ></a>
<?php
get_footer();