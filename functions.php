<?php
// Profiler tag
apply_filters("debug", "Functions start");



/* Temporary Development Stuff
-------------------------------------------------- */
if( !is_admin() )
{
	add_filter('show_admin_bar', '__return_false');
}



/* Theme Globals
-------------------------------------------------- */
global $theme_namespace;
$theme_namespace = 'arke';

global $use_theme_transients;
$use_theme_transients = true;

global $use_theme_object_cache;
$use_theme_object_cache = false;

// Use client-side LESS sheets or use compiled CSS
global $use_compiled_css;
$use_compiled_css = true;

// Enable theme design helpers like grid reference, banner placeholders, etc.
$theme_helpers = false;

// Set a maximum width for Oembedded objects
if ( ! isset( $content_width ) )
$content_width = 742;

// Define the default colspan for content displayed in the grid
$default_colspan = 8;



/* Theme Features Support
-------------------------------------------------- */
// Not very automatic...
add_theme_support( 'automatic-feed-links' );

// Default background
add_theme_support( 'custom-background',
	array(
	'default-image' => get_template_directory_uri() . '/img/gplaypattern-inverted.png'
	)
);

// Default header image
add_theme_support( 'custom-header',
	array(
		'width'         => 1170,
		'height'        => 120,
		'flex-height'    => true
	)
);

// Post thumbnails
add_theme_support( 'post-thumbnails' );

/* Bootstrap with no gutters */
set_post_thumbnail_size( 770, 200, true ); // default Post Thumbnail dimensions   
add_image_size( '2-col-thumb', 195, 200, true );
add_image_size( '3-col-thumb', 293, 200, true );
add_image_size( '4-col-thumb', 390, 200, true );
add_image_size( '5-col-thumb', 488, 200, true );
add_image_size( '6-col-thumb', 585, 200, true );
add_image_size( '7-col-thumb', 683, 200, true );
add_image_size( '8-col-thumb', 780, 200, true );

/* Default Bootstrap grid content */
/*
set_post_thumbnail_size( 770, 200, true ); // default Post Thumbnail dimensions   
add_image_size( '2-col-thumb', 170, 200, true );
add_image_size( '3-col-thumb', 270, 200, true );
add_image_size( '4-col-thumb', 370, 200, true );
add_image_size( '5-col-thumb', 470, 200, true );
add_image_size( '6-col-thumb', 570, 200, true );
add_image_size( '7-col-thumb', 670, 200, true );
add_image_size( '8-col-thumb', 770, 200, true );
*/




// All post formats
add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );



/* Scripts
-------------------------------------------------- */
if( !is_admin() )
{
	add_action('wp_enqueue_scripts', 'arke_script_setup');
	function arke_script_setup()
	{
		wp_enqueue_script(
			'arke-combined',
			get_template_directory_uri() . '/js/arke-combined.js',
			array(),  // no deps
			False,  // no version
			True  // load in footer
		);
	}

	// Form helper scripts
	//add_action('wp_enqueue_scripts', 'arke_form_helper_scripts');
	function arke_form_helper_scripts()
	{
		// No fuss form validation
		// http://parsleyjs.org/
		wp_enqueue_script(
			'arke-combined',
			get_template_directory_uri() . '/js/parsley.min.js',  
			array('jquery'),  // dep
			False,  // no version
			True  // load in footer
		);

		// LocalStorage for form contents
		// http://garlicjs.org/
		wp_enqueue_script(
			'arke-combined',
			get_template_directory_uri() . '/js/garlic.min.js',  
			array('jquery'),  // dep
			False,  // no version
			True  // load in footer
		);
	}
}



/* Menus
-------------------------------------------------- */

// Register the main menu
register_nav_menu( 'top-menu', 'Top Menu' );


// Add a CSS class to parents of submenus
//add_filter( 'wp_nav_menu_objects', 'arke_add_menu_parent_class' );
function arke_add_menu_parent_class( $items )
{
	$parents = array();
	foreach ( $items as $item )
	{
		if ( $item->menu_item_parent && $item->menu_item_parent > 0 )
		{
			$parents[] = $item->menu_item_parent;
		}
	}
	foreach ( $items as $item )
	{
		if ( in_array( $item->ID, $parents ) )
		{
			$item->classes[] = 'menu-parent-item';
		}
	}
	return $items;
}

// Container wrapper for wp_pages_menu to normalize HTML structure with wp_nav_menu
function arke_wp_page_menu()
{
	wp_page_menu();
}



/* Sidebar
-------------------------------------------------- */

add_filter( 'widgets_init', 'arke_register_sidebar' );
function arke_register_sidebar()
{
	global $theme_namespace;
	$args = array(
		'name' => 'Sidebar',
		'id' => $theme_namespace . '_main_sidebar',
		'description' => 'This is the main sidebar.',
	);
	register_sidebar( $args );
}



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

	$presentation = arke_get_post_meta( 'presentataion', $post->ID );
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
						<option <?php selected( $presentation['size'], 'big' ); ?> value="big">Big</option>
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

function arke_column_sort_date( $a, $b )
{
    return strcmp( $b['date'], $a['date'] );
}

function arke_query_sort_importance( $a, $b )
{
    return arke_get_post_meta( 'importance', $a->ID ) < arke_get_post_meta( 'importance', $b->ID );
    
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



/* Miscellaneous Tweaks
-------------------------------------------------- */

// Custom CSS for the login page, Create wp-login.css in your theme folder
add_action('login_head', 'arke_loginCSS');
function arke_loginCSS()
{
	echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/wp-login.css"/>';
}

// Put post thumbnails into rss feed
add_filter('the_excerpt_rss', 'arke_feed_post_thumbnail');
add_filter('the_content_feed', 'arke_feed_post_thumbnail');
function arke_feed_post_thumbnail( $content )
{
	global $post;
	if(has_post_thumbnail($post->ID))
	{
		$content = '<p>' . get_the_post_thumbnail($post->ID) . '</p>' . $content;
	}
	return $content;
}



// Custom walker to get flat lists of catagory and post_tag terms
// to feed into Bootstrap's typeahead attribute for the search box
class Plaintext_Cat_Walker extends Walker_Category
{
	function start_lvl( &$output, $depth = 0, $args = array() ) {}
	function end_lvl( &$output, $depth = 0, $args = array() ) {}
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 )
	{
		$output .= '"' . esc_attr( $category->name );
	}
	function end_el( &$output, $page, $depth = 0, $args = array() ) {
		$output .= '", ';
	}
}


/* Performance Improvements and Monitoring
-------------------------------------------------- */

// Profiler checkpoints
add_action( 'wp_loaded', 'arke_set_checkpoint_wp_loaded' );
function arke_set_checkpoint_wp_loaded()
{
	apply_filters("debug", "WordPress loaded");
}

add_action( 'wp_head', 'arke_set_checkpoint_wp_head' );
function arke_set_checkpoint_wp_head()
{
	apply_filters("debug", "wp_head()");
}

add_action( 'the_post', 'arke_set_checkpoint_the_post' );
function arke_set_checkpoint_the_post( $post )
{
	apply_filters("debug", "Post: " . $post->post_title);
}

// Clean up widget settings that weren't set at installation
// If never used in a sidebar, their lack of default options will
// trigger queries every page load
add_action( 'after_switch_theme', 'arke_set_missing_widget_options' );
function arke_set_missing_widget_options( ){
	add_option( 'widget_pages', array ( '_multiwidget' => 1 ) );
	add_option( 'widget_calendar', array ( '_multiwidget' => 1 ) );
	add_option( 'widget_tag_cloud', array ( '_multiwidget' => 1 ) );
	add_option( 'widget_nav_menu', array ( '_multiwidget' => 1 ) );
}



/* Transient Cache Busts
-------------------------------------------------- */

// Main menu
add_action( 'wp_update_nav_menu', 'arke_bust_menu_cache' );
function arke_bust_menu_cache()
{
	global $theme_namespace;
    delete_transient( $theme_namespace . '_top_menu' );
}

// Tag and cat list for typeahead search
add_action( 'save_post', 'arke_bust_search_terms_cache' );
add_action( 'delete_post', 'arke_bust_search_terms_cache' );
add_action( 'create_category', 'arke_bust_search_terms_cache' );
add_action( 'edit_category', 'arke_bust_search_terms_cache' );
add_action( 'delete_category', 'arke_bust_search_terms_cache' );
function arke_bust_search_terms_cache()
{
	global $theme_namespace;
	delete_transient( $theme_namespace . '_search_terms' );
}

// Homepage Loop
add_action( 'save_post', 'arke_bust_search_terms_cache' );
add_action( 'delete_post', 'arke_bust_search_terms_cache' );
function arke_bust_cache_homepage_loop()
{
	global $theme_namespace;
	delete_transient( $theme_namespace . '_homepage_loop' );
}

// Failsafe cache buster
function arke_bust_all_caches()
{
	global $theme_namespace;
	delete_transient( $theme_namespace . '_top_menu' );
	delete_transient( $theme_namespace . '_search_terms' );
	delete_transient( $theme_namespace . '_homepage_loop' );
}



/* Theme Design Helpers
-------------------------------------------------- */

// Display a reference for the Bootstrap grid
function arke_show_grid_reference()
{
	?>
	<div class="row">
		<div class="span1 grid-preview">1</div>
		<div class="span1 grid-preview">2</div>
		<div class="span1 grid-preview">3</div>
		<div class="span1 grid-preview">4</div>
		<div class="span1 grid-preview">5</div>
		<div class="span1 grid-preview">6</div>
		<div class="span1 grid-preview">7</div>
		<div class="span1 grid-preview">8</div>
		<div class="span1 grid-preview">9</div>
		<div class="span1 grid-preview">10</div>
		<div class="span1 grid-preview">11</div>
		<div class="span1 grid-preview">12</div>
	</div>

	<div class="row">
		<div class="span2 grid-preview">2</div>
		<div class="span3 grid-preview">3</div>
		<div class="span4 grid-preview">4</div>
		<div class="span3 grid-preview">3</div>
	</div>

	<div class="row">
		<div class="span5 grid-preview">5</div>
		<div class="span6 grid-preview">6</div>
		<div class="span1 grid-preview">1</div>
	</div>

	<div class="row">
		<div class="span7 grid-preview">7</div>
		<div class="span5 grid-preview">5</div>
	</div>

	<div class="row">
		<div class="span8 grid-preview">8</div>
		<div class="span4 grid-preview">4</div>
	</div>
	<?php
}






// Profiler tag
apply_filters("debug", "Functions end");
