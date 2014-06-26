<?php
/**
 * @package WordPress
 * @subpackage TimeKeepr
 */
get_header(); 

$terms        = $GLOBALS['gc_session']['t']->getTerms();
$terms_body   = array();
$terms_head[] = array('#', __('Name'), __('Description'), __('Cost'), __('Workers'), __('Count'));
if($terms)
{
	foreach ($terms as $term) 
	{
		$workers = exportWorkers($term->meta['session_workers']);		
		$workers = $workers ? implode(', ', $workers) : '';
			
		$terms_body[] = array($term->term_id, $term->name, $term->description, $term->meta['session_cost'], $workers, $term->count);
	}	
	
	echo generateTable($terms_body, $terms_head);
}
?>
<a href="http://www.gurievcreative.com" class="logo" alt="Guriev Creative"><img src="<?php echo TDU; ?>/images/logo.png" alt="Guriev Creative"></a>

<?php get_footer(); ?>