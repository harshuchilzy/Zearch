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
			<h2>Zearch</h2>
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

		add_settings_section(
			'zearch_setting_section', // id
			'Settings', // title
			array( $this, 'zearch_section_info' ), // callback
			'zearch-admin' // page
		);

	   
		 if ( $post_types ) { // If there are any custom public post types.
		     
			 foreach ( $post_types  as $post_type ) {
			
				add_settings_field(
					'title_'.$post_type , // id
					'Title : <span class="dayz-postype">'.$post_type.'</span>', // title
					array( $this, 'title_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section', // section
					$post_type
				);

				add_settings_field(
					'content_'.$post_type , // id
					'Content : <span class="dayz-postype">'.$post_type.'</span>', // title
					array( $this, 'content_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section', // section
					$post_type
				);

				add_settings_field(
					'excerpt_'.$post_type , // id
					'Excerpt : <span class="dayz-postype">'.$post_type.'</span>', // title
					array( $this, 'excerpt_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section', // section
					$post_type
				);

				add_settings_field(
					'author_'.$post_type , // id
					'Author : <span class="dayz-postype">'.$post_type.'</span>', // title
					array( $this, 'author_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section', // section
					$post_type
				);

				

			   global $wp_post_types;
			   $objs = $wp_post_types[$post_type];
			   $taxonomy_objects = get_object_taxonomies($post_type);
			   $name = $objs->taxonomies;
			   
			//    print_r($taxonomy_objects[0]);
			//    return;
               foreach ($taxonomy_objects as $taxonomy_object) {
					add_settings_field(
					$taxonomy_object , // id
					$taxonomy_object , // title
					array( $this, 'cats_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section', // section
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
		<span  class="slider_label"></span>', isset( $this->zearch_options['title_width_'.$post_type] ) ? esc_attr( $this->zearch_options['title_width_'.$post_type]) : '');
	}

	public function content_callback($post_type) {
		printf('<input type="checkbox" name="zearch_option_name[content_'.$post_type.']" id="content_'.$post_type.'" value="content_'.$post_type.'" %s> <label for="content_'.$post_type.'">Searchable</label> |',
		( isset( $this->zearch_options['content_'.$post_type] ) && $this->zearch_options['content_'.$post_type] === 'content_'.$post_type ) ? 'checked' : '' ); 

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[content_width_'.$post_type.']"  class="slider" min="0" max="100" value="%s">
		<span  class="slider_label"></span>', isset( $this->zearch_options['content_width_'.$post_type] ) ? esc_attr( $this->zearch_options['content_width_'.$post_type]) : '');
	}

	public function excerpt_callback($post_type) {
		printf('<input type="checkbox" name="zearch_option_name[excerpt_'.$post_type.']" id="excerpt_'.$post_type.'" value="excerpt_'.$post_type.'" %s> <label for="excerpt_'.$post_type.'">Searchable</label> |',
		( isset( $this->zearch_options['excerpt_'.$post_type] ) && $this->zearch_options['excerpt_'.$post_type] === 'excerpt_'.$post_type ) ? 'checked' : '' ); 

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[excerpt_width_'.$post_type.']"  class="slider" min="0" max="100" value="%s">
		<span  class="slider_label"></span>', isset( $this->zearch_options['excerpt_width_'.$post_type] ) ? esc_attr( $this->zearch_options['excerpt_width_'.$post_type]) : '');
	}

	public function author_callback($post_type) {
		printf('<input type="checkbox" name="zearch_option_name[author_'.$post_type.']" id="author_'.$post_type.'" value="author_'.$post_type.'" %s> <label for="author_'.$post_type.'">Searchable</label> |',
		( isset( $this->zearch_options['author_'.$post_type] ) && $this->zearch_options['author_'.$post_type] === 'author_'.$post_type ) ? 'checked' : '' ); 

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[author_width_'.$post_type.']"  class="slider" min="0" max="100" value="%s">
		<span  class="slider_label"></span>', isset( $this->zearch_options['author_width_'.$post_type] ) ? esc_attr( $this->zearch_options['author_width_'.$post_type]) : '');
	}

	public function cats_callback($taxonomy_object) {
		printf('<input type="checkbox" name="zearch_option_name[tax_'.$taxonomy_object.']" id="tax_'.$taxonomy_object.'" value="tax_'.$taxonomy_object.'" %s> <label for="tax_'.$taxonomy_object.'">Searchable</label> |',
		( isset( $this->zearch_options['tax_'.$taxonomy_object] ) && $this->zearch_options['tax_'.$taxonomy_object] === 'tax_'.$taxonomy_object ) ? 'checked' : '' ); 

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="zearch_option_name[tax_width_'.$taxonomy_object.']"  class="slider" min="0" max="100" value="%s">
		<span  class="slider_label"></span>', isset( $this->zearch_options['tax_width_'.$taxonomy_object] ) ? esc_attr( $this->zearch_options['tax_width_'.$taxonomy_object]) : '');
	}

	


}
if ( is_admin() )
	$zearch = new ZearchOptions();

//Api settings 
include('api-settings.php');

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>
  $(function(){
	$('.slider').on('input change', function(){
		$(this).next($('.slider_label')).html(this.value);
		
	});
	$('.slider_label').each(function(){
		var value = $(this).prev().attr('value');
		$(this).html(value);
	});  
	
})
</script>
<style>
.dayz-zearch-form .form-table th{
  text-transform: uppercase;
}
.dayz-zearch-form input[type=checkbox] + label {
  margin: 0.2em;
  cursor: pointer;
  padding: 0.2em;
}

.dayz-zearch-form input[type=checkbox] {
  display: none !important;
}

.dayz-zearch-form input[type=checkbox] + label:before {
  content: "\2714";
  border: 0.1em solid #000;
  border-radius: 0.2em;
  display: inline-block;
  width: 1em;
  height: 1em;
  padding-left: 0.2em;
  padding-bottom: 0.3em;
  margin-right: 0.2em;
  vertical-align: bottom;
  color: transparent;
  transition: .2s;
}

.dayz-zearch-form input[type=checkbox] + label:active:before {
  transform: scale(0);
}

.dayz-zearch-form input[type=checkbox]:checked + label:before {
  background-color: MediumSeaGreen;
  border-color: MediumSeaGreen;
  color: #fff;
}

.dayz-zearch-form input[type=checkbox]:disabled + label:before {
  transform: scale(1);
  border-color: #aaa;
}

.dayz-zearch-form input[type=checkbox]:checked:disabled + label:before {
  transform: scale(1);
  background-color: #bfb;
  border-color: #bfb;
}
.dayz-zearch-form .slider_label{
	background: #bfb;
    border: 1px solid;
    border-radius: 100%;
    font-weight: 500;
    padding: 5px;
}
.slider {
  -webkit-appearance: none;
  height: 10px;
  width:20%;
  background: #d3d3d3;
  outline: none;
  opacity: 0.7;
  -webkit-transition: .2s;
  transition: opacity .2s;
}

.slider:hover {
  opacity: 1;
}

.slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 15px;
  height: 15px;
  background: #04AA6D;
  cursor: pointer;
}

.slider::-moz-range-thumb {
  width: 15px;
  height: 15px;
  background: #04AA6D;
  cursor: pointer;
}
.dayz-postype{
	color: 04AA6D;
    border: 1px solid;
    padding: 5px;
}
</style>
<?