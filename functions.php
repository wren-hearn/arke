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
$use_compiled_css = false;

// Enable theme design helpers like grid reference, banner placeholders, etc.
$theme_helpers = false;

// Set a maximum width for Oembedded objects
if ( ! isset( $content_width ) )
$content_width = 742;

// Define the default colspan for content displayed in the grid
$default_colspan = 8;



include_once( 'includes/presentation.php' );



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
set_post_thumbnail_size( 780, 200, true ); // default Post Thumbnail dimensions   
add_image_size( '2-col-thumb', 195, 200, true );
add_image_size( '3-col-thumb', 293, 200, true );
add_image_size( '4-col-thumb', 390, 200, true );
add_image_size( '5-col-thumb', 488, 200, true );
add_image_size( '6-col-thumb', 585, 200, true );
add_image_size( '7-col-thumb', 683, 200, true );
add_image_size( '8-col-thumb', 780, 200, true );


// All post formats
add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

// Theme the TinyMCE editor
add_editor_style('css/arke-editor-styles.css');




/* Scripts
-------------------------------------------------- */
if( !is_admin() )
{

	// Deferred Loading of JS
	// https://developers.google.com/speed/docs/best-practices/payload#DeferLoadingJS
	// Priority of 100 pushes this further after any other functions
	add_action( 'wp_footer', 'arke_script_load', 100 );
	function arke_script_load()
	{
		?>
		
		<script type="text/javascript">
			function downloadJSAtOnload() {
				var element = document.createElement("script");
				element.src = "<?php echo get_template_directory_uri(); ?>/js/arke-combined.js";
				document.body.appendChild(element);
			}
			if (window.addEventListener)
				window.addEventListener("load", downloadJSAtOnload, false);
			else if (window.attachEvent)
				window.attachEvent("onload", downloadJSAtOnload);
			else window.onload = downloadJSAtOnload;
		</script>

		<?php
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
