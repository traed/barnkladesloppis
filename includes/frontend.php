<?php
	namespace eqhby\bkl;
	
	class Frontend extends Plugin {
		
		protected function init() {
			require_once(self::PATH . '/app/controllers/controller.php');
			
			add_action('init', array($this, 'route'));
			add_action('wp_enqueue_scripts', array($this, 'assets'));

			add_action('webbmaffian_before_content', [$this, 'before_content']);
			add_action('webbmaffian_after_content', [$this, 'after_content']);
		}
		
		
		public function route() {
			try {
				$router = new Router(include(self::PATH . '/app/routes.php'));
				$path = parse_url(get_site_url(), PHP_URL_PATH);
				$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
				
				if(!empty($path)) {
					$uri = substr($uri, strlen($path));
				}
				if($router->route($uri)) {
					exit;
				}
			}
			catch(Problem $e) {
				echo $e->getMessage();
			}
		}


		public function assets() {
			wp_enqueue_style(Plugin::SLUG . '-css', Plugin::get_url() . '/assets/css/frontend.css', [], Plugin::VERSION);
		}


		public function before_content() {
			echo '<div class="bkl-wrapper">';
		}


		public function after_content() {
			echo '</div><!-- .bkl-wrapper -->';
		}
	}
	
	new Frontend();
