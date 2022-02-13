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
			add_action('pre_get_posts', array($this, 'edit_only_bkl_pages'));

			add_action('delete_user', array($this, 'cleanup_occasion_users'));

			add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
			add_action('save_post', array($this, 'save_meta_fields'));
			add_action('new_to_publish', array($this, 'save_meta_fields'));
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
			add_submenu_page('edit.php?post_type=bkl_occasion', 'E-postutskick', 'E-postutskick', 'edit_bkl_occasions', 'bkl_email', Helper::callback('Email', 'init'));
			add_submenu_page('edit.php?post_type=bkl_occasion', 'Inställningar', 'Inställningar', 'edit_bkl_occasions', 'bkl_settings', Helper::callback('Settings', 'init'));
		}


		public function load_admin_scripts() {
			$screen = get_current_screen();

			if(strpos($screen->id, 'bkl_occasion') !== false) {
				wp_enqueue_script(Plugin::SLUG . '-js', Plugin::get_url() . '/assets/js/admin-script.js', ['jquery'], Plugin::VERSION, true);
				wp_enqueue_style(Plugin::SLUG . '-css', Plugin::get_url() . '/assets/css/admin.css', [], Plugin::VERSION);
			}
		}


		public function cleanup_occasion_users($user_id) {
			global $wpdb;

			$wpdb->delete(Helper::get_table('occasion_users'), ['user_id' => $user_id]);
		}


		public function edit_only_bkl_pages($query) {
			static $allowed;

			if(!isset($allowed) && is_admin() && !current_user_can('administrator') && current_user_can('bkl_admin')) {
				require_once(ABSPATH . 'wp-admin/includes/screen.php');

				$screen = get_current_screen();
				$allowed = 0;

				if($screen->id === 'page') {
					global $post_id;
					$template = (int)get_post_meta($post_id, '_wp_page_template', true);
					if(!in_array($template, array_keys($this->templates))) {
						wp_redirect(get_admin_url());
						exit;
					}
				}

				if($screen->id === 'edit-page') {
					$meta_query = $query->get('meta_query') ?: [];
					$meta_query[] = [
						'key' => '_wp_page_template',
						'value' => array_keys($this->templates),
						'compare' => 'IN'
					];
					$query->set('meta_query', $meta_query);
					$allowed = 1;
				}
			}
		}


		public function add_custom_meta_box() {
			add_meta_box('bkl_settings', 'Inställningar', Helper::callback('Occasion', 'occasion_settings_callback'), 'bkl_occasion', 'side');
			add_meta_box('bkl_occasion_users', 'Anmälda användare', Helper::callback('Occasion', 'occasion_users_callback'), 'bkl_occasion');
			add_meta_box('bkl_occasion_reserves', 'Väntelista', Helper::callback('Occasion', 'occasion_reserve_callback'), 'bkl_occasion');
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
			$date_signup_close = strtotime($_POST['date_signup_close']) ? $_POST['date_signup_close'] : '';
			$date_turnin = strtotime($_POST['date_turnin']) ? $_POST['date_turnin'] : '';
	
			update_post_meta($post_id, 'date_start', $date_start);
			update_post_meta($post_id, 'date_signup', $date_signup);
			update_post_meta($post_id, 'date_signup_close', $date_signup_close);
			update_post_meta($post_id, 'date_turnin', $date_turnin);
			update_post_meta($post_id, 'num_spots', (int)$_POST['num_spots']);
			update_post_meta($post_id, 'seller_fee', (int)$_POST['seller_fee']);
		}
	}
	
	new Admin();