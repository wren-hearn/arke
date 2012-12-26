
<h1>
	<?php
	if( !is_single() )
	{
		?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php
	}
	else
	{
		the_title();
	}
	?>
</h1>

<div class="content-meta">
	<p class="author-byline">
		<small>
			<?php the_author_posts_link(); ?>
		</small>
		<small class="muted">
			| <?php the_time('F j, Y') ?>
		</small>
	</p>
</div>

<?php
if( is_single() )
{
	the_content();
	?>

	<div class="content-footer">
		<p>
			<small>
				Posted in: <?php the_category(' | '); ?>
			</small>
		</p>
		<p>
			<small>
				Tagged as: <?php the_tags(' | '); ?>
			</small>
		</p>
	</div>

	<?php
}
else
{
	the_excerpt();
}
?>
