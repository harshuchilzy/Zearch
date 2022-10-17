<?php

use Elastic\Elasticsearch\ClientBuilder;

class ZearchOptions
{
	private $zearch_options;

	public function __construct()
	{
		add_action('admin_menu', array($this, 'zearch_add_plugin_page'));
		add_action('admin_init', array($this, 'zearch_page_init'));
	}

	public function zearch_add_plugin_page()
	{
		add_menu_page(
			'Zearch', // page_title
			'Zearch', // menu_title
			'manage_options', // capability
			'zearch', // menu_slug
			array($this, 'zearch_create_admin_page'), // function
			'dashicons-search', // icon_url
			2 // position
		);
	}

	public function zearch_create_admin_page()
	{
		$this->zearch_options = get_option('ezearch');
		?>

		<div class="wrap dayz-zearch-form">
			<h2>Zearch Settings</h2>
			<p></p>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
				settings_fields('zearch_option_group');
				do_settings_sections('zearch-admin');
				submit_button();
				?>
			</form>
		</div>
<?php }

	public function zearch_page_init()
	{
		$args = array(
			'public'   => true,
		);

		$output = 'names'; // 'names' or 'objects' (default: 'names')
		$operator = 'and'; // 'and' or 'or' (default: 'and')

		$post_types = get_post_types($args, $output, $operator);


		register_setting(
			'zearch_option_group', // option_group
			'ezearch', // option_name
			array($this, 'zearch_sanitize', $post_types) // sanitize_callback
		);

		global $wp_post_types;
		if ($post_types) { // If there are any custom public post types.

			foreach ($post_types  as $post_type) {

				add_settings_section(
					'zearch_setting_section' . $post_type, // id
					'<span class="postype-title">' . $post_type . '</span>', // title
					array($this, 'zearch_section_info'), // callback
					'zearch-admin' // page
				);

				add_settings_field(
					'title_' . $post_type, // id
					'Title', // title
					array($this, 'title_callback'), // callback
					'zearch-admin', // page
					'zearch_setting_section' . $post_type, // section
					$post_type
				);

				add_settings_field(
					'content_' . $post_type, // id
					'Content', // title
					array($this, 'content_callback'), // callback
					'zearch-admin', // page
					'zearch_setting_section' . $post_type, // section
					$post_type
				);

				add_settings_field(
					'post_excerpt_' . $post_type, // id
					'post_excerpt', // title
					array($this, 'post_excerpt_callback'), // callback
					'zearch-admin', // page
					'zearch_setting_section' . $post_type, // section
					$post_type
				);

				add_settings_field(
					'author_' . $post_type, // id
					'Author', // title
					array($this, 'author_callback'), // callback
					'zearch-admin', // page
					'zearch_setting_section' . $post_type, // section
					$post_type
				);

				// $objs = $wp_post_types[$post_type];
				$taxonomy_objects = get_object_taxonomies(array( 'post_type' => $post_type ));
				// $name = $objs->taxonomies;

				foreach ($taxonomy_objects as $taxonomy_object) {
					add_settings_section(
						'zearch_setting_cat_section_' . sanitize_title($post_type), // id
						'', // title
						array($this, 'zearch_section_info'), // callback
						'zearch-admin' // page
					);
					add_settings_field(
						$taxonomy_object, // id
						$taxonomy_object , // title
						array($this, 'cats_callback'), // callback
						'zearch-admin', // page
						'zearch_setting_cat_section_' . sanitize_title($post_type), // section
						array('taxonomy' => $taxonomy_object, 'post_type' => $post_type)
					);
				}
			}
		}
	}

	public function zearch_sanitize($input, $post_types)
	{
		$sanitary_values = array();

		foreach ($post_types as $post_type) {
			if (isset($input['title_' . $post_type])) {
				$sanitary_values['title_' . $post_type] = $input['title_' . $post_type];
			}

			if (isset($input['content_' . $post_type])) {
				$sanitary_values['content_' . $post_type] = $input['content_' . $post_type];
			}

			if (isset($input['author_' . $post_type])) {
				$sanitary_values['author_' . $post_type] = $input['author_' . $post_type];
			}
		}


		return $sanitary_values;
	}

	public function zearch_section_info()
	{
	}

	public function title_callback($post_type)
	{
		printf(
			'<input type="checkbox" name="ezearch[weighting][' . $post_type . '][post_title][enabled]" id="title_' . $post_type . '" value="on" %s> <label for="title_' . $post_type . '">Searchable</label>',
			(isset($this->zearch_options['weighting'][$post_type]['post_title']['enabled']) and $this->zearch_options['weighting'][$post_type]['post_title']['enabled'] === 'on') ? 'checked' : ''
		);

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="ezearch[weighting][' . $post_type . '][post_title][weight]"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset($this->zearch_options['weighting'][$post_type]['post_title']['weight']) ? esc_attr($this->zearch_options['weighting'][$post_type]['post_title']['weight']) : '1');
	}

	public function content_callback($post_type)
	{
		printf(
			'<input type="checkbox" name="ezearch[weighting][' . $post_type . '][content][enabled]" id="content_' . $post_type . '" value="content_' . $post_type . '" %s> <label for="content_' . $post_type . '">Searchable</label> |',
			(isset($this->zearch_options['weighting'][$post_type]['content']['enabled']) && $this->zearch_options['weighting'][$post_type]['content']['enabled'] === 'on') ? 'checked' : ''
		);

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="ezearch[weighting][' . $post_type . '][content][weight]"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset($this->zearch_options['weighting'][$post_type]['content']['weight']) ? esc_attr($this->zearch_options['weighting'][$post_type]['content']['weight']) : '0');
	}

	public function post_excerpt_callback($post_type)
	{
		printf(
			'<input type="checkbox" name="ezearch[weighting]['. $post_type . '][post_excerpt][enabled]" id="post_excerpt_' . $post_type . '" value="on" %s> <label for="post_excerpt_' . $post_type . '">Searchable</label> |',
			(isset($this->zearch_options['weighting'][$post_type]['post_excerpt']['enabled']) && $this->zearch_options['weighting'][$post_type]['post_excerpt']['enabled'] === 'on') ? 'checked' : ''
		);

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="ezearch[weighting][' . $post_type . '][post_excerpt][weight]"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset($this->zearch_options['weighting'][$post_type]['post_excerpt']['weight']) ? esc_attr($this->zearch_options['weighting'][$post_type]['post_excerpt']['weight']) : '0');
	}

	public function author_callback($post_type)
	{
		printf(
			'<input type="checkbox" name="ezearch[weighting][' . $post_type . '][author_name][enabled]" id="author_' . $post_type . '" value="on" %s> <label for="author_' . $post_type . '">Searchable</label> |',
			(isset($this->zearch_options['weighting'][$post_type]['author_name']['enabled']) && $this->zearch_options['weighting'][$post_type]['author_name']['enabled'] === 'on') ? 'checked' : ''
		);

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="ezearch[weighting][' . $post_type . '][author_name][weight]"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset($this->zearch_options['weighting'][$post_type]['author_name']['weight']) ? esc_attr($this->zearch_options['weighting'][$post_type]['author_name']['weight']) : '0');
	}

	public function cats_callback($args)
	{
		printf(
			'<input type="checkbox" name="ezearch[weighting]['.$args['post_type'].'][terms.' . $args['taxonomy'] . '.name][enabled]" id="tax_' . $args['taxonomy'] . '" value="on" %s> <label for="tax_' . $args['taxonomy'] . '">Searchable</label> |',
			(isset($this->zearch_options['weighting'][$args['post_type']]['terms.'.$args['taxonomy'].'.name']['enabled']) && $this->zearch_options['weighting'][$args['post_type']]['terms.'.$args['taxonomy'].'.name']['enabled'] === 'on') ? 'checked' : ''
		);

		printf('<label for="range_weight" style="margin-left:10px;">Weight: </label> <input type="range" name="ezearch[weighting]['.$args['post_type'].'][terms.' . $args['taxonomy'] . '.name][weight]"  class="slider" min="0" max="100" value="%s">
		<p  class="slider_label"></p>', isset($this->zearch_options['weighting'][$args['post_type']]['terms.' . $args['taxonomy'] .'.name']['weight']) ? esc_attr($this->zearch_options['weighting'][$args['post_type']]['terms.' . $args['taxonomy'] .'.name']['weight']) : '0');
	}
}
if (is_admin())
	$zearch = new ZearchOptions();

//Api settings 
include('api-settings.php');
?>