<?php
	namespace eqhby\bkl;
	
	abstract class Controller {
		const PATH = Plugin::PATH;
		
		static public $views_path;
		
		
		public function __construct() {
			self::$views_path = Plugin::PATH . '/app/views';
		}


		public function handle_error($e) {
			echo '<p class="error">' . $e->getMessage() . '</p>';
		}


		protected function set_title($title = '', ...$args) {
			if(!empty($args)) {
				$title = sprintf($title, ...$args);
			}

			add_filter('document_title_parts', function($parts) use($title) {
				$parts['title'] = $title;
				
				return $parts;
			});

			return $title;
		}

		
		protected function add_js($filename, $in_footer = false) {
			add_action('wp_enqueue_scripts', function() use($filename, $in_footer) {
				wp_enqueue_script(sanitize_title($filename), Plugin::get_url() . 'assets/js/' . $filename, array('jquery'), $in_footer);
			});
		}

		
		protected function add_body_class($new_classes = '') {
			if(!is_array($new_classes)) {
				$new_classes = array($new_classes);
			}
			
			add_filter('body_class', function($classes) use($new_classes) {
				return array_merge($classes, $new_classes);
			});
		}

		
		public function get_class_name() {
			$parts = explode('\\', get_class($this));
			return array_pop($parts);
		}
	}