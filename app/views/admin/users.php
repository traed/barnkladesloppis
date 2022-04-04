<?php namespace eqhby\bkl; ?>

<div class="wrap">
	<h1 class="wp-heading-inline">Användare</h1>
	<a href="/wp-admin/edit.php?post_type=bkl_occasion&page=bkl_users&id=new" class="page-title-action">Lägg till ny</a>

	<form method="post">
		<input type="hidden" name="controller" value="Users">
		<?php wp_nonce_field('bkl_users_bulk_action'); ?>

		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select name="bulk_action" id="bulk-action-selector-top">
					<option value="-1">Massåtgärder</option>
					<option value="export_all">Exportera alla</option>
					<option value="export_some">Exportera valda</option>
					<?php if(current_user_can('administrator')): ?>
						<option value="delete">Radera valda</option>
					<?php endif; ?>	
				</select>
					<button type="submit" name="action" value="bulk" class="button action"<?php if(current_user_can('administrator')): ?> onclick="return confirm('Är du säker?');"<?php endif;?>>Verkställ</button>
			</div>

			<div class="alignleft actions">
				<select name="occasion">
					<option value="-1">Alla tillfällen</option>
					<?php foreach(Occasion::get_all() as $occasion): ?>
						<option value="<?php echo $occasion->get_ID(); ?>"<?php echo isset($_GET['filter_occasion']) && $_GET['filter_occasion'] == $occasion->get_ID() ? ' selected' : ''; ?>><?php echo $occasion->get_post_title(); ?></option>
					<?php endforeach; ?>
				</select>
				<button type="submit" class="button" name="action" value="filter">Filtrera</button>
			</div>

			<?php $this->pagination($total_items, $total_pages); ?>

			<br class="clear">
		</div>

		<table class="wp-list-table widefat fixed striped users">
			<thead>
				<tr>
					<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Välj alla</label><input id="cb-select-all-1" type="checkbox"></td>
					<th scope="col" id="user_email" class="manage-column column-user_email column-primary sortable <?php echo Helper::get_ordering_class('user_email'); ?>"><a href="<?php echo Helper::get_ordering_url('user_email'); ?>"><span>E-postadress</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-first_name sortable <?php echo Helper::get_ordering_class('first_name'); ?>"><a href="<?php echo Helper::get_ordering_url('first_name'); ?>"><span>Förnamn</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-last_name sortable <?php echo Helper::get_ordering_class('last_name'); ?>"><a href="<?php echo Helper::get_ordering_url('last_name'); ?>"><span>Efternamn</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-phone"><span>Telefonnummer</span></th>
					<th scope="col" class="manage-column column-verified"><span>Verifierad</span></th>
					<th scope="col" class="manage-column column-seller_id sortable <?php echo Helper::get_ordering_class('seller_id'); ?>"><a href="<?php echo Helper::get_ordering_url('seller_id'); ?>"><span>Försäljnings-ID</span><span class="sorting-indicator"></span></a></th>
					<?php if(!empty($_GET['filter_occasion'])): ?>
						<th scope="col" class="manage-column column-return_items"><span>Vill ha tillbaka kläder</span></th>
					<?php endif; ?>
				</tr>
			</thead>

			<tbody id="the-list" data-wp-lists="list:user">

				<?php if(empty($users)): ?>
					<tr>
						<th scope="row">&nbsp;</th>
						<td colspan="4">Inga användare just nu.</td>
					</tr>
				<?php else: ?>
					<?php foreach($users as $user): ?>
					<tr id="user-<?php echo $user->ID; ?>">
						<th scope="row" class="check-column"><input type="checkbox" name="users[]" id="user_<?php echo $user->ID; ?>" class="<?php echo implode(' ', $user->roles); ?>" value="<?php echo $user->ID; ?>"></th>
						<td class="user_email column-user_email column-primary" data-colname="E-postadress">
							<strong><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;id=<?php echo $user->ID; ?>"><?php echo $user->get('user_email'); ?></a></strong>
						</td>
						<td class="name column-first_name" data-colname="Förnamn"><?php echo $user->get('first_name'); ?></td>
						<td class="name column-last_name" data-colname="Efternamn"><?php echo $user->get('last_name'); ?></td>
						<td class="phone column-phone" data-colname="Telefonnummer"><?php echo $user->get('phone'); ?></td>
						<td class="verified column-verified" data-colname="Verifierad"><?php echo $user->get('verified_phone') ? 'Ja' : 'Nej'; ?></td>
						<td class="phone column-seller_id" data-colname="Försäljnings-ID"><?php echo $user->get('seller_id') ?: '-'; ?></td>
						<?php if(!empty($_GET['filter_occasion'])): ?>
							<td class="return_items column-return_items" data-colname="Vill ha tillbaka kläder"><?php echo $user->get('return_items') ? 'Ja' : 'Nej'; ?></td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>

			</tbody>

			<tfoot>
				<tr>
					<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Välj alla</label><input id="cb-select-all-2" type="checkbox"></td>
					<th scope="col" id="user_email" class="manage-column column-user_email column-primary sortable <?php echo Helper::get_ordering_class('user_email'); ?>"><a href="<?php echo Helper::get_ordering_url('user_email'); ?>"><span>E-postadress</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-first_name sortable <?php echo Helper::get_ordering_class('first_name'); ?>"><a href="<?php echo Helper::get_ordering_url('first_name'); ?>"><span>Förnamn</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-last_name sortable <?php echo Helper::get_ordering_class('last_name'); ?>"><a href="<?php echo Helper::get_ordering_url('last_name'); ?>"><span>Efternamn</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-phone"><span>Telefonnummer</span></th>
					<th scope="col" class="manage-column column-verified"><span>Verifierad</span></th>
					<th scope="col" class="manage-column column-seller_id sortable <?php echo Helper::get_ordering_class('seller_id'); ?>"><a href="<?php echo Helper::get_ordering_url('seller_id'); ?>"><span>Försäljnings-ID</span><span class="sorting-indicator"></span></a></th>
					<?php if(!empty($_GET['filter_occasion'])): ?>
						<th scope="col" class="manage-column column-return_items"><span>Vill ha tillbaka kläder</span></th>
					<?php endif; ?>
				</tr>
			</tfoot>
		</table>
	</form>
</div>