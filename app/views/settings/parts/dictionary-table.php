<?php namespace eqhby\bkl; ?>

<form method="post">
	<h2 style="display: inline-block;margin-right: 5px;">Dictionaries</h2>
	<input type="hidden" name="controller" value="Settings">

	<a href="<?php echo Helper::admin_url(['page' => 'bkl-add-new']); ?>" class="page-title-action">Add New</a>
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
			<select name="action" id="bulk-action-selector-top">
				<option value="">Bulk Actions</option>
				<option value="dictionary-reindex">Reindex</option>
				<option value="dictionary-bulk-delete">Delete</option>
			</select>
			<input type="submit" class="button action" value="Apply">
		</div>
	</div>
	<hr class="wp-header-end">


	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
				<th scope="col" id="name" class="manage-column column-name column-primary sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'name' && isset($_GET['order']) ? $_GET['order'] : 'asc'; ?>"><a href="<?php echo Helper::admin_url(['page' => 'bkl-dictionary', 'orderby' => 'name', 'order' => isset($_GET['orderby']) && $_GET['orderby'] === 'name' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc']); ?>"><span>Name</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="lang" class="manage-column column-lang sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'lang' && isset($_GET['order']) ? $_GET['order'] : 'asc'; ?>"><a href="<?php echo Helper::admin_url(['page' => 'bkl-dictionary', 'orderby' => 'lang', 'order' => isset($_GET['orderby']) && $_GET['orderby'] === 'lang' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc']); ?>"><span>Language</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="analyzer" class="manage-column column-analyzer sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'analyzer' && isset($_GET['order']) ? $_GET['order'] : 'asc'; ?>"><a href="<?php echo Helper::admin_url(['page' => 'bkl-dictionary', 'orderby' => 'analyzer', 'order' => isset($_GET['orderby']) && $_GET['orderby'] === 'analyzer' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc']); ?>"><span>Analyzer</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="terms" class="manage-column column-terms"><span>Terms</span></th>
			</tr>
		</thead>

		<tbody id="the-list">
			<?php if (empty($dictionaries)) : ?>
				<tr></tr>
			<?php else : ?>
				<?php foreach ($dictionaries as $i => $dictionary) : ?>
					<tr id="post-1" class="iedit level-0 post-<?php echo $i; ?> format-standard hentry">
						<th scope="row" class="check-column">
							<label class="screen-reader-text" for="cb-select-1">Select <?php echo $dictionary->get_name(); ?></label>
							<input id="cb-select-1" type="checkbox" name="dictionary[]" value="<?php echo $dictionary->get_lang(); ?>">
						</th>
						<td class="name column-name column-primary" data-colname="Name">
							<strong><a class="row-name" href="<?php echo Helper::admin_url(['page' => 'bkl-dictionary', 'action' => 'edit', 'lang' => $dictionary->get_lang()]); ?>" aria-label="“<?php echo $dictionary->get_name(); ?>” (Edit)"><?php echo $dictionary->get_name(); ?></a></strong>
						</td>
						<td class="lang column-lang" data-colname="Language"><?php echo $dictionary->get_lang(); ?></td>
						<td class="analyzer column-analyzer" data-colname="Analyzer"><?php echo $dictionary->get_analyzer(); ?></td>
						<td class="terms column-terms" data-colname="Terms"><?php echo $dictionary->terms()->get_count(); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>

		<tfoot>
			<tr>
				<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></td>
				<th scope="col" id="name" class="manage-column column-name column-primary sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'name' && isset($_GET['order']) ? $_GET['order'] : 'asc'; ?>"><a href="<?php echo Helper::admin_url(['page' => 'bkl-dictionary', 'orderby' => 'name', 'order' => isset($_GET['orderby']) && $_GET['orderby'] === 'name' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc']); ?>"><span>Name</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="lang" class="manage-column column-lang sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'lang' && isset($_GET['order']) ? $_GET['order'] : 'asc'; ?>"><a href="<?php echo Helper::admin_url(['page' => 'bkl-dictionary', 'orderby' => 'lang', 'order' => isset($_GET['orderby']) && $_GET['orderby'] === 'lang' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc']); ?>"><span>Language</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="analyzer" class="manage-column column-analyzer sortable <?php echo isset($_GET['orderby']) && $_GET['orderby'] === 'analyzer' && isset($_GET['order']) ? $_GET['order'] : 'asc'; ?>"><a href="<?php echo Helper::admin_url(['page' => 'bkl-dictionary', 'orderby' => 'analyzer', 'order' => isset($_GET['orderby']) && $_GET['orderby'] === 'analyzer' && isset($_GET['order']) && $_GET['order'] === 'asc' ? 'desc' : 'asc']); ?>"><span>Analyzer</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="terms" class="manage-column column-terms"><span>Terms</span></th>
			</tr>
		</tfoot>
	</table>
</form>