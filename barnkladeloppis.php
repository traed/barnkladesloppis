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
		
		$this->init();
	}


	public function install() {
		$admin_role = add_role('bkl_admin', 'Loppis-admin');
		$seller_role = add_role('bkl_seller', 'Loppis-säljare');

		if($admin_role instanceof \WP_Role) {
			$admin_role->add_cap('bkl-view');
			$admin_role->add_cap('bkl-edit');
			$admin_role->add_cap('bkl-sell');
		}

		if($seller_role instanceof \WP_Role) {
			$admin_role->add_cap('bkl-view');
			$admin_role->add_cap('bkl-sell');
		}

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$tables = [];
		$charset = 'DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci';

		$tables[] = 'CREATE TABLE IF NOT EXISTS ' . Occasion::get_table() . ' (
			id int NOT NULL AUTO_INCREMENT,
			date_start date NOT NULL,
			date_signup date NOT NULL,
			PRIMARY KEY (id)
		) ' . $charset;
		
		foreach($tables as $table) {
			dbDelta($table . ' ' . $charset);
		}
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