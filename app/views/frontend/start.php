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

		<?php 
		if($next_occasion) {
			echo apply_filters('the_content', $next_occasion->get_post_content());
		}
		?>

	</main>
</div>
