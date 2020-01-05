<?php namespace eqhby\bkl; ?>

<div class="wrap">
	<h1>Användare</h1>

	<form method="post">
		<input type="hidden" name="controller" value="Users">
		<?php wp_nonce_field('users_table'); ?>

		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select name="bulk_action" id="bulk-action-selector-top">
					<option value="-1">Massåtgärder</option>
					<option value="trash">Radera</option>
				</select>
				<button type="submit" name="action" value="bulk" class="button action" onclick="return confirm('Är du säker?');">Verkställ</button>
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
					<td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
					<th scope="col" id="user_email" class="manage-column column-user_email column-primary sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'user_email' && isset($_GET['order']) && $_GET['order'] === 'desc' ? 'asc' : 'desc'; ?>"><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;orderby=user_email&amp;order=<?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'user_email' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc'; ?>"><span>E-postadress</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-first_name sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'first_name' && isset($_GET['order']) && $_GET['order'] === 'desc' ? 'asc' : 'desc'; ?>"><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;orderby=first_name&amp;order=<?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'first_name' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc'; ?>"><span>Förnamn</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-last_name sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'last_name' && isset($_GET['order']) && $_GET['order'] === 'desc' ? 'asc' : 'desc'; ?>"><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;orderby=last_name&amp;order=<?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'last_name' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc'; ?>"><span>Efternamn</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-phone"><span>Telefonnummer</span></th>
					<th scope="col" class="manage-column column-seller_id sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'seller_id' && isset($_GET['order']) && $_GET['order'] === 'desc' ? 'asc' : 'desc'; ?>"><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;orderby=seller_id&amp;order=<?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'seller_id' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc'; ?>"><span>Försäljnings-ID</span><span class="sorting-indicator"></span></a></th>
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
						<td class="phone column-seller_id" data-colname="Försäljnings-ID"><?php echo $user->get('seller_id'); ?></td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>

			</tbody>

			<tfoot>
				<tr>
					<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Välj alla</label><input id="cb-select-all-2" type="checkbox"></td>
					<th scope="col" id="user_email" class="manage-column column-user_email column-primary sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'user_email' && isset($_GET['order']) && $_GET['order'] === 'desc' ? 'asc' : 'desc'; ?>"><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;orderby=user_email&amp;order=<?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'user_email' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc'; ?>"><span>E-postadress</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-first_name sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'first_name' && isset($_GET['order']) && $_GET['order'] === 'desc' ? 'asc' : 'desc'; ?>"><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;orderby=first_name&amp;order=<?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'first_name' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc'; ?>"><span>Förnamn</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-last_name sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'last_name' && isset($_GET['order']) && $_GET['order'] === 'desc' ? 'asc' : 'desc'; ?>"><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;orderby=last_name&amp;order=<?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'last_name' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc'; ?>"><span>Efternamn</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-phone"><span>Telefonnummer</span></th>
					<th scope="col" class="manage-column column-seller_id sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'seller_id' && isset($_GET['order']) && $_GET['order'] === 'desc' ? 'asc' : 'desc'; ?>"><a href="/wp-admin/edit.php?post_type=bkl_occasion&amp;page=bkl_users&amp;orderby=seller_id&amp;order=<?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'seller_id' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc'; ?>"><span>Försäljnings-ID</span><span class="sorting-indicator"></span></a></th>
				</tr>
			</tfoot>
		</table>
	</form>
</div>