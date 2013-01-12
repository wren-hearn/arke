<?php

if( !is_admin() )
{
	add_filter('show_admin_bar', '__return_false');

	add_action('wp_enqueue_scripts', 'script_setup');
	function script_setup()
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

add_theme_support( 'automatic-feed-links' );
add_theme_support( 'custom-background', array( 'default-image' => get_template_directory_uri() . '/img/orange-rings-bg.jpg' ) );

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 9999, 200, true );
add_image_size( 'big-thumbnail', 600, 200, true );



// All post formats
add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );


// Register the main menu
register_nav_menu( 'top-menu', 'Top Menu' );

// Add a CSS class to parents of submenus
add_filter( 'wp_nav_menu_objects', 'add_menu_parent_class' );
function add_menu_parent_class( $items )
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


/*
// https://github.com/twittem/wp-bootstrap-navwalker
// Register Custom Navigation Walker
require_once('twitter_bootstrap_nav_walker.php');
	<div class="navbar">
		<div class="navbar-inner">
				wp_nav_menu( array(
					'menu'       => 'top_menu',
					'depth'      => 2,
					'container'  => false,
					'menu_class' => 'nav',
					//Process nav menu using our custom nav walker
					'walker' => new twitter_bootstrap_nav_walker())
				);
		</div>  <!-- ./navbar-inner -->
	</div>  <!-- ./navbar -->
*/

