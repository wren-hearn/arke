<?php

// Plain old loop, but buffer the output and save it
// to an array along with presentation information.
// After doing a rough measurement and prioritization of posts,
// we'll split the output between a few columns and echo the output
if ( have_posts() )
{
	$cols = array();

	// Get the post count
	global $wp_query;
	$post_count = $wp_query->post_count;

	// Layout of the page (one element per column, value is the grid columns it spans)
	$layout = array( 5, 3, 4 );

	$column_count = count( $layout );
	for ( $i = 0; $i <= $column_count; $i++ )
	{
		$cols[$i] = array();
	}

	$posts_per_column = ceil( $post_count / $column_count );
	$column_index = 0;
	
	while ( have_posts() )
	{
		the_post();

		$importance = arke_get_importance();
		$presentation = arke_get_presentation();

		// Set layout variable for thumbnails
		$colspan = $layout[$column_index];

		// Start buffering
		ob_start();
		?>

		<?php include(locate_template('post.php')); ?>

		<?php
		// Gather the second chunk of the html block
		$post_body = ob_get_contents();
		ob_end_clean();

		// Column housekeeping
		if ( count( $cols[$column_index] ) >= $posts_per_column && $column_index + 1 != $column_count )
		{
			// Increment
			$column_index += 1;
		}

		$cols[$column_index][] = array(
			'html' => $post_body,
			'importance' => $importance,
			'presentation' => $presentation,
			'date' => get_the_date( 'Y-m-d' )
		);
		
	}
}
else
{
	?>
	<div class="row">
		<div class="span12">
			<div class="alert alert-error">
				<strong>Whoops!</strong> No posts found!
			</div>
		</div>
	</div>
	<?
	// Stop executing this template file, but continue outside this file's scope
	return;
}




?>




<div class="row">

	<div class="span12">

		<pre>
Posts Per Column: <?php echo $posts_per_column; ?>
			
Total Before Split: <?php echo $post_count; ?>

Total After Split: <?php echo count($cols[0]) + count($cols[1]) + count($cols[2]); ?>

Elements Recombined: <?php
$agg = $cols[0] + $cols[1] + $cols[2];
foreach( $agg as $k => $v )
	echo $k . ' ' . $v['presentation']['thumbnail'] . ' ';
?>
		</pre>

	</div>

	<div class="span5">
		<?php arke_output_column( $cols[0] ); ?>
	</div>

	<div class="span3">
		<?php arke_output_column( $cols[1] ); ?>
	</div>

	<div class="span4">
		<?php arke_output_column( $cols[2] ); ?>
	</div>

</div>



