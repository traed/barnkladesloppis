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
			wp_redirect('/wp-admin');
			exit;
		} elseif(is_user_logged_in()) {
			$this->show_logged_in_no_role();
		} else {
			include(Plugin::PATH . '/app/views/frontend/login.php');
		}
	}


	protected function show_logged_in_seller() {
		$posts = Occasion::get_future();
		$title = 'Barnklädesloppis Säljare';
		$this->set_title($title);

		include(Plugin::PATH . '/app/views/frontend/start.php');
	}


	protected function show_logged_in_no_role() {
		$posts = Occasion::get_future();
		$title = 'Barnklädesloppis Inget konto';
		$this->set_title($title);

		include(Plugin::PATH . '/app/views/frontend/start.php');
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

			$user = get_user_by('ID', $user_id);
			$user->set_role('bkl_seller');

			wp_update_user([
				'ID' => $user_id,
				'display_name' => $user->get('first_name') . ' ' . $user->get('last_name')
			]);

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