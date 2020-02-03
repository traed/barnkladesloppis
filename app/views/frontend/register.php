<?php namespace eqhby\bkl; ?>

<div class="content narrow">
	<h1>Registrera dig</h1>

	<?php echo apply_filters('the_content', get_option('bkl_registration_terms', '')); ?>

	<h2>Registrera</h2>
	<?php if($error = Session::get_once('registration_error')): ?>
		<p class="red-text"><?php echo $error; ?></p>
	<?php endif; ?>
	<div class="bkl-login_form-wrapper">
		<form name="bkl_register" id="bkl_register" method="post">
			<div class="row">
				<div class="input-field col s6">
					<label for="first_name">Förnamn</label>
					<input type="text" name="first_name" id="first_name" class="input" value="<?php echo $_POST['first_name'] ?? ''; ?>" required />
				</div>
				<div class="input-field col s6">
					<label for="last_name">Efternamn</label>
					<input type="text" name="last_name" id="last_name" class="input" value="<?php echo $_POST['last_name'] ?? ''; ?>" required />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<label for="email">Epostadress</label>
					<input type="email" name="email" id="email" class="input" value="<?php echo $_POST['email'] ?? ''; ?>" required />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<label for="phone">Telefonnummer</label>
					<input type="tel" name="phone" id="phone" class="input" value="<?php echo $_POST['phone'] ?? ''; ?>" required />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<label for="password">Lösenord</label>
					<input type="password" name="password" id="password" class="input" required />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<label for="password_confirm">Bekräfta lösenord</label>
					<input type="password" name="password_confirm" id="password_confirm" class="input" required />
				</div>
			</div>
			<div class="row">
				<div class="col s12">
					<input type="hidden" name="has_swish" value="0" />
					<label>
						<input type="checkbox" name="has_swish" id="has_swish" value="1"<?php echo !empty($_POST['first_name']) ? ' checked' : ''; ?> />
						<span>Jag har swish anslutet till telefonnummret ovan</span>
					</label>
				</div>
			</div>

			<div class="row">
				<div class="col s6">
					<label>
						<input type="checkbox" name="terms" value="1"<?php echo !empty($_POST['terms']) ? ' checked' : ''; ?> required>
						<span>Jag har läst och godkänner villkoren ovan.</span>
					</label>
				</div>

				<div class="login-submit col s6 right-align">
					<input type="hidden" id="recaptcha_token" name="recaptcha_token" value="">
					<?php wp_nonce_field('bkl_register'); ?>
					<button type="submit" name="action" class="waves-effect waves-light btn" value="register">Registrera</button>
				</div>
			</div>
		</form>
	</div>
</div>