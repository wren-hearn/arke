<?php


/* Post Presentation Meta Box
-------------------------------------------------- */

// Register the meta box
add_action( 'add_meta_boxes', 'arke_presentation_meta_box_setup', 0 );  // 1 to ensure high priority
function arke_presentation_meta_box_setup()
{
	add_meta_box(
		'arke-presentation-meta-box',  // div id for meta box
		'Presentation',  // Title of metabox
		'arke_presentation_meta_box_display',  // HTML display callback
		'post',  // post type meta box applies to
		'side',  // context on the page
		'core'  // priority within the column of meta boxes
	);
}

// Display the HTML for the meta box
function arke_presentation_meta_box_display( $post )
{
	// Use nonce for verification
	wp_nonce_field( 'arke_presentation_meta_box_action', 'arke_presentation_meta_box_nonce' );

	$presentation = arke_get_post_meta( 'presentation', $post->ID );
	$importance = arke_get_post_meta( 'importance', $post->ID );

	?>
	<table class="form-table">
		<tr>
			<td valign="top">Importance</td>
			<td valign="top">
				<fieldset>
					<select name="arke_importance" id="arke_importance">
						<option <?php selected( $importance, 10 ); ?> value="10">SETI</option>
						<option <?php selected( $importance, 9 ); ?> value="9">Burning Bush</option>
						<option <?php selected( $importance, 8 ); ?> value="8">Flare Gun</option>
						<option <?php selected( $importance, 7 ); ?> value="7">Handcuffed Briefcase</option>
						<option <?php selected( $importance, 6 ); ?> value="6">FedEx</option>
						<option <?php selected( $importance, 5 ); ?> value="5">UPS</option>
						<option <?php selected( $importance, 4 ); ?> value="4">Snail Mail</option>
						<option <?php selected( $importance, 3 ); ?> value="3">Post-it</option>
						<option <?php selected( $importance, 2 ); ?> value="2">Windshield Flyer</option>
						<option <?php selected( $importance, 1 ); ?> value="1">Copyright Notice</option>
					</select>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td valign="top">Display Size</td>
			<td valign="top">
				<fieldset>
					<select name="arke_size" id="arke-size">
						<option <?php selected( $presentation['size'], 'normal' ); ?> value="normal">Normal</option>
						<option <?php selected( $presentation['size'], 'compact' ); ?> value="compact">Compact</option>
					</select>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td valign="top">Display Featured Image</td>
			<td valign="top">
				<fieldset>
					<label title='Yes'><input type="radio" name="arke_thumbnail" <?php checked( $presentation['thumbnail'], 'yes' ); ?> value="yes" /> <span>Yes</span></label><br />
					<label title='No'><input type="radio" name="arke_thumbnail" <?php checked( $presentation['thumbnail'], 'no' ); ?> value="no" /> <span>No</span></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td valign="top">Display Excerpt</td>
			<td valign="top">
				<fieldset>
					<label title='Yes'><input type="radio" name="arke_excerpt" <?php checked( $presentation['excerpt'], 'yes' ); ?> value="yes" /> <span>Yes</span></label><br />
					<label title='No'><input type="radio" name="arke_excerpt" <?php checked( $presentation['excerpt'], 'no' ); ?> value="no" /> <span>No</span></label>
				</fieldset>
			</td>
		</tr>
	</table>
	<?php
}

// Save presentation information
add_action( 'save_post', 'arke_presentation_save' );
function arke_presentation_save( $post_id )
{
	// verify if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['arke_presentation_meta_box_nonce'], 'arke_presentation_meta_box_action' ) )
		return;

	// Check permissions
	if ( !current_user_can( 'edit_post', $post_id ) )
		return;

	// OK, we're authenticated: we need to find and save the data
	$importance = '';
	$presentation = array(
		'size' => '',
		'thumbnail' => '',
		'excerpt' => ''
	);

	//sanitize user input
	$importance = intval( sanitize_text_field( $_POST['arke_importance'] ) );
	$presentation['size'] = sanitize_text_field( $_POST['arke_size'] );
	$presentation['thumbnail'] = sanitize_text_field( $_POST['arke_thumbnail'] );
	$presentation['excerpt'] = sanitize_text_field( $_POST['arke_excerpt'] );

	update_post_meta( $post_id, '_arke_importance', $importance );
	update_post_meta( $post_id, '_arke_presentation', $presentation );
}



/* Post Presentation Helper Functions
-------------------------------------------------- */

// This sort operates on the global array of posts ($wp_query->posts)
function arke_query_sort_importance( $a, $b )
{
    return arke_get_post_meta( 'importance', $a->ID ) < arke_get_post_meta( 'importance', $b->ID );
}

// This sort operates on a theme-native 'column' of posts
function arke_column_sort_date( $a, $b )
{
	if ( $b['date'] != $a['date'] )
		return strcmp( $b['date'], $a['date'] );
	else
		return $a['importance'] < $b['importance'];
}

// Loop through a column
function arke_output_column( $col )
{
	// Sanity check
	if ( ! is_array( $col ) )
		return;

	foreach( $col as $buffered_post )
		echo $buffered_post['html'];
}

// Retrieve theme-specific metadata, utilizing the object cache to
// minimize lookups to the database
function arke_get_post_meta( $key, $id = false )
{
	// Metadata keys and defaults
	$defaults = array(
		'importance' => 5,
		'presentation' => array(
			'size' => 'normal',
			'thumbnail' => 'yes',
			'excerpt' => 'yes'
		)
	);

	// Check key against whitelist
	if ( ! in_array( $key, array_keys( $defaults ) ) )
		wp_die( 'arke_get_post_meta() requires a valid metadata key. Options are: ' . implode( ', ', array_keys( $defaults ) ) );
	
	// Must be used in the loop if no post id is passed
	if( in_the_loop() )
	{
		$id = get_the_ID();
	}
	else if ( ! $id )
	{
		// This function has been used incorrectly
		wp_die( 'arke_get_post_meta() requires a post id if not used in the loop.' );
	}

	// If the theme object cache is not enabled or we can't find the
	// metadata in the object cache, go get it
	global $use_theme_object_cache, $theme_namespace;
	if ( ! $use_theme_object_cache || false === ( $meta = wp_cache_get( $theme_namespace . '_post_' . $key . '_' . $id ) ) )
	{
		$meta = get_post_meta( $id, '_' . $theme_namespace . '_' . $key, true );

		// If there's nothing, assign defaults
		if ( $meta === '' )
			$meta = $defaults[$key];

		if ( $use_theme_object_cache )
			wp_cache_set( $theme_namespace . '_post_' . $key . '_' . $id, $meta );
	} 

	return $meta;
}

// Specify the column-span of the main content area for single-item view
function arke_get_default_colspan()
{
	global $default_colspan;
	return $default_colspan;
}
