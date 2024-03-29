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
			update_option('bkl_registration_email', wp_kses_post($_POST['registration_email']));
			update_option('bkl_registration_email_reserve', wp_kses_post($_POST['registration_email_reserve']));
			update_option('bkl_enable_sign_up', (int)$_POST['enable_sign_up']);

			if(current_user_can('administrator')) {
				update_option('bkl_email_api_key', sanitize_text_field($_POST['email_api_key']));
				update_option('bkl_recaptcha_site_key', sanitize_text_field($_POST['recaptcha_site_key']));
				update_option('bkl_recaptcha_secret', sanitize_text_field($_POST['recaptcha_secret']));
				update_option('bkl_sms_api_username', sanitize_text_field($_POST['sms_api_username']));
				update_option('bkl_sms_api_password', sanitize_text_field($_POST['sms_api_password']));
			}

			Admin::notice('Inställningar sparade!', 'success');

			wp_safe_redirect($_POST['_wp_http_referer']);
			exit;
		}
	}
}