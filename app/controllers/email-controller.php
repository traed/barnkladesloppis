<?php

namespace eqhby\bkl;

use Exception;

class Email_Controller extends Controller {

	public function init() {
		$all_sellers = get_users([
			'role' => 'bkl_seller',
			'orderby' => 'display_name',
			'order' => 'ASC'
		]);

		include(Plugin::PATH . '/app/views/admin/email.php');
	}


	public function handle_post($action) {
		if($action === 'send' && wp_verify_nonce($_POST['_wpnonce'], 'bkl_send_email')) {
			try {
				$to = $this->get_recipients();
				if(empty($to)) {
					Admin::notice('Inga mottagare valda.', 'error');
					return;
				}

				$message = apply_filters('the_content', wp_kses_post($_POST['message']));
	
				$mailer = new Mailer();
				$mailer->send($to, 'Barnklädesloppis', $message);

				Admin::notice('Meddelande skickat!', 'success');
			} catch(Exception $e) {
				Log::error($e->getMessage());
				Log::error($e->getTrace());
				Admin::notice('Något gick fel. Se systemloggen för mer info.', 'error');
			}

			wp_safe_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}


	private function get_recipients() {
		if(empty($_POST['recipients'])) return [];

		$recipients = [];
		$users = [];
		$code = sanitize_key($_POST['recipients']);

		if($code === 'all') {
			$users = get_users([
				'role' => 'bkl_seller',
				'orderby' => 'display_name',
				'order' => 'ASC'
			]);
		} elseif(strpos($code, 'occasion') === 0) {
			$occasion_id = (int)str_replace('occasion_', '', $code);
			$occasion = Occasion::get_by_id($occasion_id);
			
			$status = sanitize_key($_POST['user_status']);
			if($status === 'all') $status = false;

			$users = $occasion->get_users($status);
		} elseif(strpos($code, 'seller') === 0) {
			$user_id = (int)str_replace('seller_', '', $code);
			$users = get_users(['include' => [$user_id]]);
		}

		foreach($users as $user) {
			$recipients[] = [
				'email' => $user->get('user_email'),
				'first' => $user->get('first_name'),
				'last' => $user->get('last_name'),
				'seller_id' => $user->get('seller_id') ?: '0'
			];
		}

		return $recipients;
	}
}