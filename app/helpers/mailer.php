<?php

namespace eqhby\bkl;

use Exception;
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Variable;

class Mailer {
	const FROM_NAME = 'Barnklädesloppis';
	const FROM_EMAIL = 'loppis@equmeniahasselby.se';
	const STATUS_ENQUEUED = 'enqueued';
	const STATUS_PENDING = 'pending';
	const STATUS_SENT = 'sent';

	private $client;

	public function __construct() {
		$api_key = get_option('bkl_email_api_key');

		if($api_key) {
			$this->client = new MailerSend(['api_key' => $api_key]);
		} else {
			throw new Problem('Kunde inte ansluta till eposttjänsten.');
		}
	}


	/**
	 * @param array $to Array with the following keys: email, first, last, seller_id
	 * @param string $subject
	 * @param string $message A HTML formated message
	 * 
	 * @return bool Returns true if successful or false if there were any errors.
	 */
	public function enqueue(array $to, string $subject, string $message): bool {
		global $wpdb;

		$message_id = $wpdb->get_var('SELECT MAX(message_id) + 1 FROM ' . Helper::get_table('emails'));
		$errors = false;

		foreach($to as $recipient) {
			$data = [
				'message_id'   => $message_id,
				'status'       => 'enqueued',
				'recipient'    => serialize($recipient),
				'subject'      => $subject,
				'message'      => $message,
				'time_created' => Helper::date('now')->format('Y-m-d H:i:s')
			];
			$result = $wpdb->insert(Helper::get_table('emails'), $data, ['%s', '%s', '%s', '%s']);

			if(!$result) {
				$errors = true;
				Log::email('Failed to enqueue email.');
				Log::email($data);
			}
		}

		return !$errors;
	}


	public function send(array $batch): void {
		foreach($batch as $email) {
			try {
				$recipient = new Recipient(
					$email['recipient']['email'],
					$email['recipient']['first'] . ' ' . $email['recipient']['last']
				);

				$variables = new Variable(
					$email['recipient']['email'],
					$email['recipient']
				);

				$message = (new EmailParams())
					->setFrom(self::FROM_EMAIL)
					->setFromName(self::FROM_NAME)
					->setRecipients([$recipient])
					->setSubject($email['subject'])
					->setHtml($email['message'])
					->setText(wp_strip_all_tags($email['message']))
					->setVariables([$variables]);
		
				$this->client->email->send($message);
			} catch(Exception $e) {
				Log::email($e->getMessage());
			}
		}
	}


	public function prepare_next_batch(): array {
		global $wpdb;

		$message_id = self::get_next_batch_id(self::STATUS_PENDING);

		$results = $wpdb->get_results($wpdb->prepare('
			SELECT id, recipient, subject, message
			FROM ' . Helper::get_table('emails') . ' 
			WHERE message_id = %d AND status = %s
			LIMIT 100
		', $message_id, 'pending'), ARRAY_A);

		$return = [];
		$ids = [];

		if(!empty($results)) {
			foreach($results as $result) {
				$ids[] = (int)$result['id'];
				$return[] = [
					'recipient' => unserialize($result['recipient']),
					'subject'   => $result['subject'],
					'message'   => $result['message']
				];
			}
			
			$wpdb->query($wpdb->prepare(
				'UPDATE ' . Helper::get_table('emails') . '
				SET status = %s, time_sent = %s
				WHERE id IN (' . implode(',', $ids) . ')',
				self::STATUS_SENT,
				Helper::date('now')->format('Y-m-d H:i:s')
			));
		}

		return $return;
	}


	static public function count(string $status): int {
		global $wpdb;

		return (int)$wpdb->get_var($wpdb->prepare('
			SELECT COUNT(*)
			FROM ' . Helper::get_table('emails') . '
			WHERE status = %s',
			$status
		));
	}


	static public function set_batch_status(int $message_id, string $status): void {
		global $wpdb;

		$wpdb->update(
			Helper::get_table('emails'),
			['status' => $status],
			['message_id' => $message_id],
			['%s'],
			['%d']
		);
	}


	static public function get_next_batch_id(string $status): int {
		global $wpdb;

		return (int)$wpdb->get_var($wpdb->prepare('
			SELECT MIN(message_id) 
			FROM ' . Helper::get_table('emails') . ' 
			WHERE status = %s
		', $status));
	}


	static public function clear_queue(): void {
		global $wpdb;

		$wpdb->delete(
			Helper::get_table('emails'),
			[
				'status' => self::STATUS_ENQUEUED
			],
			['%s']
		);
	}


	static public function get_status_label(string $status): string {
		switch($status) {
			case self::STATUS_ENQUEUED:
				return 'Köad';
			case self::STATUS_PENDING:
				return 'Väntar';
			case self::STATUS_SENT:
				return 'Skickat';
			default:
				return 'Okänd';
		}
	}
}