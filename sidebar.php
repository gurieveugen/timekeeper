<?php
/**
 * @package WordPress
 * @subpackage Base_theme
 */
?>
<?php if ( is_active_sidebar('footer-sidebar')): ?>
<div id="sidebar">
	<?php dynamic_sidebar('footer-sidebar'); ?>
</div>
<?php endif; ?>
