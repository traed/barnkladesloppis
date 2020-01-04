<?php

namespace eqhby\bkl; ?>

<div id="primary" class="content-area narrow">
	<main id="main" class="site-main">
		<div class="row">
			<div class="col s12 xl8">
				<h1><?php echo $title; ?></h1>

				<?php if(!is_user_logged_in()): ?>
					<div class="bkl-cta-wrapper">
						<a href="/loppis/login">
							<div class="bkl-cta login">
								<div class="icon">
									<?php echo file_get_contents(Plugin::PATH . '/assets/img/unlock.svg'); ?>
								</div>
								<p>Logga in</p>
							</div>
						</a>

						<a href="/loppis/reg">
							<div class="bkl-cta register">
								<div class="icon">
									<?php echo file_get_contents(Plugin::PATH . '/assets/img/pen.svg'); ?>
								</div>
								<p>Skapa konto</p>
							</div>
						</a>
					</div>
				<?php endif; ?>

				<?php 
				if($next_occasion) {
					echo apply_filters('the_content', $next_occasion->get_post_content());
				}
				?>
			</div>

			<div class="col s12 xl4 sidebar">
				<?php if(is_user_logged_in()): ?>
					<p>
						<a href="/wp-login.php?action=logout" class="waves-effect waves-light btn">Logga ut</a>
					</p>

					<div class="credentials-header">
						<h2 class="h3">Mina uppgifter</h2>
						<button type="button" data-target="editUserModal" class="waves-effect waves-light btn-flat modal-trigger">Ändra</button>
					</div>
					<dl>
						<dt>Namn</dt>
						<dd><?php echo $current_user->get('display_name'); ?></dd>
						<dt>E-postadress</dt>
						<dd><?php echo $current_user->get('user_email'); ?></dd>
						<dt>Telefonnummer</dt>
						<dd><?php echo $current_user->get('phone'); ?></dd>
						<dt>Försäljnings-ID</dt>
						<dd><?php echo $current_user->get('seller_number'); ?></dd>
					</dl>
				<?php endif; ?>

				<h2 class="h3">Kommande loppisar</h2>
				<div class="block-wrapper posts">
					<div class="block row">
						<?php if(empty($occasions)): ?>
							<div class="col s12">
								<p>Det finns för närvarande inga framtida loppisar. När anmälan öppnar för nästa loppis dyker den upp här.</p>
							</div>
						<?php else: ?>
							<?php foreach($occasions as $occasion): ?>
								<article id="post-<?php echo $occasion->get_ID(); ?>" class="<?php echo implode(' ', get_post_class('post col xl12 l6 s12', $occasion->get_ID())); ?>">
									<div class="link-wrapper">
										<div class="entry-header">
											<h3 class="entry-title h4"><?php echo get_the_title($occasion->get_ID()); ?></h3>
											<ul class="entry-meta">
												<li>Datum: <?php echo $occasion->get_date_start(); ?></li>
												<li>Anmälan öppnar: <?php echo $occasion->get_date_signup(); ?></li>
												<li>Avgift: <?php echo $occasion->get_seller_fee(); ?> kr</li>
											</ul><!-- .entry-meta -->

											<?php if(!is_user_logged_in()): ?>
												<p>Du måste vara inloggad för att anmäla dig.</p>
											<?php elseif(in_array($status, ['signed_up', 'reserve'])): ?>
												<?php if($status === 'signed_up'): ?>
													<p class="green-text"><strong>Du är anmäld!</strong></p>
												<?php else: ?>
													<p class="orange-text"><strong>Du är med på väntelistan.</strong></p>
													<p>Det är just nu fullt på loppisen. Om en plats blir ledig meddelar vi dig.</p>
												<?php endif; ?>
												<form method="post">
													<?php wp_nonce_field('bkl_resign', 'bkl_resign_nonce'); ?>
													<input type="hidden" name="occasion_id" value="<?php echo $occasion->get_ID(); ?>">
													<input type="hidden" name="controller" value="Frontend">
													<button type="submit" name="action" value="resign" class="waves-effect waves-light btn">Avanmäl mig</button>
												</form>
											<?php elseif($occasion->is_registration_open()): ?>
												<button type="button" data-target="confirmModal-<?php echo $occasion->get_ID(); ?>" class="waves-effect waves-light btn modal-trigger">Anmäl mig</button>

												<div id="confirmModal-<?php echo $occasion->get_ID(); ?>" class="modal">
													<div class="modal-content">
														<h4>Jag vill anmäla mig som säljare</h4>
														<?php echo apply_filters('the_content', get_option('bkl_sign_up_terms', '')); ?>
													</div>
													<div class="modal-footer">
														<form method="post">
															<label>
																<input type="checkbox" name="terms" value="1" required>
																<span>Jag har läst och förstått villkoren.</span>
															</label>
															<input type="hidden" name="bkl_sign_up_nonce" value="<?php echo wp_create_nonce('bkl_sign_up'); ?>">
															<input type="hidden" name="occasion_id" value="<?php echo $occasion->get_ID(); ?>">
															<input type="hidden" name="controller" value="Frontend">
															<button type="submit" name="action" value="sign_up" class="waves-effect waves-light btn">Slutför anmälan</button>
														</form>
													</div>
												</div>
											<?php endif; ?>
										</div><!-- .entry-header -->
									</div>
								</article><!-- #post-<?php echo $occasion->get_ID(); ?> -->
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</main>
</div>

<?php if(is_user_logged_in()): ?>
	<div id="editUserModal" class="modal">
		<form method="post">
			<div class="modal-content">
				<h3>Ändra uppgifter</h3>
				<div class="row">
					<div class="input-field col s6">
						<label for="first_name">Förnamn</label>
						<input type="text" name="first_name" id="first_name" class="input" value="<?php echo $current_user->get('first_name'); ?>" />
					</div>
					<div class="input-field col s6">
						<label for="last_name">Efternamn</label>
						<input type="text" name="last_name" id="last_name" class="input" value="<?php echo $current_user->get('last_name'); ?>" />
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<label for="email">Epostadress</label>
						<input type="email" name="email" id="email" class="input" value="<?php echo $current_user->get('user_email'); ?>" />
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<label for="phone">Telefonnummer</label>
						<input type="tel" name="phone" id="phone" class="input" value="<?php echo $current_user->get('phone'); ?>" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<?php wp_nonce_field('bkl_edit_user', 'bkl_edit_user_nonce'); ?>
				<input type="hidden" name="controller" value="Frontend">
				<button type="submit" name="action" value="edit_user" class="waves-effect waves-light btn">Spara</button>
			</div>
		</form>
	</div>
<?php endif; ?>