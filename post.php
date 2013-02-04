
<div id="post-<?php the_ID(); ?>" <?php post_class('full-excerpt'); ?>>

	<?php
	$post_format = get_post_format();
	if( false === $post_format )
		$post_format = 'standard';

	// Load specialized post format
	include(locate_template('format-' . $post_format . '.php'));
	?>

</div>
