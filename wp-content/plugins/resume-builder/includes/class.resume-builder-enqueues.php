<?php
/**
 * Admin Enqueues
 *
 * @package     Resume Builder
 * @subpackage  Enqueues
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
class Resume_Builder_Enqueues {

	function __construct() {
		add_action( 'wp_enqueue_scripts', array(&$this, 'enqueues'), 10, 1 );
        add_action( 'wp_enqueue_scripts', array(&$this, 'css_customized'), 50 );
	}

	public function enqueues( $hook ) {
        wp_enqueue_style( 'rbuilder-fa5', RBUILDER_URL . 'assets/css/fontawesome-all.min.css', array(), '5.0.4' );
		wp_register_style( 'rbuilder-styling', RBUILDER_URL . 'assets/css/style.min.css', array(), RBUILDER_VERSION );
	}

    public function css_customized(){
        $file = RBUILDER_DIR . 'assets/css/customized.php';
        $css = self::get_dynamic_css( $file );
        wp_add_inline_style( 'rbuilder-styling', $css );
        wp_enqueue_style( 'rbuilder-styling' );
    }

    public static function get_dynamic_css( $file = false ){

        if ( !$file || $file && !file_exists($file) )
            return;

        ob_start();
        include( $file );
        $css = ob_get_clean();
        $compressed_css = self::compress_css( $css );

        return $compressed_css;

    }

    public static function compress_css($css){

        // Remove Comments
        $regex = array("`^([\t\s]+)`ism"=>'',"`^\/\*(.+?)\*\/`ism"=>"","`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1","`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n","`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n");
        $css = preg_replace(array_keys($regex),$regex,$css);

        // Remove tabs, spaces, newlines, etc.
        $css = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css );

        return $css;

    }

}
