<!DOCTYPE html>
<html <?php language_attributes( 'html' ); ?>>

<head <?php hybrid_attr( 'head' ); ?>>
<?php wp_head(); // Hook required for scripts, styles, and other <head> items. ?>
<link rel="alternate" href="https://truong.tech" hreflang="vi-vn">
</head>

<body <?php hybrid_attr( 'body' ); ?>>
	<div id="top-bar">
	<div id="top-container">
	<div id="social"> 
		<?php if(get_theme_mod('blog-start_facebook_link')): ?>
		<a href="<?php echo esc_url (get_theme_mod( 'blog-start_facebook_link' )); ?>" target="_blank"><i class="fa fa-facebook"></i></a>
		<?php endif; ?>

		<?php if(get_theme_mod('blog-start_twitter_link')): ?>
		<a href="<?php echo esc_url (get_theme_mod( 'blog-start_twitter_link' )); ?>" target="_blank"><i class="fa fa-twitter"></i></a>
		<?php endif; ?>

		<?php if(get_theme_mod('blog-start_instagram_link')): ?>
		<a href="<?php echo esc_url (get_theme_mod( 'blog-start_instagram_link' )); ?>" target="_blank"><i class="fa fa-instagram"></i></a>
		<?php endif; ?>

		<?php if(get_theme_mod('blog-start_pinterest_link')): ?>
		<a href="<?php echo esc_url (get_theme_mod( 'blog-start_pinterest_link' )); ?>" target="_blank"><i class="fa fa-pinterest"></i></a>
		<?php endif; ?>

		<?php if(get_theme_mod('blog-start_youtube_link')): ?>
		<a href="<?php echo esc_url (get_theme_mod( 'blog-start_youtube_link' )); ?>" target="_blank"><i class="fa fa-youtube"></i></a>
		<?php endif; ?>

		<?php if(get_theme_mod('blog-start_bloglovin_link')): ?>
		<a href="<?php echo esc_url (get_theme_mod( 'blog-start_bloglovin_link' )); ?>" target="_blank"><i class="fa fa-heart"></i></a>
		<?php endif; ?>
		<?php if(get_theme_mod('blog-start_google_plus_link')): ?>
		<a href="<?php echo esc_url (get_theme_mod( 'blog-start_google_plus_link' )); ?>" target="_blank"><i class="fa fa-google-plus"></i></a>
		<?php endif; ?>

		<?php if(get_theme_mod('blog-start_rss_link')): ?>
		<a href="<?php echo esc_url (get_theme_mod( 'blog-start_rss_link' )); ?>" target="_blank"><i class="fa fa-rss"></i></a>
		<?php endif; ?>

	</div>

	<nav id="menu-secondary"><?php hybrid_get_menu( 'secondary' ); // Loads the menu/secondary.php template. ?></nav>
</div>
</div>
	<div id="container">

		<div class="skip-link">
			<a href="#content" class="screen-reader-text"><?php esc_html_e( 'Skip to content', 'blog-start' ); ?></a>
		</div><!-- .skip-link -->

		

		<header <?php hybrid_attr( 'header' ); ?>>

			<?php if ( display_header_text() ) : // If user chooses to display header text. ?>

				<div <?php hybrid_attr( 'branding' ); ?>>
					<?php hybrid_site_title(); ?>
					<?php hybrid_site_description(); ?>
				</div><!-- #branding -->

			<?php endif; // End check for header text. ?>

				<div id="header-widget"><?php hybrid_get_sidebar( 'subsidiary' ); // Loads the sidebar/subsidiary.php template. ?></div>
		</header><!-- #header -->

		<nav id="main-menu"><?php hybrid_get_menu( 'primary' ); // Loads the menu/primary.php template. ?>
		<div id="searchnavi">
			<?php get_search_form() ?>
		
		</div>	
		</nav>

		<?php if ( get_header_image() && ! display_header_text() ) : // If there's a header image but no header text. ?>

			<a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" rel="home"><img class="header-image" src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></a>

		<?php elseif ( get_header_image() ) : // If there's a header image. ?>

			<img class="header-image" src="<?php header_image(); ?>" width="<?php echo absint( get_custom_header()->width ); ?>" height="<?php echo absint( get_custom_header()->height ); ?>" alt="" />

		<?php endif; // End check for header image. ?>
		<div id="main-containter">
		<div id="main" class="main">

			<?php hybrid_get_menu( 'breadcrumbs' ); // Loads the menu/breadcrumbs.php template. ?>
