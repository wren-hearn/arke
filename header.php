<?php apply_filters("debug", "Header start"); ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        
		<title><?php is_home() ? bloginfo('name') : wp_title(''); ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A WordPress theme based on Bootstrap and tuned for performance.">

		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/bootstrap-custom.min.css">

		<?php global $use_compiled_css; if ( $use_compiled_css ): ?>
			<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/style.css">
		<?php else: ?>
			<link rel="stylesheet/less" type="text/css" href="<?php echo get_template_directory_uri(); ?>/less/arke.less">
			<script src="<?php echo get_template_directory_uri(); ?>/js/less-1.3.1.min.js" type="text/javascript"></script>	
		<?php endif; ?>

		<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico" />
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/img/apple-touch-icon.png" />

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<div class="container">

			<header>

				<?php $custom_header = get_custom_header(); ?>
				<div class="site-header"<?php if($custom_header->url != ''): ?> style="background: url('<?php echo $custom_header->url; ?>'); height: <?php echo $custom_header->height; ?>px;"<?php endif; ?>>
					<h2 class="site-header-title"><a href="<?php echo site_url('/'); ?>"><?php bloginfo('name'); ?></a></h2>
					<p class="site-header-desc"><?php bloginfo('description'); ?></p>
				</div>



				<?php global $arke_display_ads; if ( $arke_display_ads ): ?>
					<div class="banner banner-position-a">
						<div id="div-gpt-ad-1361748545135-1-caption" class="banner-caption">Advertisement</div>
						<div id="div-gpt-ad-1361748545135-1"></div>
					</div>
				<?php endif; ?>

				<?php global $arke_display_ads; if ( $arke_display_ads ): ?>
					<div class="banner banner-position-b">
						<div id="div-gpt-ad-1361748545135-0-caption" class="banner-caption">Advertisement</div>
						<div id="div-gpt-ad-1361748545135-0"></div>
					</div>
				<?php endif; ?>

				<?php global $arke_display_ads; if ( $arke_display_ads ): ?>
					<div class="banner banner-mobile-ad">
						<div id='div-gpt-ad-1361755671674-0-caption' class="banner-caption">Advertisement</div>
						<div id='div-gpt-ad-1361755671674-0'></div>
					</div>
				<?php endif; ?>




				<?php get_search_form(); ?>


				<nav id="site-navigation" class="main-navigation" role="navigation">
					<h3 class="menu-toggle">Menu <i class="icon-reorder"></i></h3>
					<a class="hidden" href="#content" title="Skip to content">Skip to content</a>
					<?php
					/* Main menu, cached with transient
					-------------------------------------------------- */
					global $theme_namespace, $use_theme_transients;
					if ( ! $use_theme_transients || false === ( $menu = get_transient( $theme_namespace . '_top_menu' ) ) )
					{
						$args = array(
							'theme_location' => 'top-menu',
							'menu_class' => 'nav-menu',
							'fallback_cb' => 'arke_wp_page_menu',
							'echo' => 0
						);
						$menu = wp_nav_menu($args);
						if ( $use_theme_transients )
							set_transient( $theme_namespace . '_top_menu', $menu, MINUTE_IN_SECONDS );
					}
					echo $menu;
					?>
				</nav><!-- ./main-navigation -->

			</header>

			<?php global $theme_helpers; if ( $theme_helpers ) arke_show_grid_reference(); ?>

			<?php apply_filters("debug", "Header end"); ?>
