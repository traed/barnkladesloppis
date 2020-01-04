<?php
	namespace eqhby\bkl;
	
	class Admin extends Plugin {

		static $notices = array();  

		protected function init() {
			add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
			add_action('init', array($this, 'handle_post'));
			add_action('init', array($this, 'load_notices'), 20);
			add_action('admin_notices', array($this, 'notices_html'));
			add_action('admin_menu', array($this, 'add_menu_page'));

			add_filter('manage_bkl_occasion_posts_columns', Helper::callback('Occasion', 'add_custom_columns'));
			add_filter('manage_edit-bkl_occasion_sortable_columns', Helper::callback('Occasion', 'sortable_columns'));
			add_action('manage_bkl_occasion_posts_custom_column' , Helper::callback('Occasion', 'custom_column_data'), 10, 2);
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


		public function load_notices() {
			self::$notices = Session::get_once('notices') ?: array();
		}


		public function notices_html() {
			if(!empty(self::$notices)) {
				foreach(self::$notices as $notice) {
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


		public function add_menu_page() {
			add_submenu_page('edit.php?post_type=bkl_occasion', 'Användare', 'Användare', 'edit_bkl_occasions', 'bkl_users', Helper::callback('Users', 'init'));
			add_submenu_page('edit.php?post_type=bkl_occasion', 'Inställningar', 'Inställningar', 'edit_bkl_occasions', 'bkl_settings', Helper::callback('Settings', 'init'));
		}


		public function load_admin_scripts() {
			$screen = get_current_screen();

			if(strpos($screen->id, 'bkl_occasion') !== false) {
				wp_enqueue_script(Plugin::SLUG . '-js', Plugin::get_url() . '/assets/js/admin-script.js', ['jquery'], Plugin::VERSION, true);
				wp_enqueue_style(Plugin::SLUG . '-css', Plugin::get_url() . '/assets/css/admin.css', [], Plugin::VERSION);
			}
		}
	}
	
	new Admin();