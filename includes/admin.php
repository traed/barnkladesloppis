<?php
	namespace eqhby\bkl;
	
	class Admin extends Plugin {

		protected function init() {
			add_action('admin_menu', array($this, 'add_admin_menu_item'));
			add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
			add_action('init', array($this, 'handle_post'));
			add_action('admin_notices', array($this, 'notices_html'));
		}


		static public function notice($message, $type = 'info') {
			$message = sanitize_text_field( $message );
			$type = sanitize_text_field( $type );

			if(!in_array($type, array('info', 'warning', 'error', 'success'), true)) return;

			Session::set('notices', [[
				'type' => $type,
				'message' => $message
			]]);
		}


		public function notices_html() {
			$notices = Session::get_once('notices');

			if(!empty($notices)) {
				foreach($notices as $notice) {
					printf('<div class="%1$s"><p>%2$s</p></div>', 'notice notice-' . $notice['type'] . ' is-dismissable', $notice['message']); 
				}
			}
		}
		
		
		public function handle_post() {
			if(!isset($_POST['controller'])) return;
			$controller = Helper::get_controller($_POST['controller']);
			$action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : null;
			if(method_exists($controller, 'handle_post')) $controller->handle_post($action);
		}


		public function add_admin_menu_item() {
			add_menu_page('Barnklädesloppis', 'Barnklädesloppis', 'manage_options', self::SLUG, Helper::callback('Settings', 'show_settings_page'));
			add_submenu_page(self::SLUG . '-bkl', 'Add new', 'Add new', 'manage_options', self::SLUG . '-add-new', Helper::callback('Settings', 'show_add_new_page'));
		}


		public function load_admin_scripts() {
			// $screen = get_current_screen();

			// Load scripts based on screen->id
		}
	}
	
	new Admin();