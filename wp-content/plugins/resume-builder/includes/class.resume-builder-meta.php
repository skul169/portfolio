<?php
/**
 * Post Types
 *
 * @package     Resume Builder
 * @subpackage  Meta Fields
 * @since       2.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Resume_Builder_Meta Class
 *
 * This class handles the Resume Builder Recipe Meta Box creation.
 *
 * @since 3.0.0
 */
class Resume_Builder_Meta {

	function __construct() {

		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
        add_action( 'save_post',      array( &$this, 'save_meta_box' ) );

	}

    public static function meta_cleanup( $resume_settings ){

        $resume_excerpt = '';

        if (!empty($resume_settings)):
            foreach($resume_settings as $key => $val):
                if (!is_array($val)):
                    $resume_settings[$key] = sanitize_text_field($val);
                else:
                    foreach($val as $subkey => $subval):
                        if (!is_array($subval)):

                            if ($key === 'introduction' && $subkey == 'content'):
                                $resume_settings[$key][$subkey] = wp_kses_post( $subval );
                            elseif ($key === 'contact' && $subkey == 'address'):
                                $resume_settings[$key][$subkey] = wp_kses_post( $subval );
                            else:
                                $resume_settings[$key][$subkey] = sanitize_text_field($subval);
                            endif;

                        else:
                            foreach($subval as $sub_subkey => $sub_subval):
                                if (!is_array($sub_subval)):
                                    if ( $key === 'experience' && $sub_subkey == 'long_description' ):
                                        $resume_settings[$key][$subkey][$sub_subkey] = wp_kses_post( $sub_subval );
                                    elseif ( $key === 'experience' && $sub_subkey == 'section_text_content' ):
                                        $resume_settings[$key][$subkey][$sub_subkey] = wp_kses_post( $sub_subval );
                                    else:
                                        $resume_settings[$key][$subkey][$sub_subkey] = sanitize_text_field($sub_subval);
                                    endif;
                                endif;
                            endforeach;
                        endif;
                    endforeach;
                endif;
            endforeach;
        endif;

        return $resume_settings;

    }

	/**
     * Adds the meta box container.
     */
	public function add_meta_box( $post_type ) {

		// Limit meta box to Resume Builder Recipes.
        $post_types = apply_filters( 'resume_builder_metabox_post_types' , array( 'rb_resume' ) );

        if ( in_array( $post_type, $post_types ) ) {
            add_meta_box( 'resume_builder_settings', __( 'Resume Builder', 'resume-builder' ), array( &$this, 'render_meta_box' ), $post_type, 'normal', 'high' );
        }

	}

	/**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
     public function save_meta_box( $post_id ) {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( !isset( $_POST['resume_builder_custom_box_nonce'] ) )
            return $post_id;

        $nonce = $_POST['resume_builder_custom_box_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'resume_builder_custom_box' ) )
            return $post_id;

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user's permissions.
        if ( ! current_user_can( 'edit_posts', $post_id ) )
            return $post_id;

        global $resume_settings;

        /* OK, it's safe for us to save the data now. */
        $resume_settings = isset($_POST['_resume_settings']) ? $_POST['_resume_settings'] : false;
        $resume_settings = self::meta_cleanup( $resume_settings );

        // Update the resume settings meta field.
        update_post_meta( $post_id, '_resume_settings', $resume_settings );

    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box( ) {

	    global $post;

	    /*
		 * Output the resume meta fields
		 * @since 2.0.0
		 */
		do_action( 'resume_builder_meta_fields', $post->ID );

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'resume_builder_custom_box', 'resume_builder_custom_box_nonce' );

    }

}

/**
 * Filter - Shortcodes Tab
 *
 * @since 2.0.0
 * @param $post_id
 */

function resume_builder_shortcodes_tab_filter( $tabs ) {

	$tabs['shortcodes'] = array(
		'icon' => 'code',
		'name' => __('Shortcodes','resume-builder'),
		'conditional' => false,
		'value' => false
	);
	return $tabs;

}

function resume_builder_shortcodes_tab( $post_id ){

	?><section class="rbuilder-resume-tab-content" id="rbuilder-resume-tab-content-shortcodes">

		<div class="resume-setting-block">

			<h3 class="rbuilder-settings-title"><?php _e( 'Full Resume', 'resume-builder' ); ?></h3>
            <p class="rbuilder-bm-10"><?php esc_html_e( 'Display the resume in its entirety.', 'resume-builder' ); ?></p>

			<div class="rbuilder-block rbuilder-bm-20">
				<input class='rbuilder-shortcode-field' type='text' readonly value='[rb-resume id="<?php echo $post_id; ?>"]' />
			</div>

            <h3 class="rbuilder-settings-title"><?php _e( 'Introduction', 'resume-builder' ); ?></h3>
            <p class="rbuilder-bm-10"><?php esc_html_e( 'Display just the introduction.', 'resume-builder' ); ?></p>

            <div class="rbuilder-block rbuilder-bm-20">
                <input class='rbuilder-shortcode-field' type='text' readonly value='[rb-resume id="<?php echo $post_id; ?>" section="intro"]' />
            </div>

            <h3 class="rbuilder-settings-title"><?php _e( 'Contact Information', 'resume-builder' ); ?></h3>
            <p class="rbuilder-bm-10"><?php esc_html_e( 'Display just the contact information.', 'resume-builder' ); ?></p>

            <div class="rbuilder-block rbuilder-bm-20">
                <input class='rbuilder-shortcode-field' type='text' readonly value='[rb-resume id="<?php echo $post_id; ?>" section="contact"]' />
            </div>

            <h3 class="rbuilder-settings-title"><?php _e( 'Experience & Education', 'resume-builder' ); ?></h3>
            <p class="rbuilder-bm-10"><?php esc_html_e( 'Display just the experience & education content.', 'resume-builder' ); ?></p>

            <div class="rbuilder-block rbuilder-bm-20">
                <input class='rbuilder-shortcode-field' type='text' readonly value='[rb-resume id="<?php echo $post_id; ?>" section="history"]' />
            </div>

            <h3 class="rbuilder-settings-title"><?php _e( 'Skills', 'resume-builder' ); ?></h3>
            <p class="rbuilder-bm-10"><?php esc_html_e( 'Display just the skills content.', 'resume-builder' ); ?></p>

            <div class="rbuilder-block">
                <input class='rbuilder-shortcode-field' type='text' readonly value='[rb-resume id="<?php echo $post_id; ?>" section="skills"]' />
            </div>

		</div>

	</section><?php

}

/**
 * Recipe Fields
 *
 * @since 2.0.0
 * @param $post_id
 */
function resume_builder_render_fields( $post_id ) {

	$resume_settings = get_post_meta( $post_id, '_resume_settings', true);

	// Backwards Compatibility with Resume Builder 1.x
	$rb1_resume_settings = Resume_Builder_Resumes::get_rb1_resume_meta( $post_id );

	// Show the Shortcodes tab if resume is saved.
	if ( !empty($resume_settings) ):
		add_filter('rbuilder_resume_admin_tabs','resume_builder_shortcodes_tab_filter',10,1);
		add_action('rbuilder_resume_tabs_after','resume_builder_shortcodes_tab',10,1);
	elseif( empty($resume_settings) && !empty($rb1_resume_settings) ):
		$resume_settings = $rb1_resume_settings;
	endif;

    //echo '<pre style="width:94%; margin:3% 3% 0; background:#f5f5f5; color:#888; box-sizing:border-box; overflow:scroll; height:350px; padding:30px;">' . print_r( $resume_settings, true ) . '</pre>';

	$resume_tabs = apply_filters( 'rbuilder_resume_admin_tabs', array(
		'overview' => array(
            'icon' => 'pencil',
            'name' => __('Information','resume-builder'),
            'conditional' => false,
            'value' => false
        ),
        'experience' => array(
            'icon' => 'building',
            'name' => __('Experience, Education, Etc.','resume-builder'),
            'conditional' => false,
            'value' => false
        ),
        'skills' => array(
            'icon' => 'tasks',
            'name' => __('Skills','resume-builder'),
            'conditional' => false,
            'value' => false
        ),
	));

	$rbuilder_page_args = array(
		'sort_order' => 'asc',
		'sort_column' => 'post_title',
		'hierarchical' => false,
		'post_type' => 'page',
		'post_status' => 'publish'
	);
	$rbuilder_page_array = get_pages($rbuilder_page_args);

	if (!empty($resume_tabs)):

		echo '<ul id="rbuilder-resume-tabs">';
		$first_tab = true;

		foreach($resume_tabs as $slug => $tab):

			$classes = array();
			if ($first_tab): $classes[] = 'active'; endif;
			if ($tab['conditional']): $classes[] = 'rbuilder-conditional-hidden'; endif;

			echo "<li id='rbuilder-resume-tab-$slug'" . (!empty($classes) ? " class='".implode(" ",$classes)."'" : "") . ($tab['conditional'] ? " data-condition='".$tab['conditional']."'" : "") . ($tab['value'] ? " data-value='".$tab['value']."'" : ""). ">";
			echo $tab['icon'] ? "<i class='far fa-fw fa-".$tab['icon']."'></i>&nbsp;&nbsp;" : "";
			echo $tab['name'];
			echo "</li>";
			$first_tab = false;

		endforeach;

		echo '<li class="rbuilder-loading"><i class="far fa-spin fa-loading"></i></li>';

		echo '</ul>';

	endif; ?>

	<div class="rbuilder-resume-tab-content-wrapper">

		<?php do_action('rbuilder_resume_tabs_before'); ?>

		<section class="rbuilder-resume-tab-content rbuilderClearFix" id="rbuilder-resume-tab-content-overview">

            <div class="rbuilder-has-preview">

                <div class="resume-setting-block">
                    <h3 class="rbuilder-settings-title"><?php _e( 'Tagline', 'resume-builder' ); ?></h3>
                    <p class="rbuilder-bm-10"><?php esc_html_e( "Displays under this person's name.", "resume-builder"); ?> <?php esc_html_e( "(optional)", "resume-builder"); ?></p>
                    <p><input name="_resume_settings[introduction][subtitle]" type="text" value="<?php echo ( isset($resume_settings['introduction']['subtitle']) && $resume_settings['introduction']['subtitle'] ? esc_attr( $resume_settings['introduction']['subtitle'] ) : '' ); ?>" placeholder="<?php esc_html_e('ex. Designer, Developer, etc.','resume-builder'); ?>"></p>
                </div>

                <div class="resume-setting-block">
                    <h3 class="rbuilder-settings-title"><?php _e( 'Overview', 'resume-builder' ); ?></h3>
                    <?php $wp_editor_content = ( isset($resume_settings['introduction']['content']) ? $resume_settings['introduction']['content'] : '' ); ?>
                    <?php wp_editor( $wp_editor_content, '_resume_settings_content', array( 'teeny' => true, 'media_buttons' => false, 'wpautop' => false, 'editor_height' => 200, 'textarea_name' => '_resume_settings[introduction][content]', 'quicktags' => false ) ); ?>
                </div>

                <div class="resume-setting-block rbuilder-bm-5 rbuilderClearFix rbuilder-columns-12">

                    <div class="rbuilder-setting-column-12">
                        <h3 class="rbuilder-settings-title"><?php _e( 'Email', 'resume-builder' ); ?></h3>
                        <p><input name="_resume_settings[contact][email]" type="text" value="<?php echo ( isset($resume_settings['contact']['email']) && $resume_settings['contact']['email'] ? $resume_settings['contact']['email'] : '' ); ?>" placeholder=""></p>
                    </div>

                    <div class="rbuilder-setting-column-12">
                        <h3 class="rbuilder-settings-title"><?php _e( 'Phone', 'resume-builder' ); ?></h3>
                        <p><input name="_resume_settings[contact][phone]" type="text" value="<?php echo ( isset($resume_settings['contact']['phone']) && $resume_settings['contact']['phone'] ? $resume_settings['contact']['phone'] : '' ); ?>" placeholder="<?php esc_html_e('(123) 456-7890','resume-builder'); ?>"></p>
                    </div>

                    <div class="rbuilder-setting-column-12">
                        <h3 class="rbuilder-settings-title"><?php _e( 'Website', 'resume-builder' ); ?></h3>
                        <p><input name="_resume_settings[contact][website]" type="text" value="<?php echo ( isset($resume_settings['contact']['website']) && $resume_settings['contact']['website'] ? $resume_settings['contact']['website'] : '' ); ?>" placeholder="<?php esc_html_e('http://','resume-builder'); ?>"></p>
                    </div>

                    <div class="rbuilder-setting-column-12">
                        <h3 class="rbuilder-settings-title"><?php _e( 'Address', 'resume-builder' ); ?></h3>
                        <p class="rbuilder-bm-5"><textarea name="_resume_settings[contact][address]" placeholder=""><?php echo ( isset($resume_settings['contact']['address']) && $resume_settings['contact']['address'] ? esc_textarea( $resume_settings['contact']['address'] ) : '' ); ?></textarea></p>
                    </div>

                </div>

            </div>

            <div class="rbuilder-preview-pane">

                <div class="rbuilder-preview-pane-active">

                    <span class="rbuilder-preview-big"><span style="width:40%">&nbsp;</span></span>
                    <span class="rbuilder-preview-medium"><span style="width:55%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:30%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:27%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:33%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:25%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:95%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:93%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:96%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:87%">&nbsp;</span></span>

                    <i class="fas fa-arrow-right"></i>

                </div>

                <div class="rbuilder-preview-pane-inactive rbuilder-preview-pane-left">

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:30%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:95%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:89%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:35%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:45%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:33%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:53%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:44%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:42%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:52%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:48%">&nbsp;</span></span>

                </div>

                <div class="rbuilder-preview-pane-inactive rbuilder-preview-pane-left">

                    <span class="rbuilder-preview-small"><span style="width:40%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:45%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:40%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span>

                </div>

            </div>

		</section>

        <section class="rbuilder-resume-tab-content rbuilderClearFix" id="rbuilder-resume-tab-content-experience">

            <div class="rbuilder-has-preview">

                <div id="rbuilder-experience-builder" class="rbuilder-sortable">

                    <?php if ( isset($resume_settings['experience']) && !empty($resume_settings['experience']) ): ?>

                        <?php foreach($resume_settings['experience'] as $exp_key => $value): ?>

                            <?php if ( !isset($value['section_heading_name']) && !isset($value['section_text_content']) ): ?>

                                <?php $experience_classes = apply_filters( 'rbuilder_experience_field_classes', 'resume-setting-block rbuilder-experience-block rbuilder-experience-large rbuilderClearFix', $value ); ?>

                                <div class="<?php echo $experience_classes; ?>">

                                    <i class="far fa-bars"></i>

                                    <span class="rbuilder-experience-type"><?php esc_html_e( 'Details Block', 'resume-builder' ); ?></span>

                                    <?php do_action( 'rbuilder_before_experience_fields', $exp_key, $value ); ?>

                                    <div class="rbuilder-experience-date-range">
                                        <input type="text" data-experience-part="date-range" name="_resume_settings[experience][<?php echo $exp_key; ?>][date_range]" value="<?php echo esc_attr( $value['date_range'] ); ?>" placeholder="<?php esc_html_e('2010–2014','resume-builder'); ?>">
                                    </div>

                                    <?php do_action( 'rbuilder_after_experience_date-range_field', $exp_key, $value ); ?>

                                    <div class="rbuilder-experience-title">
                                        <input type="text" data-experience-part="title" name="_resume_settings[experience][<?php echo $exp_key; ?>][title]" value="<?php echo esc_attr( $value['title'] ); ?>" placeholder="<?php esc_html_e('Company / School','resume-builder'); ?>">
                                    </div>

                                    <?php do_action( 'rbuilder_after_experience_title_field', $exp_key, $value ); ?>

                                    <div class="rbuilder-experience-short-description">
                                        <input type="text" data-experience-part="short-description" name="_resume_settings[experience][<?php echo $exp_key; ?>][short_description]" value="<?php echo esc_attr( $value['short_description'] ); ?>" placeholder="<?php esc_html_e('Job Title / Degree','resume-builder'); ?>">
                                    </div>

                                    <?php do_action( 'rbuilder_after_experience_short_description_field', $exp_key, $value ); ?>

                                    <div class="rbuilder-experience-long-description">
                                        <?php $wp_editor_content = ( isset( $value['long_description'] ) ? $value['long_description'] : '' ); ?>
                                        <?php wp_editor( $wp_editor_content, '_resume_settings_experience_' . esc_attr( $exp_key ) . '_long_description', array( 'teeny' => true, 'media_buttons' => false, 'wpautop' => false, 'editor_height' => 113, 'editor_class' => 'rb-tinymce', 'textarea_name' => '_resume_settings[experience][' . esc_attr( $exp_key ) . '][long_description]', 'quicktags' => false ) ); ?>
                                    </div>

                                    <?php do_action( 'rbuilder_after_experience_long_description_field', $exp_key, $value ); ?>

                                    <span href="#" class="rbuilder-delete-experience"><i class="far fa-times"></i></span>

                                </div>

                            <?php elseif ( isset($value['section_heading_name']) ): ?>

                                <div class="resume-setting-block rbuilder-experience-block rbuilder-experience-heading rbuilderClearFix">
                                    <i class="far fa-bars"></i>
                                    <div class="rbuilder-heading-name">
                                        <input type="text" data-experience-part="section_heading_name" name="_resume_settings[experience][<?php echo $exp_key; ?>][section_heading_name]" value="<?php echo esc_attr( $value['section_heading_name'] ); ?>" placeholder="<?php esc_html_e('ex. Education, Experience, etc.','resume-builder'); ?> ...">
                                    </div>
                                    <span href="#" class="rbuilder-delete-experience"><i class="far fa-times"></i></span>
                                </div>

                            <?php elseif ( isset($value['section_text_content']) ): ?>

                                <div class="resume-setting-block rbuilder-experience-block rbuilder-experience-text-content rbuilder-experience-medium rbuilderClearFix">
                                    <i class="far fa-bars"></i>
                                    <span class="rbuilder-experience-type"><?php esc_html_e( 'Text Block', 'resume-builder' ); ?></span>
                                    <div class="rbuilder-heading-name">
                                        <?php $wp_editor_content = ( isset( $value['section_text_content'] ) ? $value['section_text_content'] : '' ); ?>
                                        <?php wp_editor( $wp_editor_content, '_resume_settings_experience_' . esc_attr( $exp_key ) . '_section_text_content', array( 'teeny' => true, 'media_buttons' => false, 'wpautop' => false, 'editor_height' => 113, 'editor_class' => 'rb-tinymce', 'textarea_name' => '_resume_settings[experience][' . esc_attr( $exp_key ) . '][section_text_content]', 'quicktags' => false ) ); ?>
                                    </div>
                                    <span href="#" class="rbuilder-delete-experience"><i class="far fa-times"></i></span>
                                </div>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <?php $random_key = rand(100000000000,999999999999); ?>

                        <div class="resume-setting-block rbuilderClearFix rbuilder-experience-block rbuilder-experience-heading">
                            <i class="far fa-bars"></i>
                            <div class="rbuilder-heading-name">
                                <input type="text" data-experience-part="section_heading_name" name="_resume_settings[experience][<?php echo $random_key; ?>][section_heading_name]" value="" placeholder="<?php esc_html_e('Education, Experience, etc.','resume-builder'); ?> ...">
                            </div>
                            <span href="#" class="rbuilder-delete-experience"><i class="far fa-times"></i></span>
                        </div>

                        <?php $random_key = rand(100000000000,999999999999); ?>

                        <div class="resume-setting-block rbuilder-experience-block rbuilder-experience-large rbuilderClearFix">

                            <i class="far fa-bars"></i>

                            <?php do_action( 'rbuilder_before_experience_fields', $random_key, false ); ?>

                            <div class="rbuilder-experience-date-range">
                                <input type="text" data-experience-part="date-range" name="_resume_settings[experience][<?php echo $random_key; ?>][date_range]" value="" placeholder="<?php esc_html_e('2010–2014','resume-builder'); ?>">
                            </div>

                            <?php do_action( 'rbuilder_after_experience_date-range_field', $random_key, false ); ?>

                            <div class="rbuilder-experience-title">
                                <input type="text" data-experience-part="title" name="_resume_settings[experience][<?php echo $random_key; ?>][title]" value="" placeholder="<?php esc_html_e('Company / School','resume-builder'); ?>">
                            </div>

                            <?php do_action( 'rbuilder_after_experience_title_field', $random_key, false ); ?>

                            <div class="rbuilder-experience-short-description">
                                <input type="text" data-experience-part="short-description" name="_resume_settings[experience][<?php echo $random_key; ?>][short_description]" value="" placeholder="<?php esc_html_e('Job Title / Degree','resume-builder'); ?>">
                            </div>

                            <?php do_action( 'rbuilder_after_experience_short_description_field', $random_key, false ); ?>

                            <div class="rbuilder-experience-long-description">
                                <?php $wp_editor_content = ( isset( $value['long_description'] ) ? $value['long_description'] : '' ); ?>
                                <?php wp_editor( $wp_editor_content, '_resume_settings_experience_' . esc_attr( $random_key ) . '_long_description', array( 'teeny' => true, 'media_buttons' => false, 'wpautop' => false, 'editor_height' => 113, 'editor_class' => 'rb-tinymce', 'textarea_name' => '_resume_settings[experience][' . esc_attr( $random_key ) . '][long_description]', 'quicktags' => false ) ); ?>
                            </div>

                            <?php do_action( 'rbuilder_after_experience_long_description_field', $random_key, false ); ?>

                            <span href="#" class="rbuilder-delete-experience"><i class="far fa-times"></i></span>

                        </div>

                    <?php endif; ?>

                </div>

                <div class="resume-setting-block">

                    <p>
                        <a href="#" class="button rbuilder-add-experience-button"><?php esc_html_e('Add Details','resume-builder'); ?></a>
                        &nbsp;<a href="#" class="button rbuilder-add-heading-button"><?php esc_html_e('Add Heading','resume-builder'); ?></a>
                        &nbsp;<a href="#" class="button rbuilder-add-text-button"><?php esc_html_e('Add Text','resume-builder'); ?></a>
                    </p>

                    <!-- TEMPLATES -->
                    <div class="resume-setting-block rbuilder-template rbuilder-experience-template rbuilderClearFix">

                        <i class="far fa-bars"></i>

                        <?php do_action( 'rbuilder_before_experience_fields', false, false ); ?>

                        <div class="rbuilder-experience-date-range">
                            <input type="text" data-experience-part="date-range" name="" value="" placeholder="<?php esc_html_e('2010–2014','resume-builder'); ?>">
                        </div>

                        <?php do_action( 'rbuilder_after_experience_date-range_field', false, false ); ?>

                        <div class="rbuilder-experience-title">
                            <input type="text" data-experience-part="title" name="" value="" placeholder="<?php esc_html_e('Company / School','resume-builder'); ?>">
                        </div>

                        <?php do_action( 'rbuilder_after_experience_title_field', false, false ); ?>

                        <div class="rbuilder-experience-short-description">
                            <input type="text" data-experience-part="short-description" name="" value="" placeholder="<?php esc_html_e('Job Title / Degree','resume-builder'); ?>">
                        </div>

                        <?php do_action( 'rbuilder_after_experience_short_description_field', false, false ); ?>

                        <div class="rbuilder-experience-long-description">
                            <textarea id="experience_long_description_template" data-experience-part="long-description" name="" placeholder="<?php esc_html_e('Description','resume-builder'); ?> ..."></textarea>
                        </div>

                        <?php do_action( 'rbuilder_after_experience_long_description_field', false, false ); ?>

                        <span href="#" class="rbuilder-delete-experience"><i class="far fa-times"></i></span>

                    </div>
                    <div class="resume-setting-block rbuilder-template rbuilder-heading-template rbuilderClearFix">
                        <i class="far fa-bars"></i>
                        <div class="rbuilder-heading-name">
                            <input type="text" data-experience-part="section_heading_name" name="" value="" placeholder="<?php esc_html_e('Education, Experience, etc.','resume-builder'); ?> ...">
                        </div>
                        <span href="#" class="rbuilder-delete-experience"><i class="far fa-times"></i></span>
                    </div>
                    <div class="resume-setting-block rbuilder-template rbuilder-text-template rbuilderClearFix">
                        <i class="far fa-bars"></i>
                        <div class="rbuilder-heading-name">
                            <textarea id="experience_section_text_content_template" data-experience-part="section-text-content" name="" placeholder="<?php esc_html_e('Content','resume-builder'); ?> ..."></textarea>
                        </div>
                        <span href="#" class="rbuilder-delete-experience"><i class="far fa-times"></i></span>
                    </div>
                    <!-- END TEMPLATES -->

                </div>

            </div>

            <div class="rbuilder-preview-pane">

                <div class="rbuilder-preview-pane-inactive">

                    <span class="rbuilder-preview-big"><span style="width:40%">&nbsp;</span></span>
                    <span class="rbuilder-preview-medium"><span style="width:55%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:30%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:27%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:33%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:25%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:95%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:93%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:96%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:87%">&nbsp;</span></span>

                </div>

                <div class="rbuilder-preview-pane-active rbuilder-preview-pane-left">

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:30%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:95%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:89%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:35%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:45%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:33%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:53%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:44%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:42%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:52%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:48%">&nbsp;</span></span>

                    <i class="fas fa-arrow-right"></i>

                </div>

                <div class="rbuilder-preview-pane-inactive rbuilder-preview-pane-left">

                    <span class="rbuilder-preview-small"><span style="width:40%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:45%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:40%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span>

                </div>

            </div>

        </section>

        <section class="rbuilder-resume-tab-content rbuilderClearFix" id="rbuilder-resume-tab-content-skills">

            <div class="rbuilder-has-preview">

                <div id="rbuilder-skills-builder" class="rbuilder-sortable">

                    <?php $skill_levels = apply_filters( 'rb_skill_levels', array(
                        10 => esc_attr( '5 Stars', 'resume-builder' ),
                        9 => esc_attr( '4.5 Stars', 'resume-builder' ),
                        8 => esc_attr( '4 Stars', 'resume-builder' ),
                        7 => esc_attr( '3.5 Stars', 'resume-builder' ),
                        6 => esc_attr( '3 Stars', 'resume-builder' ),
                        5 => esc_attr( '2.5 Stars', 'resume-builder' ),
                        4 => esc_attr( '2 Stars', 'resume-builder' ),
                        3 => esc_attr( '1.5 Stars', 'resume-builder' ),
                        2 => esc_attr( '1 Star', 'resume-builder' ),
                        1 => esc_attr( '0.5 Stars', 'resume-builder' ),
                    ) ); ?>

                    <?php if ( isset($resume_settings['skills']) && !empty($resume_settings['skills']) ): ?>

                        <?php foreach($resume_settings['skills'] as $exp_key => $value): ?>

                            <?php if ( !isset($value['section_heading_name']) ): ?>

                                <?php $skills_classes = apply_filters( 'rbuilder_skills_field_classes', 'resume-setting-block rbuilder-skills-block rbuilder-skills-large rbuilderClearFix', $value ); ?>

                                <div class="<?php echo $skills_classes; ?>">

                                    <i class="far fa-bars"></i>

                                    <?php do_action( 'rbuilder_before_skills_fields', $exp_key, $value ); ?>

                                    <div class="rbuilder-skills-title">
                                        <input type="text" data-skills-part="title" name="_resume_settings[skills][<?php echo $exp_key; ?>][title]" value="<?php echo esc_attr( $value['title'] ); ?>" placeholder="<?php esc_html_e('Skill title','resume-builder'); ?> ...">
                                    </div>

                                    <?php do_action( 'rbuilder_after_skills_title_field', $exp_key, $value ); ?>

                                    <div class="rbuilder-skills-rating">
                                        <select data-skills-part="rating" name="_resume_settings[skills][<?php echo $exp_key; ?>][rating]">
                                            <option value=""><?php echo esc_attr( 'Skill level', 'resume-builder' ); ?> ...</option>
                                            <?php foreach($skill_levels as $key => $content):
                                                echo '<option value="' . $key . '"' . ( $value['rating'] == $key ? ' selected' : '' ) . '>' . esc_attr( $content ) . '</option>';
                                            endforeach; ?>
                                        </select>
                                    </div>

                                    <?php do_action( 'rbuilder_after_skills_rating_field', $exp_key, $value ); ?>

                                    <div class="rbuilder-skills-description">
                                        <input type="text" data-skills-part="description" name="_resume_settings[skills][<?php echo $exp_key; ?>][description]" value="<?php echo esc_attr( $value['description'] ); ?>" placeholder="<?php esc_html_e('Description','resume-builder'); ?> ...">
                                    </div>

                                    <?php do_action( 'rbuilder_after_skills_description_field', $exp_key, $value ); ?>

                                    <span href="#" class="rbuilder-delete-skills"><i class="far fa-times"></i></span>

                                </div>

                            <?php elseif ( isset($value['section_heading_name']) ): ?>

                                <div class="resume-setting-block rbuilder-skills-block rbuilder-skills-heading rbuilderClearFix">
                                    <i class="far fa-bars"></i>
                                    <div class="rbuilder-heading-name">
                                        <input type="text" data-skills-part="section_heading_name" name="_resume_settings[skills][<?php echo $exp_key; ?>][section_heading_name]" value="<?php echo esc_attr( $value['section_heading_name'] ); ?>" placeholder="">
                                    </div>
                                    <span href="#" class="rbuilder-delete-skills"><i class="far fa-times"></i></span>
                                </div>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <?php $random_key = rand(100000000000,999999999999); ?>

                        <div class="resume-setting-block rbuilderClearFix rbuilder-skills-block rbuilder-skills-heading">
                            <i class="far fa-bars"></i>
                            <div class="rbuilder-heading-name">
                                <input type="text" data-skills-part="section_heading_name" name="_resume_settings[skills][<?php echo $random_key; ?>][section_heading_name]" value="<?php esc_html_e('Skills','resume-builder'); ?> " placeholder="">
                            </div>
                            <span href="#" class="rbuilder-delete-skills"><i class="far fa-times"></i></span>
                        </div>

                        <?php $random_key = rand(100000000000,999999999999); ?>

                        <div class="resume-setting-block rbuilder-skills-block rbuilder-skills-large rbuilderClearFix">

                            <i class="far fa-bars"></i>

                            <?php do_action( 'rbuilder_before_skills_fields', $random_key, false ); ?>

                            <div class="rbuilder-skills-title">
                                <input type="text" data-skills-part="title" name="_resume_settings[skills][<?php echo $random_key; ?>][title]" value="" placeholder="<?php esc_html_e('Skill title','resume-builder'); ?> ...">
                            </div>

                            <?php do_action( 'rbuilder_after_skills_title_field', $random_key, false ); ?>

                            <div class="rbuilder-skills-rating">
                                <select data-skills-part="rating" name="_resume_settings[skills][<?php echo $random_key; ?>][rating]">
                                    <option value=""><?php echo esc_attr( 'Skill level', 'resume-builder' ); ?> ...</option>
                                    <?php foreach($skill_levels as $key => $content):
                                        echo '<option value="' . $key . '">' . esc_attr( $content ) . '</option>';
                                    endforeach; ?>
                                </select>
                            </div>

                            <?php do_action( 'rbuilder_after_skills_rating_field', $random_key, false ); ?>

                            <div class="rbuilder-skills-description">
                                <input type="text" data-skills-part="description" name="_resume_settings[skills][<?php echo $random_key; ?>][description]" value="" placeholder="<?php esc_html_e('Description','resume-builder'); ?> ...">
                            </div>

                            <?php do_action( 'rbuilder_after_skills_description_field', $random_key, false ); ?>

                            <span href="#" class="rbuilder-delete-skills"><i class="far fa-times"></i></span>

                        </div>

                    <?php endif; ?>

                </div>

                <div class="resume-setting-block">

                    <p>
                        <a href="#" class="button rbuilder-add-skills-button"><?php esc_html_e('Add Skill','resume-builder'); ?></a>
                        &nbsp;<a href="#" class="button rbuilder-add-heading-button"><?php esc_html_e('Add Heading','resume-builder'); ?></a>
                    </p>

                    <!-- TEMPLATES -->
                    <div class="resume-setting-block rbuilder-template rbuilder-skills-template rbuilderClearFix">

                        <i class="far fa-bars"></i>

                        <?php do_action( 'rbuilder_before_skills_fields', false, false ); ?>

                        <div class="rbuilder-skills-title">
                            <input type="text" data-skills-part="title" name="" value="" placeholder="<?php esc_html_e('Skill title','resume-builder'); ?> ...">
                        </div>

                        <?php do_action( 'rbuilder_after_skills_title_field', false, false ); ?>

                        <div class="rbuilder-skills-rating">
                            <select data-skills-part="rating" name="">
                                <option value=""><?php echo esc_attr( 'Skill level', 'resume-builder' ); ?> ...</option>
                                <?php foreach($skill_levels as $key => $content):
                                    echo '<option value="' . $key . '">' . esc_attr( $content ) . '</option>';
                                endforeach; ?>
                            </select>
                        </div>

                        <?php do_action( 'rbuilder_after_skills_rating_field', false, false ); ?>

                        <div class="rbuilder-skills-description">
                            <input type="text" data-skills-part="description" name="" value="" placeholder="<?php esc_html_e('Description','resume-builder'); ?> ...">
                        </div>

                        <?php do_action( 'rbuilder_after_skills_description_field', false, false ); ?>

                        <span href="#" class="rbuilder-delete-skills"><i class="far fa-times"></i></span>

                    </div>
                    <div class="resume-setting-block rbuilder-template rbuilder-heading-template rbuilderClearFix">
                        <i class="far fa-bars"></i>
                        <div class="rbuilder-heading-name">
                            <input type="text" data-skills-part="section_heading_name" name="" value="" placeholder="">
                        </div>
                        <span href="#" class="rbuilder-delete-skills"><i class="far fa-times"></i></span>
                    </div>
                    <!-- END TEMPLATES -->

                </div>

            </div>

            <div class="rbuilder-preview-pane">

                <div class="rbuilder-preview-pane-inactive">

                    <span class="rbuilder-preview-big"><span style="width:40%">&nbsp;</span></span>
                    <span class="rbuilder-preview-medium"><span style="width:55%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:30%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:27%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:33%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:25%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:95%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:93%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:96%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:87%">&nbsp;</span></span>

                </div>

                <div class="rbuilder-preview-pane-inactive rbuilder-preview-pane-left">

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:30%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:95%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:89%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:35%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:45%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:33%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:53%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:44%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-medium"><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:10%; margin-right:3px;">&nbsp;</span><span style="width:42%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:52%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:48%">&nbsp;</span></span>

                </div>

                <div class="rbuilder-preview-pane-active rbuilder-preview-pane-left">

                    <span class="rbuilder-preview-small"><span style="width:40%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:45%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span><br>

                    <span class="rbuilder-preview-small"><span style="width:40%;">&nbsp;</span></span>
                    <span class="rbuilder-preview-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                    <span class="rbuilder-preview-small"><span style="width:90%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:80%">&nbsp;</span></span>
                    <span class="rbuilder-preview-small"><span style="width:50%">&nbsp;</span></span>

                    <i class="fas fa-arrow-right"></i>

                </div>

            </div>

        </section>

        <?php do_action( 'rbuilder_resume_tabs_after', $post_id ); ?>

    </div>

<?php
}

add_action( 'resume_builder_meta_fields', 'resume_builder_render_fields', 10 );
