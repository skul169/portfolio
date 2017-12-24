
<header <?php hybrid_attr( 'archive-header' ); ?>>
<?php if ( ! is_paged()) : // Check if we're on page/1. ?>
	<?php if(get_theme_mod('blog-start_home_page_welcome_title')): ?>
		<h1><?php echo esc_html (get_theme_mod( 'blog-start_home_page_welcome_title' )); ?></h1>
		<?php endif; ?>

	<?php if(get_theme_mod('blog-start_home_page_welcome_text')): ?>
		<p><?php echo esc_html (get_theme_mod( 'blog-start_home_page_welcome_text' )); ?></p>
		<?php endif; ?>
<?php endif; ?>
</header>