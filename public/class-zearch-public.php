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

		add_action('wp_footer', [$this, 'results_template']);

		add_action('wp_ajax_query_ezearch', [$this, 'query_ezearch']);
		add_action('wp_ajax_nopriv_query_ezearch', [$this, 'query_ezearch']);

		add_action('wp_ajax_query_ezearch_by', [$this, 'query_ezearch_by']);
		add_action('wp_ajax_nopriv_query_ezearch_by', [$this, 'query_ezearch_by']);
		// add_shortcode('dayz_product_cats', [$this, 'dayz_product_cats']);
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
		wp_enqueue_style('ezearch-basic', plugin_dir_url('e-Zearch') . 'style.css');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		// wp_enqueue_script( 'underscore' );
		// wp_enqueue_script( 'wp-util' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-accordion' );

		wp_enqueue_script('zearch-public', plugin_dir_url(__FILE__) . 'js/zearch-public.js', array('jquery', 'wp-util'), $this->version, false);
		wp_localize_script('zearch-public', 'DayzAjax', array('dayz_ajaxurl' => admin_url('admin-ajax.php')));
		wp_enqueue_script('zearch-public');
		// wp_enqueue_script('zearch-infinite-scroll', plugin_dir_url(__FILE__) . 'js/scrollpagination.js', array(), $this->version, false);

		// Test ES NPM
		// wp_enqueue_script('ezearch-es', plugin_dir_url(dirname( __FILE__ )) . 'build/ezearch-es.js', array(), $this->version, false);

	}

	public function query_ezearch()
	{
		$query = $_POST['search_values'];
		$settings = get_option('ezearch');
		$from = $_POST['from'];
		$size = $_POST['size'];
		$searchables = '';
		foreach ($settings['weighting'] as $post_type => $data) {
			foreach ($data as $field => $settings) {
				if ($settings['enabled'] == 'on') {
					$searchables .= '"' . $field . '^' . $settings['weight'] . '",';
				}
			}
		}

		$client = ClientBuilder::create()
			->setHosts(['http://localhost:9200'])
			// ->setApiKey()
			->build();
		$params = [
			'index' => 'deurbeslaggigantnl-post-1', // get index dynamically
			'type' => 'product',
			'body' => '{
				"from":'.$from.',"size":'.$size.',"sort":[{"_score":{"order":"desc"}}],
				"query": {
					"query_string": {
						"query":"' . $query . '",
						"type":"phrase",
						"fields":[
						   ' . $searchables . '
							"terms.ep_custom_result.name^9999"
						],
						"boost":3
					}
				}
			}'
		];
		$response = $client->search($params);
		$response = json_decode($response);
		$hits = $response->hits->hits;
		echo json_encode($hits);
		wp_die();
	}

	public function query_ezearch_by()
	{
		$query = $_POST['search_values'];
		$taxo = $_POST['taxoTerms'];
		$settings = get_option('ezearch');

		$searchables = '';
		foreach ($settings['weighting'] as $post_type => $data) {
			foreach ($data as $field => $settings) {
				if ($settings['enabled'] == 'on') {
					$searchables .= '"' . $field . '^' . $settings['weight'] . '",';
				}
			}
		}

		$taxoQuery = '';
		if(isset($taxo) and is_array($taxo)){
			$count = 1;
			foreach($taxo as $searchTerm){
				if($count < count($taxo)){
					$taxoQuery += ',';
				}
				$taxoQuery += '{
					"multi_match":{
					   "query":"'.$searchTerm['value'].'",
					   "fields":[
						  "'.$searchTerm['name'].'^100"
					   ],
					   "operator":"and",
					   "boost":1,
					   "fuzziness":"auto"
					}
				 }';
				 $count++;
			}
		}

		$client = ClientBuilder::create()
			->setHosts(['http://localhost:9200'])
			// ->setApiKey()
			->build();
		$params = [
			'index' => 'deurbeslaggigantnl-post-1', // get index dynamically
			'type' => 'product',
			'body' => '{
				"from":0,
				"size":8,
				"sort":[
				   {
					  "_score":{
						 "order":"desc"
					  }
				   }
				],
				"query":{
				   "bool":{
					  "must":[
						 { "bool":{
							   "must":[
								  {
									 "multi_match":{
										"query":"'.$query.'",
										"type":"phrase",
										"fields":[
										   '.$searchables.',
										   "terms.ep_custom_result.name^9999"
										],
										"boost":3
									 }
								  }
								  '.$taxoQuery.'
							   ]
							}
						 }
					  ],
					  "filter":[
						 {
							"match":{
							   "post_type.raw":"product"
							}
						 }
					  ]
				   }
				}
			 }'
		];

		// {
		// 	"from":0,"size":8,"sort":[{"_score":{"order":"desc"}}],
		// 	"query": {
		// 		"query_string": {
		// 			"query":"' . $query . '",
		// 			"type":"phrase",
		// 			"fields":[
		// 				"meta._price.value^100",
		// 				"terms.ep_custom_result.name^9999"
		// 			],
		// 			"boost":3
		// 		}
		// 	}
		// }

		$response = $client->search($params);
		$response = json_decode($response);
		$hits = $response->hits->hits;
		echo json_encode($hits);
		wp_die();
	}

	public function results_template()
	{
		// echo 'Test';
		// get_template_part('template-one', 'template/template-one');
		// echo 'Test2';

		include_once 'template/template-one.php';
	}

}

function ezearch_get_price_range()
{
	if(!class_exists( 'woocommerce' )){return;}
	global $wpdb;

	$args = WC()->query->get_main_query();

	$tax_query  = isset($args->tax_query->queries) ? $args->tax_query->queries : array();
	$meta_query = isset($args->query_vars['meta_query']) ? $args->query_vars['meta_query'] : array();

	foreach ($meta_query + $tax_query as $key => $query) {
		if (!empty($query['price_filter']) || !empty($query['rating_filter'])) {
			unset($meta_query[$key]);
		}
	}

	$meta_query = new \WP_Meta_Query($meta_query);
	$tax_query  = new \WP_Tax_Query($tax_query);

	$meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
	$tax_query_sql  = $tax_query->get_sql($wpdb->posts, 'ID');

	$sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
	$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
	$sql .= " 	WHERE {$wpdb->posts}.post_type IN ('product')
			AND {$wpdb->posts}.post_status = 'publish'
			AND price_meta.meta_key IN ('_price')
			AND price_meta.meta_value > '' ";
	$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

	$search = \WC_Query::get_main_search_query_sql();
	if ($search) {
		$sql .= ' AND ' . $search;
	}

	$prices = $wpdb->get_row($sql); // WPCS: unprepared SQL ok.

	return [
		'min' => floor($prices->min_price),
		'max' => ceil($prices->max_price)
	];
}
