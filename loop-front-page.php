<?php

$buffered_posts = array();

// Plain old loop, but buffer the output and save it
// to an array along with presentation information.
// After doing a rough measurement and prioritization of posts,
// we'll split the output between a few columns and echo the output
if ( have_posts() )
{
	while ( have_posts() )
	{
		the_post();

		// Start buffering
		ob_start();
		?>

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

		<?php
		// End buffering
		$post_html = ob_get_contents();
		ob_end_clean();

		$presentation = get_post_meta( get_the_ID(), '_arke_presentation', true );

		if ( $presentation == '' )
		{
			// Defaults
			$presentation = array(
				'size' => 'normal',
				'thumbnail' => 'yes',
				'excerpt' => 'no'
			);
		}

		$buffered_posts[] = array(
			'html' => $post_html,
			'presentation' => $presentation
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



$i = round( count($buffered_posts) / 4 );
$col1 = array_slice( $buffered_posts, 0, $i, true );

$i = round( ( count($buffered_posts) - count($col1) ) / 2 );
$col2 = array_slice( $buffered_posts, count($col1), $i, true );

$col3 = array_slice( $buffered_posts, count($col1) + count($col2), NULL, true );




?>




<div class="row">

	<div class="span12">

		<pre>
Total Before Split: <?php echo count($buffered_posts); ?>

Total After Split: <?php echo count($col1) + count($col2) + count($col1); ?>

Elements Recombined: <?php
$agg = $col1 + $col2 + $col3;
foreach( array_keys( $agg ) as $e )
	echo $e . ' ';
?>
		</pre>

	</div>

	<div class="span4">
		<?php arke_output_column( $col1 ); ?>
	</div>

	<div class="span4">
		<?php arke_output_column( $col2 ); ?>
	</div>

	<div class="span4">
		<?php arke_output_column( $col3 ); ?>
	</div>

</div>



