<!DOCTYPE html>
<html>
	<head>

		<title><?php is_home() ? bloginfo('name') : wp_title(''); ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="screen" />

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<div class="container">

			<header>

				<h2><?php bloginfo('name'); ?></h2>
				<p class="muted"><?php bloginfo('description'); ?></p>

<div class="navbar">

	<div class="navbar-inner">

<?php
    wp_nav_menu( array(
        'menu'       => 'top_menu',
        'depth'      => 2,
        'container'  => false,
        'menu_class' => 'nav',
        //Process nav menu using our custom nav walker
        'walker' => new twitter_bootstrap_nav_walker())
    );
?>




	</div>  <!-- ./navbar-inner -->

</div>  <!-- ./navbar -->

			</header>


