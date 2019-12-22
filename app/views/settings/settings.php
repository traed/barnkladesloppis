<?php namespace eqhby\bkl;

use Firebase\JWT\JWT;

?>

<div class="wrap">
	<h1>Dictionary settings</h1>

	<form method="post" novalidate="novalidate">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="es-url">Elasticsearch URL</label></th>
					<td>
						<input name="es_url" type="text" id="es-url" value="<?php echo get_option('es-url', site_url()); ?>" class="regular-text">
					</td>
				</tr>
				
				<tr>
					<th scope="row"><label>Test connection</label></th>
					<td>
						<button type="submit" name="action" class="button button-primary" value="dictionary-test">Test</button>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="hidden" name="controller" value="Settings">
			<button type="submit" name="action" id="submit" class="button button-primary" value="dictionary-settings-update">Save changes</button>
		</p>
	</form>

	<?php include(Plugin::PATH . '/app/views/settings/parts/dictionary-table.php'); ?>
</div>