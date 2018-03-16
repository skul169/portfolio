<?php 

include_once 'Resume_Field.php';

interface Resume_DataStore {
	function load(Resume_Field $field);
	function save(Resume_Field $field);
	function delete(Resume_Field $field);
	function load_values($field);
	function delete_values(Resume_Field $field);
}

abstract class Resume_DataStore_Base implements Resume_DataStore {
	function __construct() {
		$this->init();
	}

	abstract function init();

	static function factory($type) {
		$type = str_replace(" ", '_', ucwords(str_replace("_", ' ', $type)));

		$class = 'Resume_DataStore_' . $type;

		if (!class_exists($class)) {
			throw new Resume_Exception ('Unknown data store type "' . $type . '".');
		}

		$field = new $class();

		return $field;
	}
}

class Resume_DataStore_CustomField extends Resume_DataStore_Base {
	protected $post_id;

	function init() {}

	function save(Resume_Field $field) {
		if ( !update_post_meta($this->post_id, $field->get_name(), $field->get_value()) ) {
			add_post_meta($this->post_id, $field->get_name(), $field->get_value(), true);
		}
	}

	function load(Resume_Field $field) {
		global $wpdb;

		$value = $wpdb->get_col('
			SELECT `meta_value`
			FROM ' . $wpdb->postmeta . '
			WHERE `post_id`=' . intval($this->post_id) . '
			AND `meta_key`="' . $field->get_name() . '"
			LIMIT 1
		');

		if ( !is_array($value) || count($value) < 1 ) {
			$field->set_value(false);
			return;
		}

		$field->set_value($value[0]);
	}

	function delete(Resume_Field $field) {
		delete_post_meta($this->post_id, $field->get_name(), $field->get_value());
	}

	function load_values($field) {
		global $wpdb;

		if ( is_object($field) && is_subclass_of($field, 'Resume_Field') ) {
			$meta_key = $field->get_name();
		} else {
			$meta_key = $field;
		}

		return $wpdb->get_results('
			SELECT meta_key AS field_key, meta_value AS field_value FROM ' . $wpdb->postmeta . '
			WHERE `meta_key` LIKE "' . addslashes($meta_key) . '_%" AND `post_id`="' . intval($this->post_id) . '"
		', ARRAY_A);
	}

	function delete_values(Resume_Field $field) {
		global $wpdb;

		$group_names = $field->get_group_names();
		$field_name = $field->get_name();

		$meta_key_constraint = '`meta_key` LIKE "' . $field_name . implode('-%" OR `meta_key` LIKE "' . $field_name, $group_names) . '-%"';

		return $wpdb->query('
			DELETE FROM ' . $wpdb->postmeta . '
			WHERE (' . $meta_key_constraint . ') AND `post_id`="' . intval($this->post_id) . '"
		');
	}

	function set_id($post_id) {
		$this->post_id = $post_id;
	}
}

class Resume_DataStore_ThemeOptions extends Resume_DataStore_Base {
	function init() {}

	function save(Resume_Field $field) {
		$name = $field->get_name();
		$autoload = $field->get_autoload() ? 'yes': 'no';

		// Add value to the cache, so that add_option always works
		$notoptions = wp_cache_get( 'notoptions', 'options' );
		$notoptions[$name] = '';
		wp_cache_set( 'notoptions', $notoptions, 'options' );

		if ( !add_option($name, $field->get_value(), null, $autoload) ) {
			update_option($name, $field->get_value());
		}
	}

	function load(Resume_Field $field) {
		$field->set_value( get_option($field->get_name()) );
	}

	function delete(Resume_Field $field) {
		delete_option($field->get_name());
	}

	function load_values($field) {
		global $wpdb;

		if ( is_object($field) && is_subclass_of($field, 'Resume_Field') ) {
			$meta_key = $field->get_name();
		} else {
			$meta_key = $field;
		}

		return $wpdb->get_results('
			SELECT option_name AS field_key, option_value AS field_value FROM ' . $wpdb->options . '
			WHERE `option_name` LIKE "' . addslashes($meta_key) . '_%"
		', ARRAY_A);
	}

	function delete_values(Resume_Field $field) {
		global $wpdb;

		$group_names = $field->get_group_names();
		$field_name = $field->get_name();

		$option_name_constraint = '`option_name` LIKE "' . $field_name . implode('-%" OR `option_name` LIKE "' . $field_name, $group_names) . '-%"';

		return $wpdb->query('
			DELETE FROM ' . $wpdb->options . '
			WHERE (' . $option_name_constraint . ')
		');
	}
}

class Resume_DataStore_TermMeta extends Resume_DataStore_Base {
	protected $term_id;

	static function create_table() {
		global $wpdb;

		$tables = $wpdb->get_results('SHOW TABLES LIKE "' . $wpdb->prefix . 'termmeta"');

		if ( !empty($tables) ) {
			return;
		}

		$charset_collate = '';	
		if ( ! empty($wpdb->charset) ) {
			$charset_collate = "DEFAULT CHARACTER SET " . $wpdb->charset;
		}
			
		if ( ! empty($wpdb->collate) ) {
			$charset_collate .= " COLLATE " . $wpdb->collate;
		}

		$wpdb->query('CREATE TABLE ' . $wpdb->prefix . 'termmeta (
			meta_id bigint(20) unsigned NOT NULL auto_increment,
			term_id bigint(20) unsigned NOT NULL default "0",
			meta_key varchar(255) default NULL,
			meta_value longtext,
			PRIMARY KEY	(meta_id),
			KEY term_id (term_id),
			KEY meta_key (meta_key)
		) ' . $charset_collate . ';');
	}

	function init() {
		global $wpdb;

		// Setup termmeta table and hooks only once
		if ( !empty($wpdb->termmeta) ) {
			return;
		}

		$wpdb->termmeta = $wpdb->prefix . 'termmeta';

		self::create_table();

		// Delete all meta associated with the deleted term
		add_action('delete_term', array(__CLASS__, 'on_delete_term'), 10, 3);
	}

	function save(Resume_Field $field) {
		if ( !add_metadata('term', $this->term_id, $field->get_name(), $field->get_value(), true) ) {
			update_metadata('term', $this->term_id, $field->get_name(), $field->get_value());
		}
	}

	function load(Resume_Field $field) {
		global $wpdb;

		$value = $wpdb->get_col('
			SELECT `meta_value`
			FROM ' . $wpdb->termmeta . '
			WHERE `term_id`=' . intval($this->term_id) . '
			AND `meta_key`="' . $field->get_name() . '"
			LIMIT 1
		');

		if ( !is_array($value) || count($value) < 1 ) {
			$field->set_value(false);
			return;
		}

		$field->set_value($value[0]);
	}

	function delete(Resume_Field $field) {
		delete_metadata('term', $this->term_id, $field->get_name(), $field->get_value());
	}

	function load_values($field) {
		global $wpdb;

		if ( is_object($field) && is_subclass_of($field, 'Resume_Field') ) {
			$meta_key = $field->get_name();
		} else {
			$meta_key = $field;
		}

		return $wpdb->get_results('
			SELECT meta_key AS field_key, meta_value AS field_value FROM ' . $wpdb->termmeta . '
			WHERE `meta_key` LIKE "' . addslashes($meta_key) . '_%" AND term_id="' . intval($this->term_id) . '"
		', ARRAY_A);
	}

	function delete_values(Resume_Field $field) {
		global $wpdb;

		$group_names = $field->get_group_names();
		$field_name = $field->get_name();

		$meta_key_constraint = '`meta_key` LIKE "' . $field_name . implode('-%" OR `meta_key` LIKE "' . $field_name, $group_names) . '-%"';

		return $wpdb->query('
			DELETE FROM ' . $wpdb->termmeta . '
			WHERE (' . $meta_key_constraint . ') AND term_id="' . intval($this->term_id) . '"
		');
	}

	function set_id($term_id) {
		$this->term_id = $term_id;
	}

	static function on_delete_term($term_id, $tt_id, $taxonomy) {
		global $wpdb;

		return $wpdb->query('
			DELETE FROM ' . $wpdb->termmeta . '
			WHERE `term_id` = "' . intval($term_id) . '"
		');
	}
}

class Resume_DataStore_UserMeta extends Resume_DataStore_Base {
	protected $user_id;

	function init() {}

	function save(Resume_Field $field) {
		if ( !update_user_meta($this->user_id, $field->get_name(), $field->get_value()) ) {
			add_user_meta($this->user_id, $field->get_name(), $field->get_value(), true);
		}
	}

	function load(Resume_Field $field) {
		global $wpdb;

		$value = $wpdb->get_col('
			SELECT `meta_value`
			FROM ' . $wpdb->usermeta . '
			WHERE `user_id`=' . intval($this->user_id) . '
			AND `meta_key`="' . $field->get_name() . '"
			LIMIT 1
		');

		if ( !is_array($value) || count($value) < 1 ) {
			$field->set_value_from_input();
			return;
		}

		$field->set_value($value[0]);
	}

	function delete(Resume_Field $field) {
		delete_user_meta($this->user_id, $field->get_name(), $field->get_value());
	}

	function load_values($field) {
		global $wpdb;

		if ( is_object($field) && is_subclass_of($field, 'Resume_Field') ) {
			$meta_key = $field->get_name();
		} else {
			$meta_key = $field;
		}

		$results = $wpdb->get_results('
			SELECT meta_key AS field_key, meta_value AS field_value FROM ' . $wpdb->usermeta . '
			WHERE `meta_key` LIKE "' . addslashes($meta_key) . '_%" AND `user_id`="' . intval($this->user_id) . '"
		', ARRAY_A);
		if (!$results && is_object($field)) {
			$tmp_field = clone $field;
			$tmp_field->set_value_from_input();

			$values = $tmp_field->get_values();

			foreach ($values as $single_value) {
				foreach ($single_value as $value_field) {
					$results[] = array(
						'field_key' => $value_field->get_name(),
						'field_value' => $value_field->get_value()
					);	
				}
			}
		}

		return $results;
	}

	function delete_values(Resume_Field $field) {
		global $wpdb;

		$group_names = $field->get_group_names();
		$field_name = $field->get_name();

		$meta_key_constraint = '`meta_key` LIKE "' . $field_name . implode('-%" OR `meta_key` LIKE "' . $field_name, $group_names) . '-%"';

		return $wpdb->query('
			DELETE FROM ' . $wpdb->usermeta . '
			WHERE (' . $meta_key_constraint . ') AND `user_id`="' . intval($this->user_id) . '"
		');
	}

	function set_id($user_id) {
		$this->user_id = $user_id;
	}
}