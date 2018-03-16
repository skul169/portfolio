<?php
/**
 * Admin Enqueues
 *
 * @package     Resume Builder
 * @subpackage  Admin Enqueues
 * @since       2.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Resume_Builder_Post_Types Class
 *
 * This class handles the post type creation.
 *
 * @since 3.0.0
 */
class Resume_Builder_Admin_Enqueues {

	public static $admin_colors;

	function __construct() {

		add_action( 'admin_enqueue_scripts', array(&$this, 'admin_enqueues'), 10, 1 );

	}

	public function admin_enqueues($hook) {

		global $post,$typenow;

		$rbuilder_admin_hooks = array(
			'index.php',
			'post-new.php',
			'post.php',
			'edit.php',
			'resumes_page_rbuilder_settings',
			'resumes_page_rbuilder_welcome'
		);

		// Required Assets for Entire Admin (icons, etc.)
		wp_enqueue_style( 'rbuilder-essentials', RBUILDER_URL . 'assets/admin/css/essentials.min.css', array(), RBUILDER_VERSION );

	    if ( in_array($hook,$rbuilder_admin_hooks) ) {

		    if (function_exists('get_current_screen')):
		    	$screen = get_current_screen();
				if ($hook === 'resumes_page_rbuilder_welcome' || $hook === 'resumes_page_rbuilder_settings' || $hook === 'post-new.php' && $screen->post_type === 'rb_resume' || $hook === 'post.php' && $screen->post_type === 'rb_resume' || $hook === 'edit.php' && $screen->post_type === 'rb_resume' || $hook === 'index.php'):
					add_editor_style( RBUILDER_URL . 'assets/admin/css/editor.min.css' );
					$enqueue = true;
				else:
					$enqueue = false;
				endif;
			else:
				$enqueue = true;
			endif;

			if ($enqueue):

		        $rbuilder_js_vars = array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'time_format' => get_option('time_format','g:ia'),
					'i18n_image_title' => __( 'Add Image', 'resume-builder' ),
	                'i18n_image_button' => __( 'Use this Image', 'resume-builder' ),
					'i18n_edit' => __('Edit','resume-builder'),
					'i18n_close' => __('Close','resume-builder'),
					'i18n_no_intervals' => __('No intervals set.','resume-builder'),
					'i18n_confirm_replace_resume_template' => __('Are you sure you want to replace all of your resume content with this template? This will remove any additional text you may have in the Recipe Layout.','resume-builder')
				);

				// FontAwesome 5
				wp_enqueue_style( 'rbuilder-fa5', RBUILDER_URL . 'assets/css/fontawesome-all.min.css', array(), '5.0.4' );

				// Resume Builder Admin Style Assets
				wp_enqueue_style( 'rbuilder-admin', RBUILDER_URL . 'assets/admin/css/style.min.css', array(), RBUILDER_VERSION );
				wp_enqueue_style( 'wp-color-picker' );

		        // Resume Builder Admin Script Assets
		        wp_enqueue_media();
	            wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-draggable' );
				wp_enqueue_script( 'jquery-ui-sortable' );

				// Resume Builder Admin Script
				wp_register_script('rbuilder-functions', RBUILDER_URL . 'assets/admin/js/rbuilder-functions.min.js', array('jquery'), RBUILDER_VERSION );
				wp_localize_script('rbuilder-functions', 'rbuilder_js_vars', $rbuilder_js_vars );
				wp_enqueue_script('rbuilder-functions');

			endif;

	    }

	}

}
