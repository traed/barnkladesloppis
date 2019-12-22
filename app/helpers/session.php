<?php

	namespace eqhby\bkl;
	
	class Session {
		static public function start() {
			if(!session_id()) {
				session_start();
			}
		}
		
		
		static public function end() {
			self::start();
			
			$_SESSION = array();

			if(ini_get('session.use_cookies')) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
			}
	
			session_destroy();
		}
		
		
		static public function set($key, $value) {
			self::start();
			
			if(is_array($value) && isset($_SESSION[$key]) && is_array($_SESSION[$key])) {
				$_SESSION[$key] = array_merge($_SESSION[$key], $value);
			}
			else {
				$_SESSION[$key] = $value;
			}
		}
		
		
		static public function has($key) {
			self::start();
			
			return isset($_SESSION[$key]);
		}
		
		
		static public function get($key) {
			self::start();
			
			return (isset($_SESSION[$key]) ? $_SESSION[$key] : null);
		}
		
		
		static public function get_once($key) {
			self::start();
			
			$value = self::get($key);
			self::clear($key);
			return $value;
		}
		
		
		static public function clear($key) {
			self::start();
			
			if(isset($_SESSION[$key])) {
				unset($_SESSION[$key]);
			}
		}
		
		
		static public function __callStatic($name, $args = array()) {
			self::start();
			
			list($type, $name) = explode('_', $name, 2);
			
			if($type === 'get') {
				return $_SESSION[$name];
			}
			elseif($type === 'has') {
				return !empty($_SESSION[$name]);
			}
		}
	}