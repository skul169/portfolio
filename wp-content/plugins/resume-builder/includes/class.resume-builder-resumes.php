<?php
/**
 * Resume Builder Recipe-Specific Functions
 *
 * @package     Resume Builder
 * @subpackage  Recipe-Specific Functions
 * @since       2.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Resume_Builder_Recipe_Meta Class
 *
 * This class handles the Resume Builder Recipe Meta Box creation.
 *
 * @since 3.0.0
 */
class Resume_Builder_Resumes {

	public function __construct() {

		add_filter( 'the_content', array(&$this, 'rb_resume_post_type_template'), 1, 1 );

	}

	public function rb_resume_post_type_template( $content ){

		global $post, $_rbuilder_content_unfiltered;

		if( $_rbuilder_content_unfiltered == false && $post->post_type == 'rb_resume' && is_singular('rb_resume') ):

			remove_filter('the_content', 'wpautop');

			ob_start();
			global $resume;
			$args = array( 'post_type' => 'rb_resume', 'post__in' => array( $post->ID ) );
			$resume = Resume_Builder_Resumes::get( $args );
			echo '<div class="rb-resume-default-template-left">';
				echo do_shortcode( '[rb-resume id="' . esc_attr( $post->ID ) .'" section="intro"][rb-resume id="' . esc_attr( $post->ID ) .'" section="history"]' );
			echo '</div>';
			echo '<div class="rb-resume-default-template-right">';
				echo do_shortcode( '[rb-resume id="' . esc_attr( $post->ID ) .'" section="skills" style="compact"]' );
			echo '</div>';
			return do_shortcode( ob_get_clean() );


		endif;

		return $content;

	}

	public static function get( $args = false ) {

		$resumes = array();
		$counter = 0;

		if (!$args):

			$args = array(
				'post_type' => 'rb_resume',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'orderby'=>'name',
				'order'=>'ASC'
			);

		endif;

		$resumes_results = new WP_Query($args);
		if ( $resumes_results->have_posts() ):
			while ( $resumes_results->have_posts() ): $resumes_results->the_post();

				$resumes[$counter]['id'] = $resumes_results->post->ID;
				$resumes[$counter]['title'] = $resumes_results->post->post_title;

				// Backwards Compatibility Check
				$resume_settings = get_post_meta( $resumes_results->post->ID, '_resume_settings', true);

				// No resume settings? This is probably from an older version.
				if ( empty($resume_settings) ):
					$rb1_resume_settings = Resume_Builder_Resumes::get_rb1_resume_meta( $resumes_results->post->ID );
					if ( !empty($rb1_resume_settings) ):
						$resume_settings = $rb1_resume_settings;
					endif;
				endif;

				foreach($resume_settings as $key => $setting):
					$resumes[$counter][$key] = $setting;
				endforeach;

				$counter++;

			endwhile;
		endif;

		wp_reset_postdata();

        if (count($resumes) == 1): $resumes = $resumes[0]; endif;

		return $resumes;

	}

	public static function get_by_slug($slug = false){
		if ($slug):

			if (!function_exists('ctype_digit') || function_exists('ctype_digit') && !ctype_digit($slug)):
				$resume_query = new WP_Query( array( 'name' => $slug, 'post_type' => 'rb_resume' ) );
				if ($resume_query->have_posts()):
					$resume_query->the_post();
					return get_the_ID();
				else:
					return false;
				endif;
			else:
				return $slug;
			endif;

		else:

			return false;

		endif;
	}

	/**
	 * Resume Builder 1.x Backwards Compatibility
	 *
	 * @since 2.0.0
	 */

	// Get and return the Resume Builder 1.x resume meta information
	public static function get_rb1_resume_meta( $post_id ){

		$resume_settings = array(); $revised_array = array();
		$resume_rb1_meta = get_post_meta($post_id);

		// If nothing, return nothing.
		if ( empty($resume_rb1_meta) )
			return false;

		foreach($resume_rb1_meta as $key => $content):
			$revised_array[$key] = $content[0];
		endforeach;

		$resume_rb1_meta = $revised_array;

		$attachment_id = isset($resume_rb1_meta['_rb_resume_sections_introduction_block-_sectionimage_0']) ? $resume_rb1_meta['_rb_resume_sections_introduction_block-_sectionimage_0'] : false;

		if ( !has_post_thumbnail( $post_id ) && $attachment_id ):
			set_post_thumbnail( $post_id, $attachment_id );
		endif;

		$resume_settings['contact']['title'] = isset($resume_rb1_meta['_rb_resume_widget_contacts_title']) ? $resume_rb1_meta['_rb_resume_widget_contacts_title'] : false;
		$resume_settings['contact']['email'] = isset($resume_rb1_meta['_rb_resume_widget_contacts_email']) ? $resume_rb1_meta['_rb_resume_widget_contacts_email'] : false;
		$resume_settings['contact']['phone'] = isset($resume_rb1_meta['_rb_resume_widget_contacts_phone']) ? $resume_rb1_meta['_rb_resume_widget_contacts_phone'] : false;
		$resume_settings['contact']['website'] = isset($resume_rb1_meta['_rb_resume_widget_contacts_website']) ? $resume_rb1_meta['_rb_resume_widget_contacts_website'] : false;
		$resume_settings['contact']['address'] = isset($resume_rb1_meta['_rb_resume_widget_contacts_address']) ? $resume_rb1_meta['_rb_resume_widget_contacts_address'] : false;

		$resume_settings['introduction']['title'] = isset($resume_rb1_meta['_rb_resume_sections_introduction_block-_sectiontitle_0']) ? $resume_rb1_meta['_rb_resume_sections_introduction_block-_sectiontitle_0'] : false;
		$resume_settings['introduction']['subtitle'] = isset($resume_rb1_meta['_rb_resume_sections_introduction_block-_sectionsubtitle_0']) ? $resume_rb1_meta['_rb_resume_sections_introduction_block-_sectionsubtitle_0'] : false;
		$resume_settings['introduction']['content'] = isset($resume_rb1_meta['_rb_resume_sections_introduction_block-_sectiontext_0']) ? $resume_rb1_meta['_rb_resume_sections_introduction_block-_sectiontext_0'] : false;

		$experience_temp = 0;
		$skills_temp = 0;

		foreach( $resume_rb1_meta as $key => $val ):
			$key_part = explode( '-', $key );
			if ( $key_part[0] == '_rb_resume_sections_default_block' ):
				if ( strpos( $key_part[1], 'sectiontitle' ) ):
					$resume_settings['experience'][$experience_temp]['section_heading_name'] = $val;
					$experience_temp++;
				elseif ( strpos( $key_part[1], 'text_block' ) ):
					$resume_settings['experience'][$experience_temp]['section_text_content'] = $val;
					$experience_temp++;
				elseif ( strpos( $key_part[1], 'detailed_row' ) ):
					if ( strpos( $key_part[2], 'rowtitle' ) ):
						$resume_settings['experience'][$experience_temp]['title'] = $val;
					elseif ( strpos( $key_part[2], 'rowsubtitle' ) ):
						$resume_settings['experience'][$experience_temp]['short_description'] = $val;
					elseif ( strpos( $key_part[2], 'rowsidetext' ) ):
						$resume_settings['experience'][$experience_temp]['date_range'] = $val;
					elseif ( strpos( $key_part[2], 'rowtext' ) ):
						$resume_settings['experience'][$experience_temp]['long_description'] = $val;
						$experience_temp++;
					endif;
				endif;
			endif;
			if ( strpos( $key_part[0], 'skills' ) ):
				if ( $key_part[0] == '_rb_resume_widget_skills_title' ):
					$resume_settings['skills'][$skills_temp]['section_heading_name'] = $val;
					$skills_temp++;
				elseif ( isset( $key_part[1] ) ):
					if ( strpos( $key_part[1], 'title' ) ):
						$resume_settings['skills'][$skills_temp]['title'] = $val;
					elseif( strpos( $key_part[1], 'rating' ) ):
						$rating = $val * 2;
						$resume_settings['skills'][$skills_temp]['rating'] = $rating;
					elseif( strpos( $key_part[1], 'text' ) ):
						$resume_settings['skills'][$skills_temp]['description'] = $val;
						$skills_temp++;
					endif;
				endif;
			endif;
		endforeach;

		return $resume_settings;

	}

	public static function rating( $rating = false ){

		if ( !$rating )
			return false;

		ob_start();

		$hs = '<i class="fas fa-star-half"></i>';
		$fs = '<i class="fas fa-star"></i>';

		switch ( $rating ):
			case 1:
				echo $hs;
			break;
			case 2:
				echo $fs;
			break;
			case 3:
				echo $fs . $hs;
			break;
			case 4:
				echo $fs . $fs ;
			break;
			case 5:
				echo $fs . $fs . $hs;
			break;
			case 6:
				echo $fs . $fs . $fs;
			break;
			case 7:
				echo $fs . $fs . $fs . $hs;
			break;
			case 8:
				echo $fs . $fs . $fs . $fs;
			break;
			case 9:
				echo $fs . $fs . $fs . $fs . $hs;
			break;
			case 10:
				echo $fs . $fs . $fs . $fs . $fs;
			break;
		endswitch;

		return ob_get_clean();

	}

}

global $Resume_Builder_Resumes;
$Resume_Builder_Resumes = new Resume_Builder_Resumes();
