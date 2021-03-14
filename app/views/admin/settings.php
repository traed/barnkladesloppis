<?php namespace eqhby\bkl; ?>

<div class="wrap">
	<h1>Inställningar</h1>

	<form method="post">
		<h2>Villkor vid anmälan till loppis</h2>
		<p>Följande text visas i dialogrutan som dyker upp när man ska anmäla sig som säljare till en loppis.</p>
		<?php
		$settings = array(
			'teeny' => true,
			'textarea_rows' => 15,
			'tabindex' => 1
		);
		wp_editor(get_option('bkl_sign_up_terms', ''), 'sign_up_terms', $settings);
		?>

		<h2>Registreringsvillkor</h2>
		<p>Följande text vid registreringsformuläret och är tänkt att vara villkoren man godkänner för att anmäla sig. Bör innehålla följande länk till vår dataskyddspolicy: <?php echo get_privacy_policy_url(); ?></p>
		<?php
		$settings = array(
			'teeny' => true,
			'textarea_rows' => 15,
			'tabindex' => 1
		);
		wp_editor(get_option('bkl_registration_terms', ''), 'registration_terms', $settings);
		?>

		<br>
		<hr>

		<?php if(current_user_can('administrator')): ?>
			<h2>E-post</h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label for="email_api_key">API-nyckel</label></th>
						<td><input type="text" name="email_api_key" id="email_api_key" value="<?php echo get_option('bkl_email_api_key', ''); ?>" class="regular-text"></td>
					</tr>
				</tbody>
			</table>

			<h2>reCaptcha v3</h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label for="recaptcha_site_key">Webbplatsnyckel</label></th>
						<td><input type="text" name="recaptcha_site_key" id="recaptcha_site_key" value="<?php echo get_option('bkl_recaptcha_site_key', ''); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="recaptcha_secret">Hemlighet</label></th>
						<td><input type="text" name="recaptcha_secret" id="recaptcha_secret" value="<?php echo get_option('bkl_recaptcha_secret', ''); ?>" class="regular-text"></td>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>

		<h2>Anmälan</h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="enable_sign_up">Öppna för nya konton</label></th>
					<td>
						<input type="hidden" name="enable_sign_up" value="0">
						<input type="checkbox" name="enable_sign_up" id="enable_sign_up" value="1"<?php echo get_option('bkl_enable_sign_up', false) ? ' checked' : ''; ?>>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<?php wp_nonce_field('bkl_settings'); ?>
			<input type="hidden" name="controller" value="Settings">
			<button type="submit" name="action" value="save" class="button button-primary">Spara ändringar</button>
		</p>
	</form>
</div>