<?php

namespace eqhby\bkl;

class Frontend_Controller extends Controller {
	public function __construct() {
		parent::__construct();

		$this->add_body_class('bkl');

		do_action('bkl_frontend');
	}


	public function init() {
		include(Plugin::PATH . '/app/views/frontend/start.php');
	}


	protected function handle_post() {
		# code...
	}
}