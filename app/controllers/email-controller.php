<?php

namespace eqhby\bkl;

use Exception;

class Email_Controller extends Controller {

	public function init() {
		global $wpdb;

		$all_sellers = get_users([
			'role' => 'bkl_seller',
			'orderby' => 'display_name',
			'order' => 'ASC'
		]);

		$num_queued_messages = Mailer::count(Mailer::STATUS_ENQUEUED);
		$num_messages = Mailer::count(Mailer::STATUS_PENDING) + $num_queued_messages;

		$messages = $wpdb->get_results($wpdb->prepare('
			SELECT COUNT(recipient) AS recipients, message_id, subject, status, time_created
			FROM ' . Helper::get_table('emails') . '
			WHERE status != %s
			GROUP BY message_id, status
			ORDER BY time_created ASC',
			Mailer::STATUS_SENT
		), ARRAY_A);

		include(Plugin::PATH . '/app/views/admin/email.php');
	}


	public function handle_post($action) {
		if(!wp_verify_nonce($_POST['_wpnonce'], 'bkl_send_email')) {
			Admin::notice('Kunde inte verifiera användare. Prova att ladda om sidan.', 'error');
			return;
		}

		if($action === 'enqueue') {
			try {
				$to = $this->get_recipients();
				if(empty($to)) {
					Admin::notice('Inga mottagare valda.', 'error');
					return;
				}

				$subject = sanitize_text_field($_POST['subject'] ?? 'Barnklädesloppis');
				$message = apply_filters('the_content', wp_kses_post($_POST['message']));

				if(strlen($subject) < 1) {
					Admin::notice('Du måste ange ett ämne.', 'error');
					return;
				}

				if(strlen($message) < 1) {
					Admin::notice('Du måste ange ett meddelande.', 'error');
					return;
				}
	
				$mailer = new Mailer();
				$success = $mailer->enqueue($to, $subject, $message);

				if($success) {
					Admin::notice('Meddelanden har lagt till i kön. Klicka på "Skicka köade" för att börja skicka.', 'success');
				} else {
					Admin::notice('Meddelanden har lagt till i kön, men vissa fel inträffade. Kontrollera systemloggen för mer info.', 'warning');
				}

			} catch(Exception $e) {
				Log::error($e->getMessage());
				Log::error($e->getTrace());
				Admin::notice('Något gick fel. Se systemloggen för mer info.', 'error');
			}

			wp_safe_redirect($_POST['_wp_http_referer']);
			exit;
		}

		else if($action === 'send') {
			$message_id = Mailer::get_next_batch_id(Mailer::STATUS_ENQUEUED);
			Mailer::set_batch_status($message_id, Mailer::STATUS_PENDING);

			Admin::notice('Köade meddelanden har börjat skickas. Detta kan ta några minuter.', 'success');

			wp_safe_redirect($_POST['_wp_http_referer']);
			exit;
		}

		else if($action === 'clear') {
			Mailer::clear_queue();

			Admin::notice('Köade meddelanden har tagits bort.', 'success');

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
				'email' => (string)$user->get('user_email'),
				'first' => (string)$user->get('first_name'),
				'last' => (string)$user->get('last_name'),
				'seller_id' => (string)$user->get('seller_id') ?: '0'
			];
		}

		return $recipients;
	}
}