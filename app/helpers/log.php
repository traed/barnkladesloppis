<?php
	namespace eqhby\bkl;
	
	class Log {
		static private $log_dir = null;
		
		static public function __callStatic($name, $args) {
			if(is_null(self::$log_dir)) {
				$log_paths = wp_upload_dir();
				
				self::$log_dir = $log_paths['basedir'] . '/webbmaffian-logs-' . wp_create_nonce('logpath');
				
				if(!file_exists(self::$log_dir)) {
					mkdir(self::$log_dir, 0755);
				}
			}
			
			$name = str_replace('_', '-', strtolower($name));
			$now = Helper::date('now');
			
			return file_put_contents(self::$log_dir . '/' . $name . '.log', $now->format('Y-m-d @ H:i:s') . ' - ' . implode(' ', array_map(function($arg) {
				return (is_string($arg) ? $arg : var_export($arg, true));
			}, $args)) . "\n", FILE_APPEND);
		}
	}