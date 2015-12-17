<?php
/*
	Plugin Name: Mt8 Term Image
*/
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	$mti = new Mt8_Term_Image();
	$mti->register_hooks();
	
	class Mt8_Term_Image {
		
		private $supported_taxonomies = array(
			'category',
			'post_tag',
		);
		
		public function get_supported_taxonomies() {
			return apply_filters( 'mt8_term_image_supported_taxonomies', $this->supported_taxonomies );
		}

		public function register_hooks() {
			
			add_action( 'admin_menu', array( &$this, 'admin_menu') );
			
			$taxonomies = $this->get_supported_taxonomies();
			foreach ( $taxonomies as $taxonomy ) {
				add_action( "{$taxonomy}_add_form_fields",  array( &$this, 'taxonomy_add_form_fields' ) );
				add_action( "{$taxonomy}_edit_form_fields", array( &$this, 'taxonomy_edit_form_fields' ) );
			}
			
			add_action( 'edited_term',  array( &$this, 'saved_term' ), 10, 3 );
			add_action( 'created_term', array( &$this, 'saved_term' ), 10, 3 );
			
		}
		
		public function admin_menu() {
			
			add_action( 'admin_print_scripts-edit-tags.php', array( &$this, 'admin_scripts' ) ); 
			
		}

		public function admin_scripts() {
			
			wp_enqueue_media();
			wp_enqueue_script( 'mt8-term-image-js', plugins_url("js/mt8-term-image.js", __FILE__ ), array( 'jquery' ), filemtime( __DIR__.'/js/mt8-term-image.js'), false );  
			wp_enqueue_style(  'mt8-term-image-css', plugins_url("css/mt8-term-image.css", __FILE__ ) );
			
		}
		
		public function taxonomy_add_form_fields( $taxonomy ) {
		?>
		<div class="form-field term-image-wrap">
			<label for="term-image"><?php _e( 'Term Image' ); ?></label>
			<button id="mt8-term-image-up"><?php _e( 'Choose Term Image' ); ?></button>
			<div id="mt8-term-image">
				<input type="hidden" id="mt8-term-image-inp" name="mt8_term_image_inp" /> 
			</div>
		</div>
		<?php
		}
		
		public function taxonomy_edit_form_fields( $tag, $taxonomy ) {
		?>
		<tr class="form-field term-image-wrap">
			<th scope="row"><label for="term-image"><?php _e( 'Term Image' ); ?></label></th>
			<td>
				<button id="mt8-term-image-up"><?php _e( 'Choose Term Image' ); ?></button>
				<div id="mt8-term-image">
					<?php $this->the_term_image( $tag ); ?>
					<input type="hidden" id="mt8-term-image-inp" name="mt8_term_image_inp" value="<?php $this->the_term_image_id( $tag ); ?>" /> 
				</div>
			</td>
		</tr>
		<?php
		}

		public function the_term_image_id( $tag ) {
			echo $this->get_the_term_image_id( $tag );
		}
		
		public function get_the_term_image_id( $tag ) {
			if ( ! isset( $tag ) || ! 'WP_Term' == gettype( $tag ) ) {
				return '';
			}
			$term_id = $tag->term_id;
			$image_id = get_term_meta( $term_id, 'mt8_term_image', true );
			if ( $image_id ) {
				return $image_id;
			} else {
				return '';
			}
		}
		
		public function the_term_image( $tag ) {
			echo $this->get_the_term_image( $tag );
		}
		
		public function get_the_term_image( $tag ) {
			$image_id = $this->get_the_term_image_id( $tag );
			if ( $image_id ) {
				return wp_get_attachment_image( $image_id );
			} else {
				return '';
			}
		}
		
		public function saved_term( $term_id, $tt_id, $taxonomy ) {
			
			if ( ! in_array( $taxonomy, $this->supported_taxonomies ) ) {
				return;
			}
			if ( isset( $_POST[ 'mt8_term_image_inp' ] ) ) {
				update_term_meta( $term_id, 'mt8_term_image', sanitize_text_field( $_POST[ 'mt8_term_image_inp' ] ) );
			}
		}
		
	}
