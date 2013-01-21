<?php
// Profiler tag
apply_filters("debug", "Functions start");



/* Temporary Development Stuff
-------------------------------------------------- */
if( !is_admin() )
{
	add_filter('show_admin_bar', '__return_false');
}



// Try and find a way to disable the default query on pages where we're
// we're doing it all manually
/*
//add_filter( 'query_vars', 'disable_default_query' , 10, 1 );
function disable_default_query( $query_vars )
{
	echo '<pre>';
	print_r( $query_vars );
	echo '</pre>';
	return $query_vars;
}


function _cancel_query( $query ) {
	if ( !is_admin() && !$query->is_feed() && $query->is_home() && $query->is_main_query() )
	{
		$query->init();
		//$query->query_vars['posts_per_page'] = 0;
		//echo '<pre>';
		//print_r( $query );
		//echo '</pre>';
	}
}
add_action( 'pre_get_posts', '_cancel_query' );
*/


function pw_filter_query( $query ) {

//echo '<pre>';
//print_r( $query );
//echo '</pre>';
	
	if( $query->is_main_query() ) {
		//echo "executed ";
		//$query->init();
		$query->set('meta_key', 'nonsense_meta_key');
		$query->set('meta_value', 'cellar_door');
		//$query->set( 'posts_per_page', 0 );
	}
}
//add_action('pre_get_posts', 'pw_filter_query', 9999);






/* Theme Globals
-------------------------------------------------- */
global $theme_namespace;
$theme_namespace = 'sapphire';

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
	add_action('wp_enqueue_scripts', 'sapphire_script_setup');
	function sapphire_script_setup()
	{
		wp_enqueue_script(
			'sapphire',
			get_template_directory_uri() . '/js/combined.js',
			array(),  // no deps
			False,  // no version
			True  // load in footer
		);
	}
}



/* Menus
-------------------------------------------------- */

// Register the main menu
register_nav_menu( 'top-menu', 'Top Menu' );

function sapphire_no_menu( $args )
{
	?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>No menus found!</strong> - Use <em>Appearance > Menus</em> to create a menu and assign it to this location.
		</div>
	<?php
}

// Add a CSS class to parents of submenus
add_filter( 'wp_nav_menu_objects', 'sapphire_add_menu_parent_class' );
function sapphire_add_menu_parent_class( $items )
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



/* Sidebar
-------------------------------------------------- */

add_filter( 'widgets_init', 'sapphire_register_sidebar' );
function sapphire_register_sidebar()
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


/* Performance Improvements and Monitoring
-------------------------------------------------- */

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

add_action( 'wp_update_nav_menu', 'sapphire_bust_menu_cache' );
function sapphire_bust_menu_cache()
{
	global $theme_namespace;
    delete_transient( $theme_namespace . '_top_menu' );
}




// Profiler tag
apply_filters("debug", "Functions end");
