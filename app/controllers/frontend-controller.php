<?php

namespace eqhby\bkl;

class Frontend_Controller extends Controller {
	public function __construct() {
		parent::__construct();

		$this->add_body_class('bkl');
		
		do_action('bkl_frontend');
	}


	public function login() {
		if(is_user_logged_in()) {
			wp_redirect('/loppis');
		} else {
			wp_redirect('/wp-login.php');
		}

		exit;
	}


	public function register() {
		add_action('wp_footer', function() {
			if($key = get_option('bkl_recaptcha_site_key')) {
				echo '
					<script src="https://www.google.com/recaptcha/api.js?render=' . $key . '"></script>
					<script>
					(function() {
						function getToken() {
							grecaptcha.execute("' . $key . '", {action: "register"}).then(function(token) {
								var token_field = document.getElementById("recaptcha_token");
								token_field.value = token;
							});
						}

						grecaptcha.ready(function() {
							getToken();
						});

						setInterval(getToken, 90000);
					})();
					</script>
				';
			}
		});

		$this->handle_post_register();

		if(is_user_logged_in()) {
			wp_redirect('/loppis');
			exit;
		}

		include(Plugin::PATH . '/app/views/frontend/register.php');
	}


	public function init() {
		$user = is_user_logged_in() ? wp_get_current_user() : false;

		if($user) {
			if(!in_array('bkl_seller', $user->roles) && !in_array('bkl_admin', $user->roles)) {
				$user->add_role('bkl_seller');
			}
			$this->show_logged_in_seller();
		} else {
			$this->show_loppis_logged_out();
		}
	}


	protected function show_logged_in_seller() {
		$occasions = Occasion::get_future();
		$current_user = wp_get_current_user();
		$status = 'none';
		if($next_occasion = Occasion::get_next()) {
			$status = $next_occasion->get_user_status($current_user->ID);
		}

		include(Plugin::PATH . '/app/views/frontend/start.php');
	}


	protected function show_loppis_logged_out() {
		$occasions = Occasion::get_future();

		include(Plugin::PATH . '/app/views/frontend/start.php');
	}


	protected function handle_post_register() {
		if(isset($_POST['action']) && $_POST['action'] === 'register' && !empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'bkl_register')) {
			$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = get_option('bkl_recaptcha_secret');
			$recaptcha_response = $_POST['recaptcha_token'];
		
			// Make and decode POST request:
			$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
			$recaptcha = json_decode($recaptcha);

			if(!$recaptcha || $recaptcha->success !== true) {
				Session::set('registration_error', 'recaptcha_failed');
				wp_redirect('/loppis/reg');
				exit;
			}

			if($_POST['password'] !== $_POST['password_confirm']) {
				Session::set('registration_error', 'passwords_dont_match');
				return;
			}

			$user_data = [
				'first_name' => sanitize_text_field($_POST['first_name']),
				'last_name' => sanitize_text_field($_POST['last_name']),
				'email' => sanitize_email($_POST['email']),
				'phone' => sanitize_text_field($_POST['phone']),
				'has_swish' => (int)$_POST['has_swish'],
				'password' => $_POST['password']
			];

			$error = array_filter($user_data, 'empty');
			if(!empty($error)) {
				Session::set('registration_error', 'empty_input');
			}

			$user_id = wp_create_user($user_data['email'], $user_data['password'], $user_data['email']);
			if(is_wp_error($user_id)) {
				throw new Problem('Invalid input.');
			}

			$user = get_user_by('ID', $user_id);
			$user->set_role('bkl_seller');

			wp_update_user([
				'ID' => $user_id,
				'display_name' => $user_data['first_name'] . ' ' . $user_data['last_name']
			]);

			update_user_meta($user_id, 'first_name', $user_data['first_name']);
			update_user_meta($user_id, 'last_name', $user_data['last_name']);
			update_user_meta($user_id, 'phone', $user_data['phone']);
			update_user_meta($user_id, 'has_swish', $user_data['has_swish']);

			wp_signon([
				'user_login' => $user_data['email'],
				'user_password' => $user_data['password']
			]);

			wp_redirect('/loppis');
			exit;
		}
	}


	public function handle_post() {
		if(!is_user_logged_in() || empty($_POST['action']) || !in_array($_POST['action'], ['sign_up', 'resign', 'edit_user'])) return;
		
		if(get_option('bkl_enable_sign_up', false)) {
			$this->handle_post_sign_up();
		}

		$this->handle_post_resign();
		$this->handle_post_edit_user();
	}


	protected function handle_post_sign_up() {
		if(isset($_POST['action']) && $_POST['action'] === 'sign_up' && !empty($_POST['bkl_sign_up_nonce']) && wp_verify_nonce($_POST['bkl_sign_up_nonce'], 'bkl_sign_up')) {
			if(is_user_logged_in() && !empty($_POST['occasion_id'])) {
				$occasion = Occasion::get_by_id((int)$_POST['occasion_id']);
				$occasion->add_user(get_current_user_id());
			}

			wp_redirect('/loppis');
			exit;
		}
	}


	protected function handle_post_resign() {
		if(isset($_POST['action']) && $_POST['action'] === 'resign' && !empty($_POST['bkl_resign_nonce']) && wp_verify_nonce($_POST['bkl_resign_nonce'], 'bkl_resign')) {
			if(is_user_logged_in() && !empty($_POST['occasion_id'])) {
				$occasion = Occasion::get_by_id((int)$_POST['occasion_id']);
				$occasion->add_user(get_current_user_id(), 'none');
			}

			wp_redirect('/loppis');
			exit;
		}
	}


	protected function handle_post_edit_user() {
		if(isset($_POST['action']) && $_POST['action'] === 'edit_user' && !empty($_POST['bkl_edit_user_nonce']) && wp_verify_nonce($_POST['bkl_edit_user_nonce'], 'bkl_edit_user')) {
			if($user_id = get_current_user_id()) {
				$first_name = sanitize_text_field($_POST['first_name']);
				$last_name = sanitize_text_field($_POST['last_name']);
				$email = sanitize_email($_POST['email']);
				$phone = sanitize_text_field($_POST['phone']);
				$has_swish = (int)$_POST['has_swish'];

				update_user_meta($user_id, 'first_name', $first_name);
				update_user_meta($user_id, 'last_name', $last_name);
				update_user_meta($user_id, 'phone', $phone);
				update_user_meta($user_id, 'has_swish', $has_swish);

				$data = [
					'ID' => $user_id,
					'display_name' => $first_name . ' ' . $last_name
				];
				if($email) {
					$data['user_email'] = $email;
				}

				wp_update_user($data);
			}

			wp_redirect('/loppis');
			exit;
		}
	}
}