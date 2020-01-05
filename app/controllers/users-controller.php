<?php

namespace eqhby\bkl;

class Users_Controller extends Controller {

	public function init() {
		if(isset($_GET['id'])) {
			if(is_numeric($_GET['id'])) {
				$this->show_single_user((int)$_GET['id']);
			} else {
				$this->show_new_user();
			}
		} else {
			$this->show_all_users();
		}
	}


	private function show_all_users() {
		$orderby = 'user_email';
		$meta_key = 'first_name';
		$order = 'ASC';
		$page = 1;

		if(isset($_GET['orderby'])) {
			$orderby = sanitize_key($_GET['orderby']);

			if(in_array($orderby, ['first_name', 'last_name', 'seller_id'])) {
				$meta_key = $orderby;
				$orderby = 'meta_value';
			}

			if(isset($_GET['order'])) {
				$order = sanitize_key($_GET['order']);
			}
		}

		if(isset($_GET['paged'])) {
			$page = (int)$_GET['paged'];
		}

		$query = [
			'role__in' => ['bkl_admin', 'bkl_seller'],
			'orderby' => $orderby,
			'order' => $order,
			'number' => 50,
			'paged' => $page
		];

		if(!empty($_GET['filter_occasion'])) {
			$occasion = Occasion::get_by_id((int)$_GET['filter_occasion']);
			$user_ids = $occasion->get_users(false, true);
			if(empty($user_ids)) {
				$user_ids[] = 0;
			}

			$query['include'] = $user_ids;
		}

		if($orderby === 'meta_value') {
			$query['meta_key'] = $meta_key;
		}

		$users_query = new \WP_User_Query($query);
		$total_items = $users_query->get_total();
		$users = (array)$users_query->get_results();
		$total_pages = ceil($total_items / max(1, count($users)));

		include(Plugin::PATH . '/app/views/admin/users.php');
	}


	private function show_single_user($id) {
		$occasion = Occasion::get_next();
		$can_sign_up = $occasion !== false;
		$user = get_user_by('ID', $id);
		if($user) {
			if(!in_array('bkl_seller', $user->roles) && !in_array('bkl_admin', $user->roles)) {
				Admin::notice('Du har inte behörighet att redigera den användaren', 'error');
				wp_redirect('/wp-admin/edit.php?post_type=bkl_occasion&page=bkl_users');
				exit;
			}

			$status = $occasion->get_user_status($user->ID, true);
		} else {
			Admin::notice('Felaktigt användar-ID.', 'error');
			wp_redirect('/wp-admin/edit.php?post_type=bkl_occasion&page=bkl_users');
			exit;
		}

		include(Plugin::PATH . '/app/views/admin/single-user.php');
	}


	private function show_new_user() {
		include(Plugin::PATH . '/app/views/admin/new-user.php');
	}


	public function pagination($total_items, $total_pages) {
		$infinite_scroll = false;

		$output = '<span class="displaying-num">' . sprintf(
			/* translators: %s: Number of items. */
			_n( '%s item', '%s items', $total_items ),
			number_format_i18n( $total_items )
		) . '</span>';

		$current              = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;
		$removable_query_args = wp_removable_query_args();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		$current_url = remove_query_arg( $removable_query_args, $current_url );

		$page_links = array();

		$total_pages_before = '<span class="paging-input">';
		$total_pages_after  = '</span></span>';

		$disable_first = false;
		$disable_last  = false;
		$disable_prev  = false;
		$disable_next  = false;

		if ( $current == 1 ) {
			$disable_first = true;
			$disable_prev  = true;
		}
		if ( $current == 2 ) {
			$disable_first = true;
		}
		if ( $current == $total_pages ) {
			$disable_last = true;
			$disable_next = true;
		}
		if ( $current == $total_pages - 1 ) {
			$disable_last = true;
		}

		if ( $disable_first ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='first-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				__( 'First page' ),
				'&laquo;'
			);
		}

		if ( $disable_prev ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='prev-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ) ),
				__( 'Previous page' ),
				'&lsaquo;'
			);
		}

		
		$html_current_page = sprintf(
			"%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
			'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
			$current,
			strlen( $total_pages )
		);
		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[]     = $total_pages_before . sprintf(
			/* translators: 1: Current page, 2: Total pages. */
			_x( '%1$s of %2$s', 'paging' ),
			$html_current_page,
			$html_total_pages
		) . $total_pages_after;

		if ( $disable_next ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='next-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ),
				__( 'Next page' ),
				'&rsaquo;'
			);
		}

		if ( $disable_last ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='last-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				__( 'Last page' ),
				'&raquo;'
			);
		}

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class .= ' hide-if-js';
		}
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}
		echo "<div class='tablenav-pages{$page_class}'>$output</div>";
	}


	public function handle_post($action) {
		if($action === 'edit_user' && isset($_POST['original_email']) && wp_verify_nonce($_POST['_wpnonce'], 'bkl_edit_user')) {
			$original_email = sanitize_email($_POST['original_email']);
			$user = get_user_by_email($original_email);

			if($user) {
				$first_name = sanitize_text_field($_POST['first_name']);
				$last_name = sanitize_text_field($_POST['last_name']);

				update_user_meta($user->ID, 'first_name', $first_name);
				update_user_meta($user->ID, 'last_name', $last_name);
				update_user_meta($user->ID, 'phone', sanitize_text_field($_POST['phone']));
				update_user_meta($user->ID, 'seller_id', (int)$_POST['seller_id']);

				$role = sanitize_key($_POST['role']);
				if(in_array($role, ['bkl_seller', 'bkl_admin']) && !in_array($role, $user->roles)) {
					$user->remove_role('bkl_admin');
					$user->remove_role('bkl_seller');
					$user->add_role($role);
				}

				$user_data = [
					'ID' => $user->ID,
					'display_name' => $first_name . ' ' . $last_name
				];

				$email = sanitize_email($_POST['email']);
				if($email !== $original_email) {
					if(email_exists($email)) {
						Admin::notice('E-postadressen finns redan i systemet. Prova med en annan.', 'warning');
					} else {
						$user_data['user_email'] = $email;
					}
				}

				wp_update_user($user_data);

				Admin::notice('Ändringarna har sparats!', 'success');

				if($occasion = Occasion::get_next()) {
					$status = sanitize_key($_POST['status']);
					if($status !== $occasion->get_user_status($user->ID)) {
						$result = $occasion->add_user($user->ID, $status);

						if($result === 'signed_up') {
							Admin::notice('Användare anmäld.', 'info');
						} elseif($result === 'reserve') {
							Admin::notice('Användare satt som reserv.', 'info');
						} elseif($result === 'none') {
							Admin::notice('Användare avanmäld.', 'info');
						} else {
							Admin::notice('Ett fel inträffade. Kunde inte ändra användarstatus.', 'error');
						}
					}
				}
			}

			wp_safe_redirect($_POST['_wp_http_referer']);
			exit;
		}

		elseif($action === 'new_user' && wp_verify_nonce($_POST['_wpnonce'], 'bkl_new_user')) {
			$first_name = sanitize_text_field($_POST['first_name']);
			$last_name = sanitize_text_field($_POST['last_name']);
			$email = sanitize_email($_POST['email']);
			$phone = sanitize_text_field($_POST['phone']);
			$role = $_POST['role'] === 'bkl_admin' ? 'bkl_admin' : 'bkl_seller';
			$password = wp_generate_password();

			$user_id = wp_create_user($email, $password, $email);
			if(is_wp_error($user_id)) {
				throw new Problem('Invalid input.');
			}

			$user = get_user_by('ID', $user_id);
			$user->set_role($role);

			wp_update_user([
				'ID' => $user_id,
				'display_name' => $first_name . ' ' . $last_name
			]);

			update_user_meta($user_id, 'first_name', $first_name);
			update_user_meta($user_id, 'last_name', $last_name);
			update_user_meta($user_id, 'phone', $phone);

			Admin::notice('Användare skapad!', 'success');
			wp_redirect('/wp-admin/edit.php?post_type=bkl_occasion&page=bkl_users&id=' . $user_id);
			exit;
		}

		elseif($action === 'filter') {
			$args = $_GET;

			if(isset($_POST['occasion']) && is_numeric($_POST['occasion']) && $_POST['occasion'] > 0) {
				$args['filter_occasion'] = (int)$_POST['occasion'];
			} else {
				unset($args['filter_occasion']);
			}

			$url = '/wp-admin/edit.php?' . http_build_query($args);
			wp_safe_redirect($url);
			exit;
		}

		elseif($action === 'bulk' && wp_verify_nonce($_POST['_wpnonce'], 'bkl_users_bulk_action')) {
			if(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'trash' && !empty($_POST['users'])) {
				global $wpdb;

				$user_ids = array_map('intval', $_POST['users']);
				foreach($user_ids as $user_id) {
					$wpdb->delete(Helper::get_table('occasion_users'), ['user_id' => $user_id]);
					wp_delete_user($user_id);
				}
			}

			elseif(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'export_all') {
				$now = Helper::date('now')->format('Y-m-d--H-i-s');

				if(isset($_GET['filter_occasion']) && is_numeric($_GET['filter_occasion'])) {
					$occasion = Occasion::get_by_id((int)$_GET['filter_occasion']);
					$users = $occasion->get_users();
					$filename = 'loppis-' . $occasion->get_post_name() . '-' . $now;
				} else {
					$users = get_users([
						'role__in' => ['bkl_seller', 'bkl_admin'],
						'orderby' => 'display_name',
						'order' => 'ASC'
					]);
					$filename = 'loppis-alla-' . $now;
				}

				Spreadsheet::export_users($users, $filename);
			}

			elseif(isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'export_some' && !empty($_POST['users'])) {
				$user_ids = array_map('intval', $_POST['users']);
				$users = get_users([
					'role__in' => ['bkl_seller', 'bkl_admin'],
					'include' => $user_ids,
					'orderby' => 'display_name',
					'order' => 'ASC'
				]);

				$now = Helper::date('now')->format('Y-m-d--H-i-s');
				Spreadsheet::export_users($users, 'loppis-utvalda-' . $now);
			}
		}
	}
}