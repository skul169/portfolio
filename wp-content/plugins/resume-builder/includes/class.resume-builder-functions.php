<?php
/**
 * Misc Functions
 *
 * @package     Resume Builder
 * @subpackage  Misc Functions
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Resume_Builder_Functions {

    public static function parse_readme_changelog( $readme_url = false, $title = false ){

        $readme = ( !$readme_url ? file_get_contents( RBUILDER_DIR . 'readme.txt') : file_get_contents( $readme_url ) );
        $readme = make_clickable(esc_html($readme));
        $readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme);
        $readme = preg_replace( '/[\040]\*\*\NEW:\*\*/', '<strong class="new">' . esc_html__( 'New', 'resume-builder' ) . '</strong>', $readme);
        $readme = preg_replace( '/[\040]\*\*\TWEAK:\*\*/', '<strong class="tweak">' . esc_html__( 'Tweak', 'resume-builder' ) . '</strong>', $readme);
        $readme = preg_replace( '/[\040]\*\*\FIX:\*\*/', '<em class="fix">' . esc_html__( 'Fixed', 'resume-builder' ) . '</em>', $readme);
        $readme = preg_replace( '/[\040]\*\*\NEW\*\*/', '<strong class="new">' . esc_html__( 'New', 'resume-builder' ) . '</strong>', $readme);
        $readme = preg_replace( '/[\040]\*\*\TWEAK\*\*/', '<strong class="tweak">' . esc_html__( 'Tweak', 'resume-builder' ) . '</strong>', $readme);
        $readme = preg_replace( '/[\040]\*\*\FIX\*\*/', '<em class="fix">' . esc_html__( 'Fixed', 'resume-builder' ) . '</em>', $readme);

        $readme = explode( '== Changelog ==', $readme );
        $readme = explode( '== Upgrade Notice ==', $readme[1] );
        $readme = $readme[0];

        $readme = preg_replace( '/\*\*(.*?)\*\*/', '<strong>\\1</strong>', $readme);
        $readme = preg_replace( '/\*(.*?)\*/', '<em>\\1</em>', $readme);

        $whats_new_title = '<h3>' . ( $title ? esc_html( $title ) : apply_filters( 'rbuilder_whats_new_title', esc_html__( "What's new?", "resume-builder" ) ) ) . '</h3>';
        $readme = preg_replace('/= (.*?) =/', $whats_new_title, $readme);
        $readme = preg_replace("/\*+(.*)?/i","<ul class='rbuilder-whatsnew-list'><li>$1</li></ul>",$readme);
        $readme = preg_replace("/(\<\/ul\>\n(.*)\<ul class=\'rbuilder-whatsnew-list\'\>*)+/","",$readme);
        $readme = explode( $whats_new_title, $readme );
        $readme = $whats_new_title . $readme[1];
        return $readme;

    }

}
