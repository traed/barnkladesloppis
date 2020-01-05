<?php

namespace eqhby\bkl;

use Mailgun\Mailgun;

class Mailer {

	private $client;
	private $domain;

	public function __construct() {
		$api_key = get_option('bkl_email_api_key');
		$domain = get_option('bkl_email_api_url');

		if($api_key && $domain) {
			$this->client = Mailgun::create($api_key, 'https://api.eu.mailgun.net');;
			$this->domain = $domain;
		} else {
			throw new Problem('Kunde inte ansluta till eposttjänsten.');
		}
	}


	/**
	 * @param array $to Array with the following keys: email, first, last, sellerId
	 * @param string $subject
	 * @param string $message A HTML formated message
	 * 
	 * @return void
	 */
	public function send(array $to, string $subject, string $message) {
		$batch_message = $this->client->messages()->getBatchMessage($this->domain);
		$batch_message->setFromAddress('loppis@equmeniahasselby.se', ['full_name' => 'Barnklädesloppis']);
		$batch_message->setSubject($subject);
		$batch_message->setHtmlBody($message);
		$batch_message->addCustomData('user', [
			'sellerId' => '%recipient.sellerId%'
		]);

		$chunks = array_chunk($to, 750);

		foreach($chunks as $users) {
			foreach($users as $user) {
				$batch_message->addToRecipient($user['email'], [
					'first' => $user['first'],
					'last' => $user['last'],
					'sellerId' => $user['seller_id'],
				]);
			}
			$batch_message->finalize();
		}
	}
}