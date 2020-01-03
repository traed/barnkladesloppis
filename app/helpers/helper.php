<?php
	namespace eqhby\bkl;
	
	class Helper {
		static protected $controllers = array();
		static protected $timezone = NULL;
		
		static public function get_table_name($name = '') {
			self::deprecated();
			
			return self::get_table($name);
		}
		

		static public function get_table($name = '') {
			global $wpdb;

			return $wpdb->prefix . Plugin::TABLE_PREFIX . $name;
		}


		static public function admin_url($args = array()) {
			$args = wp_parse_args($args, array(
				'page' => Plugin::SLUG
			));

			return  admin_url('admin.php?' . http_build_query($args));
		}


		static public function get_controller($controller, $is_admin = false) {
			if(substr($controller, -11) !== '_Controller') {
				$controller .= '_Controller';
			}

			if(!isset(self::$controllers[$controller])) {
				if($is_admin) {
					$folder = '/app/controllers/admin/';
				} else {
					$folder = '/app/controllers/';
				}
				
				$file_path = Plugin::PATH . $folder . strtolower(str_replace('_', '-', $controller)) . '.php';
				
				if(!class_exists(__NAMESPACE__  . '\Controller')) {
					include_once(Plugin::PATH . '/app/controllers/controller.php');
				}

				if(!file_exists($file_path)) {
					throw new Problem('File path does not exist: ' . $file_path);
				}

				include_once($file_path);
				$class_name = __NAMESPACE__  . '\\' . $controller;
				self::$controllers[$controller] = new $class_name();
			}

			return self::$controllers[$controller];
		}


		static public function get_referer($no_query = false) {
			$referer = parse_url($_SERVER['HTTP_REFERER']);
			$home = parse_url(home_url());

			if($referer['host'] !== $home['host']) {
				$url = home_url();
			} else {
				$url = $_SERVER['HTTP_REFERER'];
			}

			if($no_query) {
				$query = '?' . parse_url($url, PHP_URL_QUERY);
				$url = str_replace($query, '', $url);
			}

			return $url;
		}


		static public function append_to_url($url, $arg) {
			$query = parse_url($url, PHP_URL_QUERY);

			if ($query) {
				return $url . '&' . $arg;
			}

			return $url . '/?' . $arg;
		}


		static public function date($string, $convert_timezone = false) {
			if(is_null(self::$timezone)) {
				$timezone_string = get_option('timezone_string');
				
				if(!$timezone_string) {
					$timezone_string = timezone_name_from_abbr('', 3600 * (float)get_option('gmt_offset'), 0);
				}
				
				self::$timezone = new \DateTimeZone($timezone_string);
			}
			
			if(!$convert_timezone) {
				return new \DateTime($string, self::$timezone);
			}
			
			$date = new \DateTime($string);
			$date->setTimezone(self::$timezone);
			
			return $date;
		}
		

		static public function deprecated() {
			if(version_compare(PHP_VERSION, '5.4.0') >= 0) {
				$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
			} elseif(version_compare(PHP_VERSION, '5.3.6') >= 0) {
				$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			} else {
				$trace = debug_backtrace();
			}
			
			$caller = $trace[1];
			
			error_log('Deprecated function ' . $caller['class'] . $caller['type'] . $caller['function'] . ' used at ' . substr($caller['file'], strlen(ABSPATH) - 1) . ':' . $caller['line']);
		}
		

		static public function send_json($data = array()) {
			header('Content-Type: application/json; charset=UTF-8');
			echo json_encode($data);
			exit;
		}


		static public function callback($controller, $method) {
			return array(__CLASS__, 'callback_' . $controller . '__' . $method);
		}

		
		static public function __callStatic($name, $args = array()) {
			if(strncmp($name, 'callback_', 9) === 0) {
				list($controller, $method) = explode('__', substr($name, 9));
				return call_user_func_array(array(Helper::get_controller($controller), $method), $args);
			}
		}
	}
