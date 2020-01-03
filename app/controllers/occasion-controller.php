<?php

namespace eqhby\bkl;

class Occasion_Controller extends Controller {
	public function occasion_settings_callback() {
		global $post;

		$date_start = get_post_meta($post->ID, 'date_start', true) ?: '';
		$date_signup = get_post_meta($post->ID, 'date_signup', true) ?: '';
		$num_spots = get_post_meta($post->ID, 'num_spots', true) ?: '';
		$seller_fee = get_post_meta($post->ID, 'seller_fee', true) ?: '';

		wp_nonce_field('bkl_occasion_save', 'bkl_metabox_nonce');
		?>
		<div>
			<label for="date_signup">Anmälan öppnar</label>
			<input type="date" name="date_signup" value="<?php echo $date_signup; ?>">
		</div>
		<div>
			<div><label for="date_start">Startdatum</label></div>
			<input type="date" name="date_start" id="date_start" value="<?php echo $date_start; ?>">
		</div>
		<div>
			<div><label for="num_spots">Antal platser</label></div>
			<input type="number" name="num_spots" id="num_spots" value="<?php echo $num_spots; ?>">
		</div>
		<div>
			<div><label for="seller_fee">Avgift (kr)</label></div>
			<input type="number" name="seller_fee" id="seller_fee" value="<?php echo $seller_fee; ?>">
		</div>
		<?php
	}


	public function occasion_users_callback() {
		global $post;
		$occasion = Occasion::get_by_id($post->ID);
		$users = $occasion->get_users('signed_up');

		?>
		<table class="wp-list-table widefat fixed striped users">
			<thead>
				<tr>
					<th>Namn</th>
					<th>Nummer</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php if(empty($users)): ?>
					<tr>
						<td colspan="3">Inga anmälda användare.</td>
					</tr>
				<?php else: ?>
					<?php foreach($users as $user): ?>
						<tr>
							<td><?php echo $user->get('display_name'); ?></td>
							<td><?php echo $user->get('seller_number'); ?></td>
							<td><a href="/wp-admin/edit.php?post_type=bkl_occasion&page=bkl_users&id=<?php echo $user->ID; ?>">Visa</a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}


	public function occasion_reserve_callback() {
		global $post;
		$occasion = Occasion::get_by_id($post->ID);
		$users = $occasion->get_users('reserve');

		?>
		<table class="wp-list-table widefat fixed striped users">
			<thead>
				<tr>
					<th>Namn</th>
					<th>Nummer</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php if(empty($users)): ?>
					<tr>
						<td colspan="3">Inga användare på väntelistan.</td>
					</tr>
				<?php else: ?>
					<?php foreach($users as $user): ?>
						<tr>
							<td><?php echo $user->get('display_name'); ?></td>
							<td><?php echo $user->get('seller_number'); ?></td>
							<td><a href="/wp-admin/edit.php?post_type=bkl_occasion&page=bkl_users&id=<?php echo $user->ID; ?>">Visa</a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}
}