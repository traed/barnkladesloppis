<?php

return array(
	// 'loppis' => ['Frontend', 'init'],
	'loppis/reg' => ['Frontend', 'register'],
	'loppis/login' => ['Frontend', 'login'],
	'loppis/v/{id}/{nonce}' => ['Frontend', 'verify_phone_number'],
);