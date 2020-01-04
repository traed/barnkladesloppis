<?php namespace eqhby\bkl; ?>

<div class="content narrow">
	<h1>Registrera dig</h1>

	<?php echo apply_filters('the_content', get_option('bkl_registration_terms', '')); ?>

	<h2>Registrera</h2>
	<div class="bkl-login_form-wrapper">
		<form name="bkl_register" id="bkl_register" method="post">
			<div class="row">
				<div class="input-field col s6">
					<label for="first_name">Förnamn</label>
					<input type="text" name="first_name" id="first_name" class="input" />
				</div>
				<div class="input-field col s6">
					<label for="last_name">Efternamn</label>
					<input type="text" name="last_name" id="last_name" class="input" />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<label for="email">Epostadress</label>
					<input type="email" name="email" id="email" class="input" />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<label for="phone">Telefonnummer</label>
					<input type="tel" name="phone" id="phone" class="input" />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<label for="password">Lösenord</label>
					<input type="password" name="password" id="password" class="input" />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<label for="password_confirm">Bekräfta lösenord</label>
					<input type="password" name="password_confirm" id="password_confirm" class="input" />
				</div>
			</div>

			<div class="row">
				<div class="login-submit col s12 right-align">
					<input type="hidden" name="controller" value="Frontend">
					<?php wp_nonce_field('bkl_register'); ?>
					<button type="submit" name="action" class="waves-effect waves-light btn" value="register">Registrera</button>
				</div>
			</div>
		</form>
	</div>
</div>