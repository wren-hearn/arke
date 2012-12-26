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
add_theme_support( 'custom-background', array( 'default-image' => get_template_directory_uri() . '/img/wild_oliva.png' ) );

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 9999, 200, true );
add_image_size( 'big-thumbnail', 600, 200, true );



// All post formats
add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );


//register_nav_menu( 'top-menu', 'Top Menu' );


// https://github.com/twittem/wp-bootstrap-navwalker
// Register Custom Navigation Walker
require_once('twitter_bootstrap_nav_walker.php');




