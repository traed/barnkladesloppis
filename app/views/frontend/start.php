<?php

namespace eqhby\bkl; ?>

<div id="primary" class="content-area narrow">
	<main id="main" class="site-main">
		<div class="row">
			<div class="col s12 xl8">
				<?php the_content(); ?>
			</div>

			<div class="col s12 xl4 sidebar">
				<?php include 'sidebar.php' ?>
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
				<div class="row">
					<div class="col s12">
						<input type="hidden" name="has_swish" value="0" />
						<label>
							<input type="checkbox" name="has_swish" id="has_swish" value="1"<?php echo (int)$current_user->get('has_swish') ? ' checked' : ''; ?> />
							<span>Jag har swish anslutet till telefonnummret ovan</span>
						</label>
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