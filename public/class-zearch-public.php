<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://dayzsolutions.com
 * @since      1.0.0
 *
 * @package    Zearch
 * @subpackage Zearch/public
 */

use Elastic\Elasticsearch\ClientBuilder;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Zearch
 * @subpackage Zearch/public
 * @author     DayZ Solutions <info@dayzsolutions.com>
 */
class Zearch_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'wp_head', [ $this, 'results_template'] );

		add_action('wp_ajax_query_zearch', [$this, 'query_zearch']);
		add_action('wp_ajax_nopriv_query_zearch', [$this, 'query_zearch']);
		add_shortcode('dayz_product_cats', [$this, 'dayz_product_cats']);
		add_shortcode('dayz_product_brands', [$this, 'dayz_product_brands']);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zearch_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zearch_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/zearch-public.css', array(), $this->version, 'all');
		wp_enqueue_style('tailwind', plugin_dir_url('Zearch') . 'Zearch/style.css');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zearch_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zearch_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/zearch-public.js', array('jquery'), $this->version, false);
		wp_register_script('dayz-search-result',  plugin_dir_url(__FILE__) . 'js/ajax.js', array('jquery'), '1.0.0', true);
		wp_localize_script('dayz-search-result', 'DayzAjax', array('dayz_ajaxurl' => admin_url('admin-ajax.php')));
		wp_enqueue_script('dayz-search-result');
	}

	public function query_zearch()
	{
		$query = $_POST['search_values'];

		$client = ClientBuilder::create()
			->setHosts(['http://88.198.32.151:9200'])
			// ->setApiKey()
			->build();
		$params = [
			'index' => 'deurbeslaggigantnl-post-1', // get index dynamically
			'type' => 'post',
			'body' => '{
				"from":0,"size":8,"sort":[{"_score":{"order":"desc"}}],
				"query": {
					"query_string": {
						"query":"'.$query.'",
						"type":"phrase",
						"fields":[
						   "post_title^1",
						   "post_excerpt^1",
						   "post_content^1",
						   "terms.category.name^1",
						   "terms.post_tag.name^1",
						   "terms.ep_custom_result.name^9999"
						],
						"boost":3
					}
				}
			}'
		];
		$response = $client->search($params);
		echo $response;
		wp_die();
	}



	public function results_template()
	{
		echo include 'template/template-one.php';
	}
	public  function dayz_product_cats()
	{
		$orderby = 'name';
		$order = 'asc';
		$hide_empty = false;
		$cat_args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
		);

		$product_categories = get_terms('product_cat', $cat_args);

		if (!empty($product_categories)) {



			foreach ($product_categories as $key => $category) {

				echo '<div class="form-control">';
				echo '<label class="cursor-pointer label">';
				echo '<span class="label-text text-black text-left text-lg capitalize">' . $category->name . '</span>';
				echo '<input type="checkbox" value="' . $category->name . '" class="checkbox" />';
				echo '</label>';
				echo '</div>';
			}
		}
	}

	public  function dayz_product_brands()
	{
		$orderby = 'name';
		$order = 'asc';
		$hide_empty = false;
		$cat_args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
		);

		$product_categories = get_terms('product_brand', $cat_args);

		if (!empty($product_categories)) {

			foreach ($product_categories as $key => $category) {
				echo '<div class="form-control">';
				echo '<label class="cursor-pointer label">';
				echo '<span class="label-text text-black text-left text-lg capitalize">' . $category->name . '</span>';
				echo '<input type="checkbox" value="' . $category->name . '" class="checkbox" />';
				echo '</label>';
				echo '</div>';
				echo '<br>';
			}
		}
	}
}
