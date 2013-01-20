<!DOCTYPE html>
<html>
	<head>

		<title><?php is_home() ? bloginfo('name') : wp_title(''); ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/font-awesome.css">

		<link rel="stylesheet/less" type="text/css" href="<?php echo get_template_directory_uri(); ?>/less/bootstrap.less">
		<link rel="stylesheet/less" type="text/css" href="<?php echo get_template_directory_uri(); ?>/less/sapphire.less">

		<script src="<?php echo get_template_directory_uri(); ?>/js/less-1.3.1.min.js" type="text/javascript"></script>

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<div class="container">

			<header>

				<?php
				$custom_header = get_custom_header();
				if ( $custom_header->url != '')
				{
					/*
					?>
					<img src="<?php echo $custom_header->url; ?>" height="<?php echo $custom_header->height; ?>" width="<?php echo $custom_header->width; ?>" alt="" />
					<?php
					*/
				}
				?>
				<div class="site-header"<?php if($custom_header->url != ''): ?> style="background: url('<?php echo $custom_header->url; ?>'); height: <?php echo $custom_header->height; ?>px;"<?php endif; ?>>
					<h2 class="site-header-title"><a href="<?php echo site_url('/'); ?>"><?php bloginfo('name'); ?></a></h2>
					<p class="site-header-desc"><?php bloginfo('description'); ?></p>
				</div>



				<?php

				$args = array(
					'theme_location' => 'top-menu',
					'container' => false,
					'menu_class' => 'top-navigation-menu',
					'fallback_cb' => 'sapphire_no_menu'
				);

				wp_nav_menu($args);

				?>

			</header>
