
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php
		$post_format = get_post_format();
		if( false === $post_format )
		{
			// Standard post
			get_template_part( 'format', 'standard' );
		}
		else
		{
			// Load specialized post format
			get_template_part( 'format', $post_format );
		}
		?>

	</div>

<?php endwhile; else: ?>

	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>

