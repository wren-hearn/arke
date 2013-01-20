
<?php get_header(); ?>

<div class="row">

	<div class="span8">

		<p>Front page</p>
	
		<?php get_template_part( 'loop' ); ?>
	</div>

	<div class="sidebar span4">
		<?php get_sidebar(); ?>
	</div>

</div>

<?php get_footer(); ?>
