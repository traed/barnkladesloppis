<?php namespace eqhby\bkl; ?>

<div class="wrap">
	<h1>E-postutskick</h1>

	<form method="post">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">Ämne</th>
					<td>
						<input type="text" id="subject" name="subject" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">Meddelande</th>
					<td>
						<?php
						$settings = array(
							'teeny' => true,
							'textarea_rows' => 15,
							'tabindex' => 1
						);
						wp_editor('', 'message', $settings);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row">Tillgängliga variabler</th>
					<td>
						<ul>
							<li><pre style="display: inline;">{$first}</pre> - Förnamn</li>
							<li><pre style="display: inline;">{$last}</pre> - Efternamn</li>
							<li><pre style="display: inline;">{$email}</pre> - E-postadress</li>
							<li><pre style="display: inline;">{$seller_id}</pre> - Säljarnummer</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recipients">Mottagare</label></th>
					<td>
						<select name="recipients" id="recipients">
							<option value="all">Alla</option>
							<optgroup label="Loppis-tillfällen">
								<?php foreach(Occasion::get_future() as $occasion): ?>
									<option value="occasion_<?php echo $occasion->get_ID(); ?>"><?php echo $occasion->get_post_title(); ?></option>
								<?php endforeach; ?>
							</optgroup>
							<optgroup label="Enstaka användare">
								<?php foreach($all_sellers as $user): ?>
									<option value="seller_<?php echo $user->ID; ?>"><?php echo $user->get('display_name'); ?> (<?php echo $user->get('user_email'); ?>)</option>
								<?php endforeach; ?>
							</optgroup>
						</select>
					</td>
				</tr>
				<tr style="display: none;" id="user_status_row">
					<th scope="row"><label for="user_status">Status</label></th>
					<td>
						<select name="user_status" id="user_status">
							<option value="all">Alla</option>
							<option value="signed_up">Anmäld</option>
							<option value="reserve">Reserv</option>
							<option value="none">Ej anmäld</option>
						</select>
						<p class="description">Statusen avser användarens anmälningsstatus för det valda loppis-tillfället.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Meddelanden i kö</th>
					<td>
						<?php echo $num_queued_messaged; ?>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<?php wp_nonce_field('bkl_send_email'); ?>
			<input type="hidden" name="controller" value="Email">
			<button type="submit" name="action" value="enqueue" class="button button-primary">Lägg i kö</button>

			<?php if($num_queued_messaged > 0): ?>
				<button type="submit" name="action" value="send" class="button button-secondary">Skicka köade</button>
			<?php endif; ?>
		</p>
	</form>	
</div>
