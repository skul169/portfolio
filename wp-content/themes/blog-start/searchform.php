<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="text" placeholder="<?php esc_attr_e('To search type and hit enter...', 'blog-start')?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" id="s" />
</form>