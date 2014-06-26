<?php $sq = get_search_query() ? get_search_query() : 'Search'; ?>
<form action="<?php bloginfo('url'); ?>" method="get" class="search-form">
	<fieldset>
		<input type="text" name="s" value="<?php echo $sq; ?>" class="text" />
		<input class="btn-search" type="submit" value="Search"/>
	</fieldset>
</form>