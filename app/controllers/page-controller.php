<?php

namespace eqhby\bkl;

class Page_Controller extends Controller {
	public function allow_admin_callback() {
		global $post;

		$allow_bkl_admin = get_post_meta($post->ID, 'allow_bkl_admin', true) ?: '';

		wp_nonce_field('bkl_page_save', 'bkl_metabox_nonce');
		?>
		<div>
			<label>
				<input type="checkbox" name="allow_bkl_admin" value="1"<?php echo ($allow_bkl_admin ? ' checked' : '') . (!current_user_can('administrator') ? ' readonly' : ''); ?>>
				Tillåt att Loppis-admin redigerar
			</label>
		</div>
		<?php
	}
}