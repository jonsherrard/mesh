<?php namespace Mesh;

	class JSON_Loader {

		function __construct( $file = null ) {
			if ( $file ) {
				$this->import_json_file($file);
			}
		}

		public function import_json_file($file) {
			$data = file_get_contents($file);
			if ($data) {
				$json = json_decode($data);
				if (isset($json->users)) {
					$this->import_users($json->users);
				}
				if (isset($json->posts)) {
					$this->import_posts($json->posts);
				}
				if (isset($json->terms)) {
					$this->import_terms($json->terms);
				}
				if (json_last_error()) {
					trigger_error( 'Mesh: There is an error in your JSON file : '.$file );
					return false;
				}
				return true;
			}
			return false;
		}

		protected function import_users($array) {
			foreach($array as $user_data) {
				$user = new User($user_data->display_name);
				foreach($user_data as $key => $value) {
					if (strstr($key, ':image')) {
						//insert image
						$image_key = explode(':', $key);
						$user->set_image($image_key[0], $value);
					} elseif (strstr($key, ':repeater')) {
						$rep_key = explode(':', $key);
						$user->set_repeater($rep_key[0], $value);
					} else {
						$user->set($key, $value);
					}

				}
			}
		}

		protected function import_posts($array) {
			foreach( $array as $post_data ) {
				$post = new Post( $post_data->post_title, 'post' );
				foreach( $post_data as $key => $value ) {
					if ( $key === "thumbnail" ) {
						$post->set_image( $key, $value );
					} else if ( $key === "terms" ) {
						$post->set_terms( $key, $value );
					} else {
						$post->set( $key, $value );
					}
				}
			}
		}

		protected function import_terms($array) {
			foreach( $array as $term_data ) {
				$term = new Term( $term_data->name, $term_data->taxonomy );
				if( $term->id ) {
					foreach( $term_data as $key => $value ) {
						$term->set( $key, $value );
					}
				}
			}
		}
	}
