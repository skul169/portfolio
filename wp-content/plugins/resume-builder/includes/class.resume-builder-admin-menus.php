<?php
/**
 * Admin Menus
 *
 * @package     Resume Builder
 * @subpackage  Admin Menus
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
class Resume_Builder_Admin_Menus {

	function __construct() {

		add_action('admin_menu', array(&$this, 'add_menu'));

	}

	public function add_menu() {

		add_menu_page( esc_html__( 'Resumes', 'resume-builder' ), esc_html__( 'Resumes', 'resume-builder' ), 'edit_posts', 'rb_resumes_menu', '', 'none', 58 );
		add_submenu_page('rb_resumes_menu', esc_html__('Add New','resume-builder'), esc_html__('Add New','resume-builder'), 'edit_posts', 'post-new.php?post_type=rb_resume', '' );
		add_submenu_page('rb_resumes_menu', esc_html__('Settings','resume-builder'), esc_html__('Settings','resume-builder'), 'administrator', 'rbuilder_settings', array(&$this, 'rbuilder_settings_page') );
		add_submenu_page('rb_resumes_menu', esc_html__("What's New?", "resume-builder"), esc_html__("What's New?", "resume-builder"), 'administrator', 'rbuilder_welcome', array(&$this, 'rbuilder_welcome_content') );

	}

	// Settings Panel
	public function rbuilder_settings_page() {
		if(!current_user_can('administrator')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'resume-builder'));
		}
		include( RBUILDER_DIR . 'templates/admin/settings.php' );
	}

	// What's New?
	public function rbuilder_welcome_content() {
		if(!current_user_can('administrator')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'resume-builder'));
		}
		include( RBUILDER_DIR . 'templates/admin/welcome.php' );
	}

}
