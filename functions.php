<?php
// Profiler tag
apply_filters("debug", "Functions start");



/* Temporary Development Stuff
-------------------------------------------------- */
if( !is_admin() )
{
	add_filter('show_admin_bar', '__return_false');
}

// Remove unneeded widgets that have undesirable query overhead
add_action( 'widgets_init', 'remove_unneeded_widgets' );
function remove_unneeded_widgets() {
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WP_Nav_Menu_Widget');
	/*
		WP_Widget_Pages                   = Pages Widget
		WP_Widget_Calendar                = Calendar Widget
		WP_Widget_Archives                = Archives Widget
		WP_Widget_Links                   = Links Widget
		WP_Widget_Meta                    = Meta Widget
		WP_Widget_Search                  = Search Widget
		WP_Widget_Text                    = Text Widget
		WP_Widget_Categories              = Categories Widget
		WP_Widget_Recent_Posts            = Recent Posts Widget
		WP_Widget_Recent_Comments         = Recent Comments Widget
		WP_Widget_RSS                     = RSS Widget
		WP_Widget_Tag_Cloud               = Tag Cloud Widget
		WP_Nav_Menu_Widget                = Menus Widget
	*/
}




/* Theme Globals
-------------------------------------------------- */
global $theme_namespace;
$theme_namespace = 'arke';

// Use client-side LESS sheets or use compiled CSS
global $use_compiled_css;
$use_compiled_css = true;

// Set a maximum width for Oembedded objects
if ( ! isset( $content_width ) )
$content_width = 742;



/* Theme Features Support
-------------------------------------------------- */
// Not very automatic...
add_theme_support( 'automatic-feed-links' );

// Default background
add_theme_support( 'custom-background',
	array(
	'default-image' => get_template_directory_uri() . '/img/orange-rings-bg.jpg'
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
set_post_thumbnail_size( 9999, 200, true );
add_image_size( 'big-thumbnail', 770, 200, true );

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

	$presentation = get_post_meta( $post->ID, '_arke_presentation', true );

	if ( $presentation == '' )
	{
		// Defaults
		$presentation = array(
			'size' => 'normal',
			'thumbnail' => 'yes',
			'excerpt' => 'no'
		);
	}
	?>
	<table class="form-table">
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
	$presentation = array(
		'size' => '',
		'thumbnail' => '',
		'excerpt' => ''
	);
		
	//sanitize user input
	$presentation['size'] = sanitize_text_field( $_POST['arke_size'] );
	$presentation['thumbnail'] = sanitize_text_field( $_POST['arke_thumbnail'] );
	$presentation['excerpt'] = sanitize_text_field( $_POST['arke_excerpt'] );

	update_post_meta( $post_id, '_arke_presentation', $presentation );
}



/* Post Presentation Helper Functions
-------------------------------------------------- */

function arke_output_column( $col )
{
	// Sanity check
	if ( ! is_array( $col ) )
		return;

	foreach( $col as $buffered_post )
		echo $buffered_post['html'];
}



/* Miscellaneous Tweaks
-------------------------------------------------- */

// Custom CSS for the login page, Create wp-login.css in your theme folder
add_action('login_head', 'wpfme_loginCSS');
function wpfme_loginCSS()
{
	echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/wp-login.css"/>';
}

// Put post thumbnails into rss feed
add_filter('the_excerpt_rss', 'wpfme_feed_post_thumbnail');
add_filter('the_content_feed', 'wpfme_feed_post_thumbnail');
function wpfme_feed_post_thumbnail( $content )
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
add_action( 'wp_loaded', 'set_checkpoint_wp_loaded' );
function set_checkpoint_wp_loaded()
{
	apply_filters("debug", "WordPress loaded");
}

add_action( 'wp_head', 'set_checkpoint_wp_head' );
function set_checkpoint_wp_head()
{
	apply_filters("debug", "wp_head()");
}

add_action( 'the_post', 'set_checkpoint_the_post' );
function set_checkpoint_the_post( $post )
{
	apply_filters("debug", "Post: " . $post->post_title);
}



/* Transient Cache Busts
-------------------------------------------------- */

add_action( 'wp_update_nav_menu', 'arke_bust_menu_cache' );
function arke_bust_menu_cache()
{
	global $theme_namespace;
    delete_transient( $theme_namespace . '_top_menu' );
}

//delete_transient( $theme_namespace . '_top_menu' );
//delete_transient( $theme_namespace . '_search_terms' );


// Profiler tag
apply_filters("debug", "Functions end");
