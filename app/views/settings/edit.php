<?php

namespace eqhby\bkl;

use Webbmaffian\MVC\Helper\Value;

?>

<div class="wrap">
	<h1><?php echo isset($dictionary) ? 'Edit' : 'Add'; ?> dictionary</h1>

	<form method="post" novalidate="novalidate">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="dictionary-name">Name</label></th>
					<td>
						<input name="name" type="text" id="dictionary-name" value="<?php echo Value::get('name'); ?>" class="regular-text">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dictionary-status">Status</label></th>
					<td>
						<select name="status" id="dictionary-status">
							<option value="inactive"<?php echo 'inactive' === Value::get('status') ? ' selected' : ''; ?>>Inactive</option>
							<option value="active"<?php echo 'active' === Value::get('status') ? ' selected' : ''; ?>>Active</option>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dictionary-lang">Language code</label></th>
					<td>
						<input name="lang" type="text" id="dictionary-lang" value="<?php echo Value::get('lang'); ?>" class="small-text" maxlength="2"<?php isset($dictionary) ? ' readonly' : ''; ?>>
						<p class="description">A two letter country or language code.</p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="hidden" name="controller" value="Settings">
			<?php if(isset($dictionary)): ?>
				<button type="submit" name="action" id="submit" class="button button-primary" value="dictionary-edit">Save changes</button>
				<button type="submit" name="action" id="submit" class="button" value="dictionary-reindex">Reindex</button>
				<button type="submit" name="action" id="submit" class="button" value="dictionary-delete" onclick="return confirm('Are you sure?');">Delete</button>
			<?php else: ?>
				<button type="submit" name="action" id="submit" class="button button-primary" value="dictionary-add">Create</button>
			<?php endif; ?>
		</p>
	</form>
</div>