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
	 * @return void
	 */
	public function enqueue(array $to, string $subject, string $message): void {
		global $wpdb;

		foreach($to as $recipient) {
			$wpdb->insert(Helper::get_table('emails'), [
				'recipient' => serialize($recipient),
				'subject' => $subject,
				'message' => $message,
				'time_created' => Helper::date('now')->format('Y-m-d H:i:s')
			], ['%s', '%s', '%s', '%s']);
		}
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


	public function get_next_batch(): array {
		global $wpdb;

		$results = $wpdb->get_results('
			SELECT *
			FROM ' . Helper::get_table('emails') . ' 
			WHERE time_sent IS NULL 
			ORDER BY time_created ASC 
			LIMIT 100
		', ARRAY_A);

		$ids = [];
		$return = [];

		if(!empty($results)) {
			foreach($results as $result) {
				$return[] = [
					'recipient' => unserialize($result['recipient']),
					'subject' => $result['subject'],
					'message' => $result['message']
				];
				$ids[] = (int)$result['id'];
			}
			
			$wpdb->query($wpdb->prepare(
				'UPDATE ' . Helper::get_table('emails') . '
				SET time_sent = %s
				WHERE id IN (' . implode(',', $ids) . ')',
				Helper::date('now')->format('Y-m-d H:i:s')
			));
		}

		return $return;
	}
}