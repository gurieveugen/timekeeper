<?php
/**
 * Template name: My projects
 */
get_header(); 

$projects       = new Projects($GLOBALS['gc_session']['t']);
$owner_projects = $projects->getOwnerProject();
$projects->getTable($owner_projects);

?>
<a href="http://www.gurievcreative.com" class="logo" ><img src="<?php echo TDU; ?>/images/logo.png" alt="Guriev Creative" ></a>

<?php get_footer(); ?>