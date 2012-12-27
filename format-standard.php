
<?php
if ( has_post_thumbnail() )
{
	?>
	<div class="post-thumbnail-wrapper">
		<?php if( !is_single() ): ?><a href="<?php the_permalink(); ?>" class="thumbnail-link"><?php endif; ?>
			<span class="post-thumbnail" style="background-image: url('<?php
			$image_meta = wp_get_attachment_image_src( get_post_thumbnail_id(), 'big-thumbnail' );
			echo $image_meta[0];
			?>');">
				<h1><?php the_title(); ?></h1>
			</span>
		<?php if( !is_single() ): ?></a><?php endif; ?>
	</div>
	<div class="inset-content-block">
	<?php
}
else
{
	?>
	<div class="inset-content-block">
		<h1>
			<?php if( !is_single() ): ?><a href="<?php the_permalink(); ?>"><?php endif; ?>
				<?php the_title(); ?>
			<?php if( !is_single() ): ?></a><?php endif; ?>
		</h1>
	<?php
}
?>

	<?php get_template_part( 'content-meta' ); ?>

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

</div>  <!-- ./inset-content-block -->
