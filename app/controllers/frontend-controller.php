<?php

namespace eqhby\bkl;

class Frontend_Controller extends Controller {
	public function __construct() {
		parent::__construct();

		$this->add_body_class('bkl');

		do_action('bkl_frontend');
	}


	public function init() {
		$this->handle_post();

		$user = wp_get_current_user();
		if(in_array('bkl_seller', $user->roles)) {
			$this->show_logged_in_seller();
		} elseif(in_array('bkl_admin', $user->roles)) {
			$this->show_logged_in_admin();
		} elseif(is_user_logged_in()) {
			$this->show_logged_in_no_role();
		} else {
			include(Plugin::PATH . '/app/views/frontend/login.php');
		}
	}


	protected function show_logged_in_seller() {
		$posts = $this->get_occasions();
		$title = 'Barnklädesloppis Säljare';
		$this->set_title($title);

		include(Plugin::PATH . '/app/views/frontend/start.php');
	}


	protected function show_logged_in_admin() {
		$posts = $this->get_occasions();
		$title = 'Barnklädesloppis Admin';
		$this->set_title($title);

		include(Plugin::PATH . '/app/views/frontend/start.php');
	}


	protected function show_logged_in_no_role() {
		$posts = $this->get_occasions();
		$title = 'Barnklädesloppis Inget konto';
		$this->set_title($title);

		include(Plugin::PATH . '/app/views/frontend/start.php');
	}


	protected function get_occasions() {
		$posts = get_posts([
			'post_type' => 'bkl_occasion',
			'post_status' => 'publish',
			'meta_key' => 'date_start',
			'meta_compare' => '>=',
			'meta_value' => Helper::date('now')->format('Y-m-d'),
			'orderby' => 'meta_value',
			'order' => 'ASC'
		]);

		return $posts;
	}


	protected function handle_post() {
		if(isset($_POST['action']) && $_POST['action'] === 'register' && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'bkl_register')) {
			$first_name = sanitize_text_field($_POST['first_name']);
			$last_name = sanitize_text_field($_POST['last_name']);
			$email = sanitize_email($_POST['email']);
			$phone = sanitize_text_field($_POST['phone']);
			
			if(empty($_POST['password'])) {
				throw new Problem('Invalid password.');
			}

			$user_id = wp_create_user($email, $_POST['password'], $email);
			if(is_wp_error($user_id)) {
				throw new Problem('Invalid input.');
			}

			update_user_meta($user_id, 'first_name', $first_name);
			update_user_meta($user_id, 'last_name', $last_name);
			update_user_meta($user_id, 'phone', $phone);

			wp_signon([
				'user_login' => $email,
				'user_password' => $_POST['password']
			]);

			wp_redirect('/loppis');
			exit;
		}
	}
}