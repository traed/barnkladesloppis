<?php

namespace eqhby\bkl; ?>

<div class="wrap">

	<h1>Redigera användare</h1>

	<form method="post" novalidate="novalidate">
	
		<table class="form-table" role="presentation">
	
			<tbody>
				<tr>
					<th scope="row"><label for="first_name">Förnamn</label></th>
					<td><input name="first_name" type="text" id="first_name" value="<?php echo $user->get('first_name'); ?>" class="regular-text"></td>
				</tr>
	
				<tr>
					<th scope="row"><label for="last_name">Efternamn</label></th>
					<td><input name="last_name" type="text" id="last_name" value="<?php echo $user->get('last_name'); ?>" class="regular-text"></td>
				</tr>
	
				<tr>
					<th scope="row"><label for="email">E-postadress</label></th>
					<td><input name="email" type="email" id="email" value="<?php echo $user->get('user_email'); ?>" class="regular-text"></td>
				</tr>
	
				<tr>
					<th scope="row"><label for="phone">Telefonnummer</label></th>
					<td><input name="phone" type="tel" id="phone" value="<?php echo $user->get('phone'); ?>" class="regular-text"></td>
				</tr>

				<tr>
					<th scope="row"><label for="verified_phone">Verifierad</label></th>
					<td>
						<input type="hidden" name="verified_phone" value="0">
						<input name="verified_phone" type="checkbox" id="verified_phone" value="1"<?php echo $user->get('verified_phone') ? ' checked' : ''; ?>>
						<span>Användarens telefonnummer är verifierat</span>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="has_swish">Swish</label></th>
					<td>
						<input type="hidden" name="has_swish" value="0">
						<input name="has_swish" type="checkbox" id="has_swish" value="1"<?php echo (int)$user->get('has_swish') ? ' checked' : ''; ?>>
						<span>Användaren har swish</span>
					</td>
				</tr>
	
				<tr>
					<th scope="row"><label for="role">Roll</label></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="role" value="bkl_seller"<?php echo in_array('bkl_seller', $user->roles) ? ' checked' : ''; ?>>
								<span>Säljare</span>
							</label>
							<br>
							<label>
								<input type="radio" name="role" value="bkl_admin"<?php echo in_array('bkl_admin', $user->roles) ? ' checked' : ''; ?>>
								<span>Admin</span>
							</label>
							<br>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="seller_id">Försäljnings-ID</label></th>
					<td>
						<input type="number" id="seller_id" name="seller_id" value="<?php echo $user->get('seller_id'); ?>" class="small-text">
						<button type="button" class="button" id="clear_seller_id">Rensa</button>
					</td>
				</tr>

				<?php if($can_sign_up): ?>
				<tr>
					<th scope="row"><label for="next_status">Status för kommande loppis</label></th>
					<td>
						<select name="status" id="next_status">
							<option value="none">Ej anmäld</option>
							<option value="signed_up"<?php echo $status === 'signed_up' ? ' selected' : ''; ?>>Anmäld</option>
							<option value="reserve"<?php echo $status === 'reserve' ? ' selected' : ''; ?>>Reserv</option>
						</select>
					</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<?php wp_nonce_field('bkl_edit_user'); ?>
		<input type="hidden" name="original_email" value="<?php echo $user->get('user_email'); ?>">
		<input type="hidden" name="controller" value="Users">
		<p class="submit"><button type="submit" class="button button-primary" name="action" value="edit_user">Spara ändringar</button></p>
	</form>
</div>
