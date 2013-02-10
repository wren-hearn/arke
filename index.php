
<?php get_header(); ?>

<div class="row">

	<div class="span8">
		<?php get_template_part( 'loops/loop' ); ?>
	</div>

	<div class="sidebar span4">
		<?php get_sidebar(); ?>
	</div>

</div>

<?php get_footer(); ?>
