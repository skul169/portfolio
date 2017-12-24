<?php

# Call late so child themes can override.
add_action( 'after_setup_theme', 'blog_start_custom_header_setup', 15 );

/**
 * Adds support for the WordPress 'custom-header' theme feature and registers custom headers.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function blog_start_custom_header_setup() {

	add_theme_support(
		'custom-header',
		array(
			'default-image'          => '',
			'random-default'         => false,
			'width'                  => 1280,
			'height'                 => 400,
			'flex-width'             => true,
			'flex-height'            => true,
			'default-text-color'     => '00b22d',
			'header-text'            => true,
			'uploads'                => true,
			'wp-head-callback'       => 'blog_start_custom_header_wp_head'
		)
	);

	// Registers default headers for the theme.
	//register_default_headers();
}

/**
 * Callback function for outputting the custom header CSS to `wp_head`.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function blog_start_custom_header_wp_head() {

	if ( ! display_header_text() )
		return;

	$hex = get_header_textcolor();

	if ( ! $hex )
		return;

	$style = "body.custom-header #site-title a { color: #{$hex}; }";

	echo "\n" . '<style type="text/css" id="custom-header-css">' . trim( $style ) . '</style>' . "\n";
}
