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

		$presentation = get_post_meta( get_the_ID(), '_arke_presentation', true );

		if ( $presentation === '' )
		{
			// Defaults
			$presentation = array(
				'size' => 'normal',
				'thumbnail' => 'yes',
				'excerpt' => 'no'
			);
		}


		// Start buffering
		ob_start();
		?>

		<div id="post-<?php the_ID(); ?>" <?php post_class('full-excerpt'); ?>>
			<?php
			// Gather the first chunk of the html block
			$post_head = ob_get_contents();
			ob_end_clean();

			// Start buffering again
			ob_start();
			
			$post_format = get_post_format();
			if( false === $post_format )
			{
				// Standard post
				if( $presentation['thumbnail'] === 'yes' && has_post_thumbnail() )
					get_template_part( 'format', 'standard-home' );
				else
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
		// Gather the second chunk of the html block
		$post_body = ob_get_contents();
		ob_end_clean();

		$buffered_posts[] = array(
			'html' => '',
			'html_head' => $post_head,
			'html_thumbnail' => '',
			'html_body' => $post_body,
			'presentation' => $presentation,
			'id' => get_the_ID()
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




arke_insert_thumbnail( $col1, '5' );
arke_insert_thumbnail( $col2, '3' );
arke_insert_thumbnail( $col3, '4' );



arke_reassemble_html( $col1 );
arke_reassemble_html( $col2 );
arke_reassemble_html( $col3 );

// Now that we know where to place things, go back and get the correctly-
// sized thumbnails and surrounding 


?>




<div class="row">

	<div class="span12">

		<pre>
Total Before Split: <?php echo count($buffered_posts); ?>

Total After Split: <?php echo count($col1) + count($col2) + count($col1); ?>

Elements Recombined: <?php
$agg = $col1 + $col2 + $col3;
foreach( $agg as $k => $v )
	echo $k . ' ' . $v['presentation']['thumbnail'] . ' ';
?>
		</pre>

	</div>

	<div class="span5">
		<?php arke_output_column( $col1 ); ?>
	</div>

	<div class="span3">
		<?php arke_output_column( $col2 ); ?>
	</div>

	<div class="span4">
		<?php arke_output_column( $col3 ); ?>
	</div>

</div>



