
<div class="content-meta">
	<p class="author-byline">
		<small>
			<?php the_author_posts_link(); ?>
		</small>
		<small class="muted">
			|
			<?php
			if( !is_single() )
			{
				?><a href="<?php the_permalink(); ?>"><?php the_time('F j, Y'); ?></a><?php
			}
			else
			{
				the_time('F j, Y');
			}
			?>
		</small>
	</p>
</div>
