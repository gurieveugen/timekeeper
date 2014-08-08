<?php
$total_spend_time = 0;
$qo               = get_queried_object();
$project          = new Project($qo->term_id, $GLOBALS['gc_session']['t']);
$timer            = new Timer($_GET, $project);
$user             = $timer->isTotalMode() ? 0 : get_current_user_id();
$cost             = $project->getTotalField(get_current_user_id(), 'cost');
$timer_start      = $timer->getStartTime();


// =========================================================
// CHECK USER ALLOW TO PROJECT
// IF NOT REDIRECT TO PAGE
// ACCESS DENIED
// =========================================================
if(!$project->isAllowWorker()) wp_redirect(
		add_query_arg(
			'action', 
			'accessDeniedProject', 
			TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)
		)
	);

// =========================================================
// if this project not actual
// redirect to home page.
// =========================================================
if($project->isActual() === false) wp_redirect(get_bloginfo('url'));

$total_spend_time   = $project->getTotalField($user, 'time');
$total_elapsed_time = $project->getElapsedTime($total_spend_time);
$total_spend_money  = $project->getTotalField($user, 'money');
$timer_start_big    = strtotime($timer_start)-$total_spend_time;

?>
<?php get_header(); ?>
<script>
	var session_active  = <?php echo intval(Session::getActive()); ?>;
	var timer_start     = '<?php echo $timer_start; ?>';
	var timer_start_big = '<?php echo date('Y-m-d H:i:s', $timer_start_big); ?>';
	var user_cost       = '<?php echo $cost; ?>';
	var money_offset    = '<?php echo $total_spend_money; ?>';
</script>


	<?php		
	$panel_money = $project->wrapPanelMoney(array('big' => $total_spend_money));
	$panel_timer = $project->wrapPanelTimer(array(
		'big' => array(
			'days'    => $total_elapsed_time['day'], 
			'hours'   => $total_elapsed_time['hour'], 
			'minutes' => $total_elapsed_time['minute']))); 
	if($panel_money) echo $panel_money;
	if($panel_timer) echo $panel_timer;
	?>	

<div class="buttons-panel">
	<?php echo $timer->getButton(); ?>
	<a id="switch-panels" class="btn btn-black" href="#"><i class="fa fa-dollar"></i></a>		
	<?php echo $timer->getTotalButton(); ?>
</div>
<?php
// =========================================================
// PRINT SESSIONS TABLE
// =========================================================

$page   = (get_query_var('paged')) ? get_query_var('paged') : 1;
$offset = intval($page-1)*intval(get_option('posts_per_page'));
echo $project->getSessionsTable(array('offset' => $offset));	
echo $project->getSessionPagination();
?>
<?php get_footer(); ?>
