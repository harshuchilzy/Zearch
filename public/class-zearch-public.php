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
class Zearch_Public {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		add_action( 'wp_head', [ $this, 'results_template'] );

		add_action( 'wp_ajax_query_elasticsearch', [ $this, 'query_elasticsearch'] );
		add_action( 'wp_ajax_nopriv_query_elasticsearch', [ $this, 'query_elasticsearch'] );



	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zearch-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'tailwind', plugin_dir_url('Zearch').'Zearch/style.css');


	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zearch-public.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'dayz-search-result',  plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ), '1.0.0', true);
		wp_localize_script( 'dayz-search-result', 'DayzAjax', array( 'dayz_ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script('dayz-search-result');
	}

	public function query_elasticsearch() {
		$query = $_POST['search_values'];
		echo $query;
		die();
	}

	public function results_template() {
		echo include 'template/template-one.php' ; 
	}

}
