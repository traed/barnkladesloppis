<?php

namespace eqhby\bkl;

use Webbmaffian\MVC\Helper\Value;

class Settings_Controller extends Controller {

	public function show_settings_page() {
		if(isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
			$occasion = Occasion::get_by_id((int)$_GET['id']);
			Value::set($occasion->get_data());
			include(Plugin::PATH . '/app/views/settings/edit.php');
		} else {
			$orderby = 'name';
			$order = 'asc';
	
			if(isset($_GET['orderby'])) {
				$orderby = sanitize_key($_GET['orderby']);
				if(isset($_GET['order'])) {
					$order = sanitize_key($_GET['order']);
				}
			}
	
			$occasions = Occasion::collection()->order_by($orderby, $order)->get();
			
			include(Plugin::PATH . '/app/views/settings/settings.php');
		}
	}


	public function show_add_new_page() {
		include(Plugin::PATH . '/app/views/settings/edit.php');
	}


	public function handle_post($action) {
		if(in_array($action, ['add', 'edit'])) {
			$data = [
				'date_start' => strtotime($_POST['date_start']) ? $_POST['date_start'] : false,
				'date_signup' => strtotime($_POST['date_signup']) ? $_POST['date_signup'] : false
			];
		}

		if($action === 'add') {
			try {
				$occasion = Occasion::create($data);

				Admin::notice('Loppis-tillfälle skapat!', 'success');
				wp_redirect(Helper::admin_url(['page' => Plugin::SLUG, 'action' => 'edit', 'id' => $occasion->get_id()]));
				exit;
			} catch(Problem $p) {
				Log::error($p->getMessage());
				Admin::notice('Ett fel inträffade. Se systemloggen för mer info.', 'error');
			}
		}

		elseif($action === 'edit') {
			try {
				$occasion = Occasion::get_by_id((int)$_POST['id']);
				$occasion->update($data);
				
				Admin::notice('Loppis-tillfälle uppdaterat!', 'success');
				wp_redirect(Helper::admin_url(['page' => Plugin::SLUG, 'action' => 'edit', 'id' => $occasion->get_id()]));
				exit;
			} catch(Problem $p) {
				Log::error($p->getMessage());
				Admin::notice('Ett fel inträffade. Se systemloggen för mer info.', 'error');
			}
		}

		elseif($action === 'delete') {
			try {
				$occasion = Occasion::get_by_id((int)$_POST['id']);
				$occasion->delete();
				
				Admin::notice('Loppis-tillfälle raderat!', 'success');
			} catch(Problem $p) {
				Log::error($p->getMessage());
				Admin::notice('Ett fel inträffade. Se systemloggen för mer info.', 'error');
			}
		}

		elseif($action === 'bulk-delete') {
			$occations = array_map('intval', $_POST['occation']);
			$error = false;

			foreach($occations as $occation_id) {
				try {
					$occation = Occasion::get_by_id($occation_id);
					$occation->delete();
				} catch(Problem $p) {
					Log::error($p->getMessage());
					$error = true;
				}
			}

			if($error) {
				Admin::notice('Ett fel inträffade. Se systemloggen för mer info.', 'error');
			} else {
				Admin::notice('Loppis-tillfällen raderade!', 'success');
			}
		}

		wp_redirect(Helper::admin_url(['page' => Plugin::SLUG]));
		exit;
	}
}