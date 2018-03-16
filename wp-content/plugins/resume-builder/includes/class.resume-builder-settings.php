<?php
/**
 * Register Settings
 *
 * @package     Resume Builder
 * @subpackage  Settings
 * @since       1.0.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Resume_Builder_Settings Class
 *
 * This class handles the settings creation and contains functions for retreiving those settings.
 *
 * @since 1.0.0
 */
class Resume_Builder_Settings {

	public function __construct(){
		add_filter( 'admin_init', array( &$this, 'init' ) );
		add_filter( 'init', array( &$this, 'init' ) );
	}

	public static function init() {

		global $_rbuilder_settings,$list_id_counter;
		$list_id_counter = 0;
		$_rbuilder_settings = Resume_Builder_Settings::get();
		register_setting( 'rbuilder_settings_group','rbuilder_settings' );
		register_setting( 'rbuilder_settings_group','rbuilder_settings_saved' );

	}

	public static function reset() {
		global $_rbuilder_settings;
		$_rbuilder_settings = Resume_Builder_Settings::get();
	}

	public static function get() {

		$update_settings = false;
		$_rbuilder_settings = get_option( 'rbuilder_settings' );
		$rbuilder_settings_saved = get_option( 'rbuilder_settings_saved', false );
		$_rbuilder_settings_version = get_option( 'rbuilder_settings_version', '2.0' );
		$_og_rbuilder_settings = $_rbuilder_settings;

		$version_compare = version_compare( $_rbuilder_settings_version, RBUILDER_VERSION );

		// Get defaults for fields that are not set yet.
		$rbuilder_tabs_fields = self::tabs_fields();
		foreach( $rbuilder_tabs_fields as $tab ):
			foreach( $tab['fields'] as $name => $field ):

				if ( $field['type'] == 'nonce' )
					continue;

				if ( $field['type'] == 'checkboxes' && $rbuilder_settings_saved && $version_compare >= 0 ):
					$_rbuilder_settings[$name] = ( isset($_rbuilder_settings[$name]) ? $_rbuilder_settings[$name] : array() );
				else:
					$_rbuilder_settings[$name] = ( isset($_rbuilder_settings[$name]) ? $_rbuilder_settings[$name] : ( isset( $field['default'] ) ? $field['default'] : false ) );
					$update_settings = true;
				endif;

			endforeach;
		endforeach;

		if ( $update_settings ): update_option( 'rbuilder_settings', $_rbuilder_settings ); endif;
		if ( $version_compare < 0 ):
			update_option( 'rbuilder_settings_version', RBUILDER_VERSION );
		endif;

		return apply_filters( 'rbuilder_get_settings', $_rbuilder_settings );

	}

	public static function tabs_fields() {

		return apply_filters('rbuilder_settings_tabs_fields', array(

			'rb_general' => array(
				'name' => esc_html__('General','resume-builder'),
				'icon' => 'cog',
				'fields' => array(
					'fix_tinyfonts' => array(
						'title' => esc_html__('Tiny Fonts Fix', 'resume-builder'),
						'desc' => esc_html__( 'Does your resume have tiny fonts? Check this to fix it.', 'resume-builder' ),
						'type' => 'checkboxes',
						'default' => array( 'enabled' ),
						'options' => apply_filters( 'rbuilder_tinyfonts_fix', array(
							'enabled' => esc_html__('Fix "Tiny Fonts"','resume-builder'),
						))
					),
				)
			),
			'rb_colors' => array(
				'name' => esc_html__('Colors','resume-builder'),
				'icon' => 'eye-dropper',
				'fields' => array(
					'headings_color' => array(
						'title' => esc_html__('Headings', 'resume-builder'),
						'desc' => esc_html__( 'Choose a color for your resume headings.', 'resume-builder' ),
						'type' => 'color_field',
						'default' => '#f35e46',
						'options' => '#f35e46'
					),
					'stars_color' => array(
						'title' => esc_html__('Stars', 'resume-builder'),
						'desc' => esc_html__( 'Choose a color for your "Skills" stars.', 'resume-builder' ),
						'type' => 'color_field',
						'default' => '#f7b94f',
						'options' => '#f7b94f'
					),
				)
			)

		) );

	}

	public static function field_radio( $field_name, $options ){
		global $_rbuilder_settings,$conditions;
		echo '<p class="rbuilder-padded">';
			foreach( $options as $value => $name) :

				$is_disabled = '';
				$conditional_value = '';
				$conditional_requirement = '';

				if ( is_array($name) ):
					if ( isset($name['read_only']) && $name['read_only'] ):
						$is_disabled = ' disabled';
					endif;
					if ( isset($name['conditional_value']) && $name['conditional_value'] ):
						$conditional_value = ' v-model="' . esc_attr($name['conditional_value']) . '"';
						if ( !in_array( $name['conditional_value'], $conditions ) ):
							$conditions[$value] = esc_attr($name['conditional_requirement']);
						endif;
					endif;
					if ( isset($name['conditional_requirement']) && $name['conditional_requirement'] ):
						if ( is_array($name['conditional_requirement']) ):
							$conditional_requirement = ' v-show="' . implode( ' && ', $name['conditional_requirement'] ) . '"';
						else:
							$conditional_requirement = ' v-show="' . esc_attr($name['conditional_requirement']) . '"';
						endif;
					endif;
					$name = $name['label'];
				endif;

				$combined_extras = $is_disabled . $conditional_value;

				if ( $conditional_requirement ): echo '<transition name="fade"><span class="conditional-requirement"' . $conditional_requirement . '>'; endif;
				echo '<input' . $combined_extras . ' type="radio" id="radio-group-' . $field_name . '-' . $value . '" name="rbuilder_settings[' . $field_name . ']" value="' . $value . '"' . ( isset( $_rbuilder_settings[$field_name] ) && $_rbuilder_settings[$field_name] == $value ? ' checked' : '' ) . '/>';
				echo '&nbsp;<label for="radio-group-' . $field_name . '-' . $value . '">' . $name . '</label>';
				echo '<br>';
				if ( $conditional_requirement ): echo '</span></transition>'; endif;

			endforeach;
		echo '</p>';
	}

	public static function field_select( $field_name, $options ){
		global $_rbuilder_settings;
		echo '<p>';
			echo '<select name="rbuilder_settings[' . $field_name . ']">';
			foreach( $options as $value => $name) :
				echo '<option value="' . $value . '"' . ( isset( $_rbuilder_settings[$field_name] ) && $_rbuilder_settings[$field_name] == $value ? ' selected' : '' ) . '>' . $name . '</option>';
			endforeach;
			echo '</select>';
		echo '</p>';
	}

	public static function field_nonce( $field_name, $options ){
		wp_nonce_field( $field_name, $field_name );
	}

	public static function field_text( $field_name, $placeholder ){
		global $_rbuilder_settings;
		echo '<p>';
			echo '<input type="text"' . ( $placeholder ? ' placeholder="' . esc_attr( $placeholder ) . '"' : '' ) . ' name="rbuilder_settings[' . $field_name . ']" value="' . ( isset( $_rbuilder_settings[$field_name] ) && $_rbuilder_settings[$field_name] ? $_rbuilder_settings[$field_name] : '' ) . '">';
		echo '</p>';
	}

	public static function field_password( $field_name, $placeholder ){
		global $_rbuilder_settings;
		echo '<p>';
			echo '<input type="password"' . ( $placeholder ? ' placeholder="' . esc_attr( $placeholder ) . '"' : '' ) . ' name="rbuilder_settings[' . $field_name . ']" value="' . ( isset( $_rbuilder_settings[$field_name] ) && $_rbuilder_settings[$field_name] ? $_rbuilder_settings[$field_name] : '' ) . '">';
		echo '</p>';
	}

	public static function field_html( $field_name, $placeholder ){
		global $_rbuilder_settings;
		echo $placeholder;
	}

	public static function field_permalink_field( $field_name, $end_of_url ){
		global $_rbuilder_settings;
		echo '<p class="rbuilder-permalink-field-wrapper">';
			echo '<span>' . get_home_url() . '/</span><input type="text" class="rbuilder-permalink-field" name="rbuilder_settings[' . $field_name . ']" value="' . ( isset( $_rbuilder_settings[$field_name] ) && $_rbuilder_settings[$field_name] ? $_rbuilder_settings[$field_name] : '' ) . '"><span>/' . $end_of_url . '/</span>';
		echo '</p>';
	}

	public static function field_number_field( $field_name, $options ){
		global $_rbuilder_settings;
		echo '<p>';
			echo '<input type="number" step="any" name="rbuilder_settings[' . $field_name . ']" value="' . ( isset( $_rbuilder_settings[$field_name] ) && $_rbuilder_settings[$field_name] ? $_rbuilder_settings[$field_name] : '' ) . '">';
		echo '</p>';
	}

	public static function field_color_field( $field_name, $default ){
		global $_rbuilder_settings;
		echo '<p>';
			echo '<input class="rbuilder-color-field" type="text"' . ( $default ? ' data-default-color="' . esc_attr( $default ) . '"' : '' ) . ' name="rbuilder_settings[' . $field_name . ']" value="' . ( isset( $_rbuilder_settings[$field_name] ) && $_rbuilder_settings[$field_name] ? $_rbuilder_settings[$field_name] : '' ) . '">';
		echo '</p>';
	}

	public static function field_checkboxes( $field_name, $options, $color = false ){
		global $_rbuilder_settings,$conditions;
		echo '<p class="rbuilder-padded">';
			foreach( $options as $value => $name) :

				$is_disabled = '';
				$conditional_value = '';
				$conditional_requirement = '';

				if ( is_array($name) ):
					if ( isset($name['read_only']) && $name['read_only'] ):
						$is_disabled = ' disabled';
					endif;
					if ( isset($name['conditional_value']) && $name['conditional_value'] ):
						$conditional_value = ' v-model="' . esc_attr($name['conditional_value']) . '"';
						if ( !in_array( $name['conditional_value'], $conditions ) ):
							$conditions[$field_name][$name['conditional_value']] = $value;
						endif;
					endif;
					if ( isset($name['conditional_requirement']) && $name['conditional_requirement'] ):
						if ( is_array($name['conditional_requirement']) ):
							$conditional_requirement = ' v-show="' . implode( ' && ', $name['conditional_requirement'] ) . '"';
						else:
							$conditional_requirement = ' v-show="' . esc_attr($name['conditional_requirement']) . '"';
						endif;
					endif;
					$name = $name['label'];
				endif;

				$combined_extras = $is_disabled . $conditional_value;

				if ( $conditional_requirement ): echo '<transition name="fade"><span class="conditional-requirement"' . $conditional_requirement . '>'; endif;
				if ( $is_disabled ):
					echo '<input type="hidden" name="cooked_settings[' . $field_name . '][]" value="' . $value . '">';
					echo '<input' . $combined_extras . ' class="rbuilder-switch' . ( $color ? '-' . $color : '' ) . '" type="checkbox" id="checkbox-group-' . $field_name . '-' . $value . '"' . ( isset( $_rbuilder_settings[$field_name] ) && !empty($_rbuilder_settings[$field_name]) && in_array( $value, $_rbuilder_settings[$field_name] ) || $is_disabled ? ' checked' : '' ) . '/>';
				else:
					echo '<input' . $combined_extras . ' class="rbuilder-switch' . ( $color ? '-' . $color : '' ) . '" type="checkbox" id="checkbox-group-' . $field_name . '-' . $value . '" name="rbuilder_settings[' . $field_name . '][]" value="' . $value . '"' . ( isset( $_rbuilder_settings[$field_name] ) && !empty($_rbuilder_settings[$field_name]) && in_array( $value, $_rbuilder_settings[$field_name] ) || $is_disabled ? ' checked' : '' ) . '/>';
				endif;
				echo '&nbsp;<label for="checkbox-group-' . $field_name . '-' . $value . '">' . $name . '</label>';
				echo '<br>';
				if ( $conditional_requirement ): echo '</span></transition>'; endif;

			endforeach;
		echo '</p>';
	}

}
