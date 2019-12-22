<?php
	namespace eqhby\bkl;
	
	class Router {
		static protected $controllers = array();
		
		protected $routes = array();
		protected $keys = array();
		
		
		public function __construct($routes = array()) {
			$this->routes = $routes;
		}
		
		
		public function route($input) {
			$input = trim($input, '/');
			
			foreach($this->routes as $route => $controller) {
				$this->keys = array();
				
				$regex = preg_replace_callback('/\{([^\}]+)\}/', array($this, 'extract_args'), $route);
				$regex = '/^' . str_replace('/', '\/', $regex) . '$/';
				
				if(preg_match($regex, $input, $matches)) {
					if(!is_array($controller)) {
						throw new Problem('Invalid controller.');
					}
					
					$controller[0] = Helper::get_controller($controller[0]);
					
					$options = array(
						'wrap' => true
					);

					if(isset($controller[2])) {
						if(!is_array($controller[2])) {
							throw new Problem('Invalid controller.');
						}
						
						$options = $controller[2];
						unset($controller[2]);
					}

					ob_start();
					
					try {
						$args = (empty($this->keys) ? array() : array_combine($this->keys, array_slice($matches, 1)));

						if(method_exists($controller[0], 'run_always')) {
							$controller[0]->run_always($args);
						}

						call_user_func($controller, $args);
					}
					catch(Problem $e) {
						if($e instanceof Unauthorized) {
							status_header(403);
						}

						$controller[0]->handle_error($e);
					}
					
					$content = ob_get_clean();
					
					if($options['wrap']) {
						get_header();
						do_action('webbmaffian_before_content');
					}
					
					echo $content;
					unset($content);
					
					if($options['wrap']) {
						do_action('webbmaffian_after_content');
						get_footer();
					}
					
					return true;
				}
			}
			
			return false;
		}
		
		
		protected function extract_args($matches = array()) {
			$this->keys[] = $matches[1];
			
			// The slash will be escaped later
			return '([^/]+)';
		}
	}