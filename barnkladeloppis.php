<?php
/*
	Plugin Name: Barnklädesloppis
	Description: Plugin som hanterar barnklädesloppisens anmälningssystem.
	Version: 1.0.0
	Author: Mattias Forsman
	Author URI: https://github.com/traed
*/

namespace eqhby\bkl;

abstract class Plugin {
	const VERSION = '1.0.0';
	const SLUG = 'bkl';
	const TABLE_PREFIX = 'bkl_';
	const FILE = __FILE__;
	const PATH = __DIR__;
	const EMAIL_ADDRESS = '';
	const MAIN_LANG = 'en';
	
	
	static public function get_url() {
		return plugin_dir_url(self::FILE);
	}
	
	
	public function __construct() {
		// Create/update database tables on activation
		register_activation_hook(self::FILE, array($this, 'install'));
		
		// Autoload all classes
		spl_autoload_register(array($this, 'autoloader'));

		// Composer
		if(file_exists(self::PATH . '/vendor/autoload.php')) {
			require_once(self::PATH . '/vendor/autoload.php');
		}

		add_action('init', array($this, 'add_custom_post_type'));
		add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
		add_action('save_post', array($this, 'save_meta_fields'));
		add_action('new_to_publish', array($this, 'save_meta_fields'));
		
		$this->init();
	}


	public function install() {
		add_role(
			'bkl_admin',
			'Loppis-admin',
			[
				'read' => true,
				'read_bkl_occasions' => true,
				'edit_bkl_occasions' => true,
				'edit_others_bkl_occasions' => true,
				'publish_bkl_occasions' => true,
				'read_private_bkl_occasions' => true,
				'delete_bkl_occasions' => true,
				'delete_private_bkl_occasions' => true,
				'delete_published_bkl_occasions' => true,
				'delete_others_bkl_occasions' => true,
				'edit_private_bkl_occasions' => true,
				'edit_published_bkl_occasions' => true,
			]
		);
		add_role(
			'bkl_seller',
			'Loppis-säljare'
		);
	}
	
	
	protected function autoloader($name) {
		if(strncmp($name, __NAMESPACE__, strlen(__NAMESPACE__)) !== 0) return;
		
		$classname = trim(substr($name, strlen(__NAMESPACE__)), '\\');
		$filename = strtolower(str_replace('_', '-', $classname));
		
		$paths = array(
			self::PATH . '/app/models/' . $filename . '.php',
			self::PATH . '/app/models/collections/' . $filename . '.php',
			self::PATH . '/app/helpers/' . $filename . '.php'
		);
		foreach($paths as $path) {
			if(!file_exists($path)) continue;
			
			include($path);
		}
	}


	public function add_custom_post_type() {
		register_post_type('bkl_occasion', [
			'labels' => [
				'name'                     => 'Loppisar',
				'singular_name'            => 'Loppis',
				'add_new'                  => 'Skapa ny',
				'add_new_item'             => 'Skapa ny loppis',
				'new_item'                 => 'Ny loppis',
				'edit_item'                => 'Redigera loppis',
				'view_item'                => 'Visa loppis',
				'all_items'                => 'Alla loppisar',
				'search_items'             => 'Sök loppisar',
				'not_found'                => 'Hittade inga loppisar',
				'not_found_in_trash'       => 'Inga loppisar hittades i papperskorgen.'
			],
			'description' => 'Ett loppistillfälle',
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'exclude_from_search' => true,
			'map_meta_cap' => true,
			'capability_type' => 'bkl_occasion',
			'supports' => ['title', 'editor', 'revisions', 'thumbnail']
		]);
	}


	public function add_custom_meta_box() {
		add_meta_box('bkl_settings', 'Inställningar', array($this, 'occasion_meta_box_callback'), 'bkl_occasion', 'side');
	}


	public function occasion_meta_box_callback() {
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


	public function save_meta_fields($post_id) {
		if(!isset($_POST['bkl_metabox_nonce']) || !wp_verify_nonce($_POST['bkl_metabox_nonce'], 'bkl_occasion_save')) {
			return 'nonce not verified';
		}

		if(wp_is_post_autosave($post_id)) {
			return 'autosave';
		}
	
	  	if(wp_is_post_revision($post_id)) {
			return 'revision';
		}

		// if($_POST['post_type'] === 'bkl_occasion') {
		// 	if(!current_user_can('edit_bkl_occasion', $post_id)) {
		// 		return 'cannot edit occasion';
		// 	}
		// }

		$date_start = strtotime($_POST['date_start']) ? $_POST['date_start'] : '';
		$date_signup = strtotime($_POST['date_signup']) ? $_POST['date_signup'] : '';

		update_post_meta($post_id, 'date_start', $date_start);
		update_post_meta($post_id, 'date_signup', $date_signup);
		update_post_meta($post_id, 'num_spots', (int)$_POST['num_spots']);
		update_post_meta($post_id, 'seller_fee', (int)$_POST['seller_fee']);
	}
	
	
	protected function init() {
		// Should be overriden by extending class
	}
}

if(is_admin()) {
	require_once(Plugin::PATH . '/includes/admin.php');
}
else {
	require_once(Plugin::PATH . '/includes/frontend.php');
}