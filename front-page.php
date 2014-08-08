<?php
/**
 * @package WordPress
 * @subpackage TimeKeepr
 */
$projects = new Projects($GLOBALS['gc_session']['t']);
get_header(); 
$projects->getTable();
$projects->getJoinModal();
$projects->getAllowJoinModal();
$projects->getRemoveProjectModal();

?>
<a href="http://www.gurievcreative.com" class="logo" ><img src="<?php echo TDU; ?>/images/logo.png" alt="Guriev Creative" ></a>

<?php get_footer(); ?>