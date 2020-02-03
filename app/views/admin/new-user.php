<?php

namespace eqhby\bkl; ?>

<div class="wrap">

	<h1>Lägg till användare</h1>

	<form method="post" novalidate="novalidate">
	
		<table class="form-table" role="presentation">
	
			<tbody>
				<tr>
					<th scope="row"><label for="first_name">Förnamn</label></th>
					<td><input name="first_name" type="text" id="first_name" class="regular-text"></td>
				</tr>
	
				<tr>
					<th scope="row"><label for="last_name">Efternamn</label></th>
					<td><input name="last_name" type="text" id="last_name" class="regular-text"></td>
				</tr>
	
				<tr>
					<th scope="row"><label for="email">E-postadress</label></th>
					<td><input name="email" type="email" id="email" class="regular-text"></td>
				</tr>
	
				<tr>
					<th scope="row"><label for="phone">Telefonnummer</label></th>
					<td><input name="phone" type="tel" id="phone" class="regular-text"></td>
				</tr>

				<tr>
					<th scope="row"><label for="has_swish">Swish</label></th>
					<td>
						<input type="hidden" name="has_swish" value="0">
						<input name="has_swish" type="checkbox" id="has_swish" value="1">
						<span>Användaren har swish</span>
					</td>
				</tr>
	
				<tr>
					<th scope="row"><label for="role">Roll</label></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="role" value="bkl_seller" checked>
								<span>Säljare</span>
							</label>
							<br>
							<label>
								<input type="radio" name="role" value="bkl_admin">
								<span>Admin</span>
							</label>
							<br>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<?php wp_nonce_field('bkl_new_user'); ?>
		<input type="hidden" name="controller" value="Users">
		<p class="submit"><button type="submit" class="button button-primary" name="action" value="new_user">Skapa användare</button></p>
	</form>
</div>
