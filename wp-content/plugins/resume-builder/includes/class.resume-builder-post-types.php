<?php
/**
 * Post Types
 *
 * @package     Resume Builder
 * @subpackage  Post Types
 * @since       2.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Resume_Builder_Post_Types Class
 *
 * This class handles the post type creation.
 *
 * @since 2.0.0
 */
class Resume_Builder_Post_Types {

	function __construct() {

		register_activation_hook( RBUILDER_PLUGIN_FILE, array( &$this, 'activation' ) );
		register_deactivation_hook( RBUILDER_PLUGIN_FILE, array( &$this, 'deactivation' ) );

		add_action('init', array( &$this, 'init' ) );
		add_filter('enter_title_here', array( &$this, 'change_new_resume_title' ) );

		add_action( 'manage_rb_resume_posts_custom_column', array( &$this, 'custom_columns_data' ), 10, 2 );
		add_filter( 'manage_rb_resume_posts_columns', array( &$this, 'custom_columns' ) );

	}

	function custom_columns( $columns ) {
		$new_columns = array();
		foreach( $columns as $key => $val ):
			$new_columns[$key] = $val;
			if ( $key == 'cb' ):
				$new_columns['featured_image'] = '<i class="fas fa-camera"></i>';
			endif;
		endforeach;
		return $new_columns;
	}

	function custom_columns_data( $column, $post_id ) {
	    if ( $column == 'featured_image' ):
	        echo '<span class="rb-admin-resume-list-image">';
	    		echo the_post_thumbnail( 'thumbnail' );
	    	echo '</span>';
	    endif;
	}

	public function activation(){
		self::init();
		flush_rewrite_rules();
	}

	public function deactivation(){
		flush_rewrite_rules();
	}

	public function init() {

		add_image_size('rb-resume-thumbnail', 474, 606, true);

		$resume_slug = ( get_option('resume_builder_resume_slug') ? get_option('resume_builder_resume_slug') : 'resume' );

		register_post_type('rb_resume',
			array(
				'labels' => array(
					'name'               => __( 'Resumes', 'resume-builder' ),
					'singular_name'      => __( 'Resume', 'resume-builder' ),
					'menu_name'          => __( 'Resumes', 'resume-builder' ),
					'name_admin_bar'     => __( 'Resume', 'resume-builder' ),
					'add_new'            => __( 'Add New', 'resume-builder' ),
					'add_new_item'       => __( 'Add New Resume', 'resume-builder' ),
					'new_item'           => __( 'New Resume', 'resume-builder' ),
					'edit_item'          => __( 'Edit Resume', 'resume-builder' ),
					'view_item'          => __( 'View Resume', 'resume-builder' ),
					'all_items'          => __( 'All Resumes', 'resume-builder' ),
					'search_items'       => __( 'Search Resumes', 'resume-builder' ),
					'not_found'          => __( 'No Resumes found.', 'resume-builder' ),
					'not_found_in_trash' => __( 'No Resumes found in Trash.', 'resume-builder' )
				),
				'description' => __('Resumes','resume-builder'),
				'public' => true,
				'show_in_admin_bar' => true,
				'show_in_menu' => 'rb_resumes_menu',
				'has_archive' => true,
				'menu_position' => 25,
				'supports' => array( 'title', 'thumbnail' ),
				'rewrite' => array(
					'with_front' => false,
					'slug' => $resume_slug
				)

			)
		);

	}

	public function change_new_resume_title( $title ) {

		$screen = get_current_screen();
		if  ( 'rb_resume' == $screen->post_type ) {
			$title = __('Name ...','resume-builder');
		}

		return $title;

	}

}
