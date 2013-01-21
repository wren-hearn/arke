<?php

global $theme_namespace;
if ( false === ( $query = get_transient( $theme_namespace . '_homepage_cool_stuff_query' ) ) )
{
	$args = array(
		'category_name' => 'cool-stuff',
		'posts_per_page' => 20
	);
	$query = new WP_Query( $args );
	set_transient( $theme_namespace . '_homepage_cool_stuff_query', $query, MINUTE_IN_SECONDS );
}



?>

<div class="row">

	<div class="span5">

		<?php if ( $query->have_posts() ) : while ( $query->have_posts() ): $query->the_post(); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class('full-excerpt'); ?>>

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

	</div>

</div>



