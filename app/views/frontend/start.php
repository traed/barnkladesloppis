<?php

namespace eqhby\bkl; ?>

<h1><?php echo $title; ?></h1>

<div id="primary" class="content-area narrow">
	<main id="main" class="site-main">

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
					<p>Registrera dig</p>
				</div>
			</a>
		</div>
		<?php endif; ?>

		<div class="row">
			<div class="col s12 xl8">
				<?php 
				if($next_occasion) {
					echo apply_filters('the_content', $next_occasion->get_post_content());
				}
				?>
			</div>

			<div class="col s12 xl4">
				<h2 class="h3">Kommande loppisar</h2>
				<div class="block-wrapper posts">
					<div class="block row">
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

										<?php $status = $occasion->get_user_status(get_current_user_id()); ?>
										<?php if(in_array($status, ['signed_up', 'reserve'])): ?>
											<?php if($status === 'signed_up'): ?>
												<p class="green-text"><strong>Du är anmäld!</strong></p>
											<?php else: ?>
												<p class="orange-text"><strong>Du är med på väntelistan.</strong></p>
												<p>Det är just nu fullt på loppisen. Om en plats blir ledig meddelar vi dig.</p>
											<?php endif; ?>
											<form method="post">
												<?php wp_nonce_field('bkl_resign'); ?>
												<input type="hidden" name="occasion_id" value="<?php echo $occasion->get_ID(); ?>">
												<input type="hidden" name="controller" value="Frontend">
												<button type="submit" name="action" value="resign" class="waves-effect waves-light btn">Avanmäl mig</button>
											</form>
										<?php else: ?>
											<button type="button" data-target="confirmModal" class="waves-effect waves-light btn modal-trigger">Anmäl mig</button>
										<?php endif; ?>

										<div id="confirmModal" class="modal">
											<div class="modal-content">
												<h4>Jag vill anmäla mig som säljare</h4>
												<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed volutpat a ante quis accumsan. Nunc augue est, tempus in purus nec, gravida imperdiet est. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Mauris commodo ex non odio efficitur sollicitudin. Curabitur sit amet consectetur dolor. Nam id pharetra elit. Proin congue massa at mauris egestas, eget mattis ante suscipit.</p>
												<p>Vivamus sodales a sapien sit amet venenatis. Etiam a tellus scelerisque, cursus quam eleifend, finibus sapien. Ut scelerisque id purus ut elementum. Proin ut libero sed turpis varius faucibus. Vestibulum vehicula sapien vitae sagittis sagittis. Ut ac faucibus orci. Curabitur at tellus lobortis tellus dictum commodo sed eget odio. Maecenas lacinia condimentum nibh vitae facilisis. Morbi tristique dui libero, at rutrum lorem faucibus non. Praesent pellentesque eget lacus id fringilla. In fringilla pellentesque augue, at posuere urna tempus id. Maecenas malesuada velit id maximus tristique. Donec euismod vitae mi eget tincidunt. Sed sit amet lectus ut ligula pulvinar tempor ac a sapien.</p>
											</div>
											<div class="modal-footer">
												<form method="post">
													<label>
														<input type="checkbox" name="terms" value="1" required>
														<span>Jag har läst och förstått villkoren.</span>
													</label>
													<?php wp_nonce_field('bkl_sign_up'); ?>
													<input type="hidden" name="occasion_id" value="<?php echo $occasion->get_ID(); ?>">
													<input type="hidden" name="controller" value="Frontend">
													<button type="submit" name="action" value="sign_up" class="waves-effect waves-light btn">Slutför anmälan</button>
												</form>
											</div>
										</div>
									</div><!-- .entry-header -->
								</div>
							</article><!-- #post-<?php echo $occasion->get_ID(); ?> -->
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</main>
</div>