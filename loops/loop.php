<?php

if ( have_posts() )
{
	while ( have_posts() )
	{
		the_post();
		
		$importance = arke_get_importance();
		$presentation = arke_get_presentation();
		$colspan = arke_get_default_colspan();

		include(locate_template('post.php'));
	}
}
else
{
	get_template_part( 'partials/no-posts-found' );
	
	// Stop executing this template file, but continue outside this file's scope
	return;
}
