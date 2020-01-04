<?php

namespace eqhby\bkl;

class Occasion {
	private $post;


	public function __construct($post) {
		$this->post = $post;
	}


	static public function get_by_id(int $post_id) {
		$post = get_post($post_id);
		if(is_null($post)) {
			throw new Problem('Invalid post.');
		}

		return new self($post);
	}


	static public function get_future($limit = -1) {
		$now = Helper::date('now')->format('Y-m-d');

		$query = [
			'post_type' => 'bkl_occasion',
			'post_status' => 'publish',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => 'date_start',
					'compare' => '>=',
					'value' => $now,
					'type' => 'DATE'
				],
				[
					'key' => 'date_signup',
					'compare' => '<=',
					'value' => $now,
					'type' => 'DATE'
				]
			],
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'numberposts' => (int)$limit
		];

		$posts = get_posts($query);
		$occasions = array_map(function($p) {
			return new self($p);
		}, $posts);

		return $occasions;
	}


	static public function get_all() {
		$query = [
			'post_type' => 'bkl_occasion',
			'post_status' => 'publish',
			'meta_key' => 'date_start',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'numberposts' => -1
		];

		$posts = get_posts($query);
		$occasions = array_map(function($p) {
			return new self($p);
		}, $posts);

		return $occasions;
	}


	static public function get_next() {
		$occasions = self::get_future(1);
		if($occasion = reset($occasions)) {
			return $occasion;
		}

		return false;
	}


	static public function get_seller_number() {
		global $wpdb;
		
		$locked = get_option('bkl_locked_numbers', []);
		$in_use = $wpdb->get_col("
			SELECT meta_value
			FROM $wpdb->usermeta
			WHERE meta_key = 'seller_number'
		");
		$in_use = array_filter($in_use, 'intval');

		$unavailable = array_merge($locked, $in_use);
		sort($unavailable, SORT_NUMERIC);

		$available = range(1, end($unavailable) + 1);
		$available = array_diff($available, $unavailable);

		return reset($available);
	}


	public function __call($name, $args = array()) {
		list($type, $name) = explode('_', $name, 2);

		$meta = get_post_meta($this->post->ID);
		
		if($type === 'get') {
			if(isset($this->post->$name)) {
				return $this->post->$name;
			}

			if(isset($meta[$name])) {
				return $meta[$name];
			}

			return false;
		} elseif($type === 'has') {
			return !empty($this->post->$name) || !empty($meta[$name]);
		}
	}


	public function get_users($status = false, $only_ids = false) {
		global $wpdb;

		$query = '
			SELECT user_id
			FROM ' . Helper::get_table('occasion_users') . '
			WHERE occasion_id = %d
		';

		if($status) {
			$query .= ' AND status = %s';
			$query = $wpdb->prepare($query, $this->get_ID(), $status);
		} else {
			$query = $wpdb->prepare($query, $this->get_ID());
		}

		$user_ids = $wpdb->get_col($query);

		if($only_ids) return $user_ids;
		
		$users = [];
		if(!empty($user_ids)) {
			$users = get_users([
				'include' => $user_ids,
				'orderby' => 'name',
				'order' => 'ASC'
			]);
		}

		return $users;
	}


	public function count_users($status = false) {
		global $wpdb;

		$query = '
			SELECT COUNT(user_id)
			FROM ' . Helper::get_table('occasion_users') . '
			WHERE occasion_id = %d
		';

		if($status) {
			$query .= ' AND status = %s';
			$query = $wpdb->prepare($query, $this->get_ID(), $status);
		} else {
			$query = $wpdb->prepare($query, $this->get_ID());
		}

		return $wpdb->get_var($query);
	}


	public function get_user_status($user_id) {
		global $wpdb;

		$query = $wpdb->prepare('
			SELECT status
			FROM ' . Helper::get_table('occasion_users') . '
			WHERE occasion_id = %d AND user_id = %d
		', $this->get_ID(), $user_id);

		return $wpdb->get_var($query) ?: 'none';
	}


	public function add_user($user_id, $status = false) {
		global $wpdb;
		
		if(!$status) {
			$status = $this->count_users('signed_up') < $this->get_num_spots() ? 'signed_up' : 'reserve';
		}

		$seller_number = (int)get_user_meta($user_id, 'seller_number', true);
		if(empty($seller_number)) {
			$seller_number = self::get_seller_number();
			update_user_meta($user_id, 'seller_number', $seller_number);
		}

		$created = Helper::date('now')->format('Y-m-d H:i:s');

		$query = $wpdb->prepare('
			INSERT INTO ' . Helper::get_table('occasion_users') . ' (occasion_id, user_id, time_created, time_updated, status)
			VALUES (%d, %d, %s, %s, %s)
			ON DUPLICATE KEY UPDATE time_updated = %s, status = %s
		', $this->get_ID(), $user_id, $created, $created, $status, $created, $status);

		return $wpdb->query($query) !== false ? $status : false;
	}
}