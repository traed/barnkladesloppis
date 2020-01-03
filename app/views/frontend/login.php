<?php namespace eqhby\bkl; ?>

<h1>Loppis utloggad</h1>

<?php
	$defaults = array(
		'echo'           => true,
		// Default 'redirect' value takes the user back to the request URI.
		'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'form_id'        => 'bkl_loginform',
		'label_username' => 'Epostadress',
		'label_password' => 'Lösenord',
		'label_remember' => 'Kom ihåg mig',
		'label_log_in'   => 'Logga in',
		'id_username'    => 'user_login',
		'id_password'    => 'user_pass',
		'id_remember'    => 'rememberme',
		'id_submit'      => 'wp-submit',
		'remember'       => true,
		'value_username' => '',
		// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
		'value_remember' => true,
	);
	$args = wp_parse_args( [], apply_filters( 'login_form_defaults', $defaults ) );
?>

<div class="row bkl-login">
	<div class="col m12 l5">
		<div class="bkl-login_form-wrapper z-depth-1">
			<h2>Logga in</h2>
			<form name="<?php echo $args['form_id']; ?>" id="<?php echo $args['form_id']; ?>" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
				<div class="row">
					<div class="input-field col s12">
						<label for="<?php echo esc_attr( $args['id_username'] ); ?>"><?php echo esc_html( $args['label_username'] ); ?></label>
						<input type="text" name="log" id="<?php echo esc_attr( $args['id_username'] ); ?>" class="input" value="<?php echo esc_attr( $args['value_username'] ); ?>" size="20" />
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<label for="<?php echo esc_attr( $args['id_password'] ); ?>"><?php echo esc_html( $args['label_password'] ); ?></label>
						<input type="password" name="pwd" id="<?php echo esc_attr( $args['id_password'] ); ?>" class="input" value="" size="20" />
					</div>
				</div>
				<div class="row">
					<?php if($args['remember']): ?>
						<div class="login-remember col s6">
							<label>
								<input name="rememberme" type="checkbox" class="filled-in" id="<?php echo esc_attr( $args['id_remember'] ); ?>" value="forever"<?php echo ( $args['value_remember'] ? ' checked="checked"' : '' ); ?> />
								<?php echo esc_html( $args['label_remember'] ); ?>
							</label>
						</div>
					<?php endif; ?>
					<div class="login-submit col s6 right-align">
						<button type="submit" name="wp-submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" class="waves-effect waves-light btn" value="<?php echo esc_attr( $args['label_log_in'] ); ?>">Logga in</button>
						<input type="hidden" name="redirect_to" value="<?php echo esc_url( $args['redirect'] ); ?>" />
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="col m12 l2 bkl-middle-section">
		<span>--</span> eller <span>--</span>
	</div>

	<div class="col m12 l5">
		<div class="bkl-login_form-wrapper z-depth-1">
			<h2>Registrera dig</h2>
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
					<div class="login-submit col s12 right-align">
						<input type="hidden" name="controller" value="Frontend">
						<?php wp_nonce_field('bkl_register'); ?>
						<button type="submit" name="action" class="waves-effect waves-light btn" value="register">Registrera</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>