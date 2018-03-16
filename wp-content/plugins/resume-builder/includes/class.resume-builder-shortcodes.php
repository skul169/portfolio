<?php
/**
 * Resume Builder Shortcodes
 *
 * @package     Resume Builder
 * @subpackage  Shortcodes
 * @since       2.0.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Resume_Builder_Shortcodes {

    function __construct(){

        // Backwards Compatibility
        add_shortcode( 'rb_resume', array($this, 'resume_shortcode') );
        add_shortcode( 'rb_resume_widget_contacts', array($this, 'resume_contact_info') );
        add_shortcode( 'rb_resume_widget_skills', array($this, 'resume_skills') );

        // New Shortcodes
        add_shortcode('rb-resume', array($this, 'resume_shortcode') );

    }

    public function resume_shortcode( $atts, $content = null ){

        global $resume, $resume_section, $resume_style;

        // Shortcode Attributes
        $options = shortcode_atts(
            array(
                'id' => false,
                'section' => false,
                'style' => false
            ), $atts
        );

        // A Resume ID is required.
        if ( !$options['id'] )
            return false;

        $resume_id = esc_html( $options['id'] );
        $resume_section = esc_html( $options['section'] );
        $resume_style = esc_html( $options['style'] );

        $args = array(
            'post_type' => 'rb_resume',
            'post_status' => 'publish',
            'post__in' => array( $resume_id )
        );

        // Display the full resume!
        ob_start();
        $resume = Resume_Builder_Resumes::get( $args );
        if ( !$resume || $resume && empty( $resume ) ):
            return wpautop( '<strong>[rb-resume id="' . $resume_id . '"]</strong><br><em>' . esc_html__( '(resume not found)', 'resume-builder' ) . '</em>' );
        else:
            load_template( RBUILDER_DIR . 'templates/front/resume.php', false );
        endif;

        return ob_get_clean();

    }

    public function resume_contact_info( $atts, $content = null ){

        // Shortcode Attributes
        $options = shortcode_atts(
            array(
                'id' => false
            ), $atts
        );

        // A Resume ID is required.
        if ( !$options['id'] )
            return false;

        ob_start();
        echo do_shortcode( '[rb-resume id="' . esc_attr( $options['id'] ) . '" section="contact"]' );
        return ob_get_clean();

    }

    public function resume_skills( $atts, $content = null ){

        // Shortcode Attributes
        $options = shortcode_atts(
            array(
                'id' => false
            ), $atts
        );

        // A Resume ID is required.
        if ( !$options['id'] )
            return false;

        ob_start();
        echo do_shortcode( '[rb-resume id="' . esc_attr( $options['id'] ) . '" section="skills" style="compact"]' );
        return ob_get_clean();

    }

}
