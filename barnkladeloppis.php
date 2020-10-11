<?php
/*
	Plugin Name: Barnklädesloppis
	Description: Plugin som hanterar barnklädesloppisens anmälningssystem.
	Version: 1.0.1
	Author: Mattias Forsman
	Author URI: https://github.com/traed
*/

namespace eqhby\bkl;

abstract class Plugin {
	const VERSION = '1.0.1';
	const SLUG = 'bkl';
	const TABLE_PREFIX = 'bkl_';
	const FILE = __FILE__;
	const PATH = __DIR__;
	const EMAIL_ADDRESS = '';
	const MAIN_LANG = 'en';

	protected $templates;
	
	
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
		
		add_filter('login_redirect', array($this, 'redirect_login'), 10, 3);
		add_filter('register_url', array($this, 'register_url'));
		add_action('login_form_register', array($this, 'redirect_register'));

		// Add custom page template
		add_filter('theme_page_templates', array($this, 'add_new_template'));
		add_filter('wp_insert_post_data', array($this, 'register_project_templates'));
		add_filter('template_include', array($this, 'view_project_template'));

		// Add your templates to this array.
		$this->templates = array(
			'app/views/frontend/bkl-page-template.php' => 'Loppis-sida'
		);
		
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
				'upload_files' => true,
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


	public function add_new_template($posts_templates) {
		return array_merge($posts_templates, $this->templates);
	}


	public function register_project_templates( $atts ) {
		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if(empty($templates)) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete($cache_key, 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge($templates, $this->templates);

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add($cache_key, $templates, 'themes', 1800);

		return $atts;
	}


	public function view_project_template($template) {
		global $post;

		if(!$post) {
			return $template;
		}

		$template_name = get_post_meta($post->ID, '_wp_page_template', true);
		if(!isset($this->templates[$template_name])) {
			return $template;
		}

		$file = plugin_dir_path(self::FILE) . $template_name;

		if(file_exists($file)) {
			return $file;
		}

		return $template;
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