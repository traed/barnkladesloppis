<?php
/*
	Plugin Name: Barnklädesloppis
	Description: Plugin som hanterar barnklädesloppisens anmälningssystem.
	Version: 1.0.0
	Author: Mattias Forsman
	Author URI: https://github.com/traed
*/

namespace eqhby\bkl;

abstract class Plugin {
	const VERSION = '1.0.0';
	const SLUG = 'bkl';
	const TABLE_PREFIX = 'bkl_';
	const FILE = __FILE__;
	const PATH = __DIR__;
	const EMAIL_ADDRESS = '';
	const MAIN_LANG = 'en';
	
	
	static public function get_url() {
		return plugin_dir_url(self::FILE);
	}
	
	
	public function __construct() {
		// Create/update database tables on activation
		register_activation_hook(self::FILE, array($this, 'install'));
		register_deactivation_hook(self::FILE, array($this, 'uninstall'));
		
		// Autoload all classes
		spl_autoload_register(array($this, 'autoloader'));

		// Composer
		if(file_exists(self::PATH . '/vendor/autoload.php')) {
			require_once(self::PATH . '/vendor/autoload.php');
		}

		add_action('init', array($this, 'add_custom_post_type'));
		add_action('init', array($this, 'add_custom_shortcode'));
		add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
		add_action('save_post', array($this, 'save_meta_fields'));
		add_action('new_to_publish', array($this, 'save_meta_fields'));
		add_filter('login_redirect', array($this, 'redirect_login'), 10, 3);

		add_filter('register_url', array($this, 'register_url'));
		add_action('login_form_register', array($this, 'redirect_register'));
		
		$this->init();
	}


	public function install() {
		add_role(
			'bkl_admin',
			'Loppis-admin',
			[
				'read' => true,
				'read_bkl_occasions' => true,
				'edit_bkl_occasions' => true,
				'edit_others_bkl_occasions' => true,
				'publish_bkl_occasions' => true,
				'read_private_bkl_occasions' => true,
				'delete_bkl_occasions' => true,
				'delete_private_bkl_occasions' => true,
				'delete_published_bkl_occasions' => true,
				'delete_others_bkl_occasions' => true,
				'edit_private_bkl_occasions' => true,
				'edit_published_bkl_occasions' => true,

				'read_pages' => true,
				'edit_pages' => true,
				'edit_others_pages' => true,
				'publish_pages' => true,
				'read_private_pages' => false,
				'delete_pages' => false,
				'delete_private_pages' => false,
				'delete_published_pages' => false,
				'delete_others_pages' => false,
				'edit_private_pages' => false,
				'edit_published_pages' => true,
			]
		);
		add_role(
			'bkl_seller',
			'Loppis-säljare'
		);

		if(!get_option('bkl_locked_numbers')) {
			add_option('bkl_locked_numbers', [62, 68, 74, 80, 86, 92, 98, 100, 104, 110, 116, 120, 128, 130, 134, 140, 158, 164, 170]);
		}

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$tables = [];
		$charset = 'DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci';
		
		$tables[] = '
			CREATE TABLE IF NOT EXISTS ' . Helper::get_table('occasion_users') . ' (
				occasion_id int NOT NULL,
				user_id int NOT NULL,
				time_created datetime NOT NULL,
				time_updated datetime NOT NULL,
				status varchar(16) NOT NULL,
				PRIMARY KEY (occasion_id, user_id)
		) ' . $charset;
			
		foreach($tables as $table) {
			dbDelta($table);
		}
	}


	public function uninstall() {
		remove_role('bkl_admin');
		remove_role('bkl_seller');

		delete_option('bkl_locked_numbers');
	}
	
	
	protected function autoloader($name) {
		if(strncmp($name, __NAMESPACE__, strlen(__NAMESPACE__)) !== 0) return;
		
		$classname = trim(substr($name, strlen(__NAMESPACE__)), '\\');
		$filename = strtolower(str_replace('_', '-', $classname));
		
		$paths = array(
			self::PATH . '/app/models/' . $filename . '.php',
			self::PATH . '/app/models/collections/' . $filename . '.php',
			self::PATH . '/app/helpers/' . $filename . '.php'
		);
		foreach($paths as $path) {
			if(!file_exists($path)) continue;
			
			include($path);
		}
	}


	public function add_custom_post_type() {
		register_post_type('bkl_occasion', [
			'labels' => [
				'name'                     => 'Loppisar',
				'singular_name'            => 'Loppis',
				'add_new'                  => 'Skapa ny',
				'add_new_item'             => 'Skapa ny loppis',
				'new_item'                 => 'Ny loppis',
				'edit_item'                => 'Redigera loppis',
				'view_item'                => 'Visa loppis',
				'all_items'                => 'Alla loppisar',
				'search_items'             => 'Sök loppisar',
				'not_found'                => 'Hittade inga loppisar',
				'not_found_in_trash'       => 'Inga loppisar hittades i papperskorgen.'
			],
			'description' => 'Ett loppistillfälle',
			'show_ui' => true,
			'show_in_menu' => true,
			// 'show_in_rest' => true,
			'exclude_from_search' => true,
			'map_meta_cap' => true,
			'capability_type' => 'bkl_occasion',
			'supports' => ['title']
		]);
	}


	public function add_custom_meta_box() {
		add_meta_box('bkl_settings', 'Inställningar', Helper::callback('Occasion', 'occasion_settings_callback'), 'bkl_occasion', 'side');
		add_meta_box('bkl_occasion_users', 'Anmälda användare', Helper::callback('Occasion', 'occasion_users_callback'), 'bkl_occasion');
		add_meta_box('bkl_occasion_reserves', 'Väntelista', Helper::callback('Occasion', 'occasion_reserve_callback'), 'bkl_occasion');

		add_meta_box('allow_bkl_admin', 'Barnklädesloppis', Helper::callback('Page', 'allow_admin_callback'), 'page', 'side', 'low');
	}


	public function save_meta_fields($post_id) {
		if(!isset($_POST['bkl_metabox_nonce']) || !wp_verify_nonce($_POST['bkl_metabox_nonce'], 'bkl_occasion_save')) {
			return 'nonce not verified';
		}

		if(wp_is_post_autosave($post_id)) {
			return 'autosave';
		}
	
	  	if(wp_is_post_revision($post_id)) {
			return 'revision';
		}

		$date_start = strtotime($_POST['date_start']) ? $_POST['date_start'] : '';
		$date_signup = strtotime($_POST['date_signup']) ? $_POST['date_signup'] : '';

		update_post_meta($post_id, 'date_start', $date_start);
		update_post_meta($post_id, 'date_signup', $date_signup);
		update_post_meta($post_id, 'num_spots', (int)$_POST['num_spots']);
		update_post_meta($post_id, 'seller_fee', (int)$_POST['seller_fee']);
		if(empty($_POST['allow_bkl_admin'])) {
			delete_post_meta($post_id, 'allow_bkl_admin');
		} else {
			update_post_meta($post_id, 'allow_bkl_admin', (int)$_POST['allow_bkl_admin']);
		}
	}


	public function add_custom_shortcode() {
		add_shortcode('barnkladesloppis', Helper::callback('Frontend', 'shortcode'));
	}


	public function redirect_login($redirect_to, $requested_redirect_to, $user) {
		if(!is_wp_error($user) && in_array('bkl_seller', $user->roles)) {
			$redirect_to = '/loppis';
		}

		return $redirect_to; 
	}


	public function register_url() {
        return home_url('/loppis/reg');
	}
	

	public function redirect_register() {
        wp_redirect(wp_registration_url());
        exit;
    }
	
	
	protected function init() {
		// Should be overriden by extending class
	}
}

if(is_admin()) {
	require_once(Plugin::PATH . '/includes/admin.php');
}
else {
	require_once(Plugin::PATH . '/includes/frontend.php');
}