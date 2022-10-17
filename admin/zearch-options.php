<?php

use Elastic\Elasticsearch\ClientBuilder;

class ZearchOptions {
	private $zearch_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'zearch_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'zearch_page_init' ) );
	}

	public function zearch_add_plugin_page() {
		add_menu_page(
			'Zearch', // page_title
			'Zearch', // menu_title
			'manage_options', // capability
			'zearch', // menu_slug
			array( $this, 'zearch_create_admin_page' ), // function
			'dashicons-search', // icon_url
			2 // position
		);
	}

	public function zearch_create_admin_page() {
		$this->zearch_options = get_option( 'zearch_option_name' );

		// print_r($this->zearch_options);
		// return; 

        $client = ClientBuilder::create()
        ->setHosts(['http://88.198.32.151:9200'])
        // ->setApiKey()
        ->build();

        $response = $client->info();
        // echo '<pre>';
		// print_r($response);
		// echo '</pre>';
		// echo $response['version']['number']; // 8.0.0

        ?>

		<div class="wrap dayz-zearch-form">
			<h2>Zearch Settings</h2>
			<p></p>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'zearch_option_group' );
					do_settings_sections( 'zearch-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function zearch_page_init() {

		

		$args = array(
			'public'   => true,
			'_builtin' => true,
		 );
		  
		 $output = 'names'; // 'names' or 'objects' (default: 'names')
		 $operator = 'and'; // 'and' or 'or' (default: 'and')
		
		 $post_types = get_post_types( $args, $output, $operator );

		 
		register_setting(
			'zearch_option_group', // option_group
			'zearch_option_name', // option_name
			array( $this, 'zearch_sanitize', $post_types ) // sanitize_callback
		);
            
		 if ( $post_types ) { // If there are any custom public post types.
		     
			 foreach ( $post_types  as $post_type ) {
				
				add_settings_section(
					'zearch_setting_section'.$post_type, // id
					'<span class="postype-title">'.$post_type.'</span>', // title
					array( $this, 'zearch_section_info' ), // callback
					'zearch-admin' // page
				);

				add_settings_field(
					'title_'.$post_type , // id
					'Title : <span class="dayz-postype">'.$post_type.'</span>', // title
					array( $this, 'title_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section'.$post_type, // section
					$post_type
				);

				add_settings_field(
					'content_'.$post_type , // id
					'Content : <span class="dayz-postype">'.$post_type.'</span>', // title
					array( $this, 'content_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section'.$post_type, // section
					$post_type
				);

				add_settings_field(
					'excerpt_'.$post_type , // id
					'Excerpt : <span class="dayz-postype">'.$post_type.'</span>', // title
					array( $this, 'excerpt_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section'.$post_type, // section
					$post_type
				);

				add_settings_field(
					'author_'.$post_type , // id
					'Author : <span class="dayz-postype">'.$post_type.'</span>', // title
					array( $this, 'author_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section'.$post_type, // section
					$post_type
				);
				

			   global $wp_post_types;
			   $objs = $wp_post_types[$post_type];
			   $taxonomy_objects = get_object_taxonomies($post_type);
			   $name = $objs->taxonomies;
			   
			//    print_r($taxonomy_objects[0]);
			//    return;
               foreach ($taxonomy_objects as $taxonomy_object) {
				add_settings_section(
					'zearch_setting_cat_section', // id
					'', // title
					array( $this, 'zearch_section_info' ), // callback
					'zearch-admin' // page
				);
					add_settings_field(
						$taxonomy_object , // id
						'Taxonomies : <span class="dayz-postype">'.$taxonomy_object.'</span>', // title
						array( $this, 'cats_callback' ), // callback
						'zearch-admin', // page
						'zearch_setting_cat_section', // section
						$taxonomy_object
					);
			   }
			  
			}
			

		 }
		
		
	
	}

	public function zearch_sanitize($input, $post_types) {
		$sanitary_values = array();

		foreach ($post_types as $post_type) {
			if ( isset( $input['title_'.$post_type] ) ) {
				$sanitary_values['title_'.$post_type] = $input['title_'.$post_type];
			}
	
			if ( isset( $input['content_'.$post_type] ) ) {
				$sanitary_values['content_'.$post_type] = $input['content_'.$post_type];
			}
	
			if ( isset( $input['author_'.$post_type] ) ) {
				$sanitary_values['author_'.$post_type] = $input['author_'.$post_type];
			}
		}
        
		
		return $sanitary_values;
	}

	public function zearch_section_info() {
		
	}
	
	public function title_callback($post_type) {
		printf('<input type="checkbox" name="zearch_option_name[title_'.$post_type.']" id="title_'.$post_type.'" value="title_'.$post_type.'" %s> <label for="title_'.$post_type.'">Searchable</label> |',
		( isset( $this->zearch_options['title_'.$post_type] ) && $this->zearch_options['title_'.$post_type] === 'title_'.$post_type ) ? 'checked' : '' ); 
		
		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[title_width_'.$post_type.']"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset( $this->zearch_options['title_width_'.$post_type] ) ? esc_attr( $this->zearch_options['title_width_'.$post_type]) : '');
	}

	public function content_callback($post_type) {
		printf('<input type="checkbox" name="zearch_option_name[content_'.$post_type.']" id="content_'.$post_type.'" value="content_'.$post_type.'" %s> <label for="content_'.$post_type.'">Searchable</label> |',
		( isset( $this->zearch_options['content_'.$post_type] ) && $this->zearch_options['content_'.$post_type] === 'content_'.$post_type ) ? 'checked' : '' ); 

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[content_width_'.$post_type.']"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset( $this->zearch_options['content_width_'.$post_type] ) ? esc_attr( $this->zearch_options['content_width_'.$post_type]) : '');
	}

	public function excerpt_callback($post_type) {
		printf('<input type="checkbox" name="zearch_option_name[excerpt_'.$post_type.']" id="excerpt_'.$post_type.'" value="excerpt_'.$post_type.'" %s> <label for="excerpt_'.$post_type.'">Searchable</label> |',
		( isset( $this->zearch_options['excerpt_'.$post_type] ) && $this->zearch_options['excerpt_'.$post_type] === 'excerpt_'.$post_type ) ? 'checked' : '' ); 

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[excerpt_width_'.$post_type.']"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset( $this->zearch_options['excerpt_width_'.$post_type] ) ? esc_attr( $this->zearch_options['excerpt_width_'.$post_type]) : '');
	}

	public function author_callback($post_type) {
		printf('<input type="checkbox" name="zearch_option_name[author_'.$post_type.']" id="author_'.$post_type.'" value="author_'.$post_type.'" %s> <label for="author_'.$post_type.'">Searchable</label> |',
		( isset( $this->zearch_options['author_'.$post_type] ) && $this->zearch_options['author_'.$post_type] === 'author_'.$post_type ) ? 'checked' : '' ); 

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[author_width_'.$post_type.']"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset( $this->zearch_options['author_width_'.$post_type] ) ? esc_attr( $this->zearch_options['author_width_'.$post_type]) : '');
	}

	public function cats_callback($taxonomy_object) {
		printf('<input type="checkbox" name="zearch_option_name[tax_'.$taxonomy_object.']" id="tax_'.$taxonomy_object.'" value="tax_'.$taxonomy_object.'" %s> <label for="tax_'.$taxonomy_object.'">Searchable</label> |',
		( isset( $this->zearch_options['tax_'.$taxonomy_object] ) && $this->zearch_options['tax_'.$taxonomy_object] === 'tax_'.$taxonomy_object ) ? 'checked' : '' ); 

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[tax_width_'.$taxonomy_object.']"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset( $this->zearch_options['tax_width_'.$taxonomy_object] ) ? esc_attr( $this->zearch_options['tax_width_'.$taxonomy_object]) : '');
	}

	


}
if ( is_admin() )
	$zearch = new ZearchOptions();

//Api settings 
include('api-settings.php');
