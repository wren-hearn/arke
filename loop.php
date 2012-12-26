
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

	<?php
	$post_format = get_post_format();
	if( false === $post_format )
	{
		// Generic post
		get_template_part( 'format', 'post' );
	}
	else
	{
		// Load specialized post format
		get_template_part( 'format', $post_format );
	}
	?>

<?php endwhile; else: ?>

	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>

