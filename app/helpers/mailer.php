<?php

namespace eqhby\bkl;

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

class Mailer {

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
	 * @param array $to Array with the following keys: email, first, last, sellerId
	 * @param string $subject
	 * @param string $message A HTML formated message
	 * 
	 * @return void
	 */
	public function send(array $to, string $subject, string $message) {
		$recipients = array_map(function($t) {
			return new Recipient($t['email'], $t['first'] . ' ' . $t['last']);
		}, $to);

		$variables = array_map(function($t) {
			return [
				'email' => $t['email'],
				'substitutions' => [
					[
						'var' => 'email',
						'value' => (string)$t['email']
					],
					[
						'var' => 'first',
						'value' => (string)$t['first']
					],
					[
						'var' => 'last',
						'value' => (string)$t['last']
					],
					[
						'var' => 'sellerId',
						'value' => (string)$t['seller_id']
					]
				]
			];
		}, $to);

		$email = (new EmailParams())
			->setFrom('loppis@equmeniahasselby.se')
			->setFromName('Barnklädesloppis')
			->setRecipients($recipients)
			->setSubject($subject)
			->setHtml($message)
			->setText(wp_strip_all_tags($message))
			->setVariables($variables);

		$this->client->email->send($email);
	}
}