<?php

namespace eqhby\bkl;

class Users_Controller extends Controller {

	public function init() {
		if(isset($_GET['id']) && is_numeric($_GET['id'])) {
			$this->show_single_user((int)$_GET['id']);
		} else {
			$this->show_all_users();
		}
	}


	private function show_all_users() {
		$orderby = 'user_email';
		$meta_key = 'first_name';
		$order = 'ASC';
		$page = 1;

		if(isset($_GET['orderby'])) {
			$orderby = sanitize_key($_GET['orderby']);

			if(in_array($orderby, ['first_name', 'last_name'])) {
				$meta_key = $orderby;
				$orderby = 'meta_value';
			}

			if(isset($_GET['order'])) {
				$order = sanitize_key($_GET['order']);
			}
		}

		if(isset($_GET['paged'])) {
			$page = (int)$_GET['paged'];
		}

		$query = [
			'role__in' => ['bkl_admin', 'bkl_seller'],
			'orderby' => $orderby,
			'order' => $order,
			'number' => 50,
			'paged' => $page
		];

		if($orderby === 'meta_value') {
			$query['meta_key'] = $meta_key;
		}

		$users = get_users($query);

		include(Plugin::PATH . '/app/views/admin/users.php');
	}


	private function show_single_user($id) {
		$user = get_user_by('ID', $id);

		include(Plugin::PATH . '/app/views/admin/single-user.php');
	}


	public function handle_post($action) {
		if($action === 'edit_user' && isset($_POST['original_email']) && wp_verify_nonce($_POST['_wpnonce'], 'bkl_edit_user')) {
			$original_email = sanitize_email($_POST['original_email']);
			$user = get_user_by_email($original_email);

			if($user) {
				update_usermeta($user->ID, 'first_name', sanitize_text_field($_POST['first_name']));
				update_usermeta($user->ID, 'last_name', sanitize_text_field($_POST['last_name']));
				update_usermeta($user->ID, 'phone', sanitize_text_field($_POST['phone']));

				$role = sanitize_key($_POST['role']);
				if($GLOBALS['wp_roles']->is_role($role)) {
					$user->set_role($role);
				}

				$email = sanitize_email($_POST['email']);
				if($email !== $original_email) {
					if(email_exists($email)) {
						Admin::notice('E-postadressen finns redan i systemet. Prova med en annan.', 'warning');
					} else {
						wp_update_user([
							'ID' => $user->ID,
							'user_email' => $email
						]);
					}
				}

				Admin::notice('Ändringarna har sparats!', 'success');
			}

			wp_safe_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
}