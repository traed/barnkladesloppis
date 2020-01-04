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
		wp_editor(esc_html(get_option('bkl_sign_up_terms', '')), 'sign_up_terms', $settings);
		?>

		<h2>Registreringsvillkor</h2>
		<p>Följande text vid registreringsformuläret och är tänkt att vara villkoren man godkänner för att anmäla sig. Bör innehålla följande länk till vår dataskyddspolicy: <?php echo get_privacy_policy_url(); ?></p>
		<?php
		$settings = array(
			'teeny' => true,
			'textarea_rows' => 15,
			'tabindex' => 1
		);
		wp_editor(esc_html(get_option('bkl_registration_terms', '')), 'registration_terms', $settings);
		?>

		<p class="submit">
			<?php wp_nonce_field('bkl_settings'); ?>
			<input type="hidden" name="controller" value="Settings">
			<button type="submit" name="action" value="save" class="button button-primary">Spara ändringar</button>
		</p>
	</form>
</div>