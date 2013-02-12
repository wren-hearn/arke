<?php

// Sanity Check
if ( ! have_posts() )
{
	get_template_part( 'partials/no-posts-found' );

	// Stop executing this template file, but continue outside this file's scope
	return;
}


global $theme_namespace, $use_theme_transients;
if ( ! $use_theme_transients || false === ( $rendered_loop = get_transient( $theme_namespace . '_homepage_loop' ) ) )
{


	/* Preprocessing
	-------------------------------------------------- */

	// Plain old loop, but buffer the output and save it
	// to an array along with presentation information.


	$cols = array();

	// Get the post count
	global $wp_query;
	$post_count = $wp_query->post_count;

	// Layout of the page (one element per column, value is the grid columns it spans)
	$layout = array( 5, 3, 4 );

	// Initialize the column array
	$column_count = count( $layout );
	for ( $i = 0; $i <= $column_count; $i++ )
	{
		$cols[$i] = array();
	}

	// Indexing and splitting variables
	$posts_per_column = ceil( $post_count / $column_count );
	$column_index = 0;

	// Reorder the query
	global $wp_query;
	usort( $wp_query->posts, 'arke_query_sort_importance' );


	while ( have_posts() )
	{
		the_post();

		$importance = arke_get_importance();
		$presentation = arke_get_presentation();

		// Set layout variable for thumbnails
		$colspan = $layout[$column_index];

		// Start buffering
		ob_start();

		include( locate_template( 'post.php' ) );
		
		// Gather the second chunk of the html block
		$post_body = ob_get_contents();
		ob_end_clean();

		// Begin filling a new column if this one has all it should hold
		// Ignore this check when on the last column
		if ( count( $cols[$column_index] ) >= $posts_per_column && $column_index + 1 != $column_count )
		{
			// Increment
			$column_index += 1;
		}

		// Add the post array to the columns array
		$cols[$column_index][] = array(
			'html' => $post_body,
			'importance' => $importance,
			'presentation' => $presentation,
			'date' => get_the_date( 'Y-m-d' )
		);
	}


	// Sort each column by date (not absolute time)
	foreach( $cols as &$col )
		usort( $col, 'arke_column_sort_date' );

	// Start buffering for full loop output
	ob_start();


	/* Rendering
	-------------------------------------------------- */

	// Diagnostics
	global $theme_helpers;
	if ( $theme_helpers )
	{
		?>
		<div class="row">
			<div class="span12">
				<pre>
	Posts Per Column: <?php echo $posts_per_column; ?>
				
	Total Before Split: <?php echo $post_count; ?>

	Total After Split: <?php echo count($cols[0]) + count($cols[1]) + count($cols[2]); ?>

	Elements Recombined: <?php
	$agg = array_merge( $cols[0], $cols[1], $cols[2] );
	foreach( $agg as $k => $v )
		echo $k . ' ' . $v['presentation']['thumbnail'] . ' ';
	?>
				</pre>
			</div>
		</div>
		<?php
	}
	?>

	<div id="colhead-wrapper">
		<div id="colhead" class="row">
			<div class="span5 colhead-head colhead-top-story"><h4>Top Stories</h4></div>
			<div class="span7 colhead-head colhead-other-stories"><h4>Everything Else</h4></div>
		</div>
	</div>

	<div class="row">

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

	<?php

	$rendered_loop = ob_get_contents();
	ob_end_clean();

	if ( $use_theme_transients )
		set_transient( $theme_namespace . '_homepage_loop', $rendered_loop, MINUTE_IN_SECONDS );
}

echo $rendered_loop;
