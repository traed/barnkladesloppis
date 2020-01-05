<?php

namespace eqhby\bkl;

class Settings_Controller extends Controller {

	public function init() {
		include(Plugin::PATH . '/app/views/admin/settings.php');
	}


	public function handle_post($action) {
		if($action === 'save' && wp_verify_nonce($_POST['_wpnonce'], 'bkl_settings')) {
			update_option('bkl_sign_up_terms', wp_kses_post($_POST['sign_up_terms']));
			update_option('bkl_registration_terms', wp_kses_post($_POST['registration_terms']));
			update_option('bkl_email_api_key', sanitize_text_field($_POST['email_api_key']));
			update_option('bkl_email_api_url', sanitize_text_field($_POST['email_api_url']));
			update_option('bkl_recaptcha_site_key', sanitize_text_field($_POST['recaptcha_site_key']));
			update_option('bkl_recaptcha_secret', sanitize_text_field($_POST['recaptcha_secret']));

			Admin::notice('Inställningar sparade!', 'success');

			wp_safe_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
}