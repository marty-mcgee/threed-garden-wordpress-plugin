<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://threed.design
 * @since      0.0.1
 *
 * @package    ThreeD_Garden
 * @subpackage ThreeD_Garden/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.0.1
 * @package    ThreeD_Garden
 * @subpackage ThreeD_Garden/includes
 * @author     Marty McGee <support@companyjuice.com>
 */
class ThreeD_Garden {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      ThreeD_Garden_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {
		if ( defined( 'THREED_GARDEN_VERSION' ) ) {
			$this->version = THREED_GARDEN_VERSION;
		} else {
			$this->version = '0.0.17';
		}
		$this->plugin_name = 'threed-garden';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ThreeD_Garden_Loader. Orchestrates the hooks of the plugin.
	 * - ThreeD_Garden_i18n. Defines internationalization functionality.
	 * - ThreeD_Garden_Admin. Defines all hooks for the admin area.
	 * - ThreeD_Garden_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-threed-garden-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-threed-garden-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-threed-garden-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-threed-garden-public.php';

		/**
		 * [MM] CUSTOM CLASSES ??
		 * (includes: admin + public)
		 * for example: require_once plugin_dir_path( dirname( __FILE__ ) ) . 'qmanager.php';
		 */
		// ** [MM] The class responsible for defining GRAPHQL WordPress integrations (Extending WP-GraphQL) ...
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-threed-garden-graphql.php';

		$this->loader = new ThreeD_Garden_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ThreeD_Garden_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ThreeD_Garden_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new ThreeD_Garden_Admin( $this->get_plugin_name(), $this->get_version() );

		// css + js
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// admin menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'construct_plugin_menu' );
		$this->loader->add_filter( 'parent_file', $plugin_admin, 'set_current_menu' );

		// custom post types + taxonomies
		$this->loader->add_action( 'init', $plugin_admin, 'scenes_init' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'scene_updated_messages' );
		$this->loader->add_action( 'init', $plugin_admin, 'allotments_init' );
		$this->loader->add_action( 'init', $plugin_admin, 'allotment_taxonomies' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'allotment_updated_messages' );
		$this->loader->add_action( 'init', $plugin_admin, 'beds_init' );
		$this->loader->add_action( 'init', $plugin_admin, 'bed_taxonomies' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'bed_updated_messages' );
		$this->loader->add_action( 'init', $plugin_admin, 'plants_init' );
		$this->loader->add_action( 'init', $plugin_admin, 'plant_taxonomies' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'plant_updated_messages' );
		$this->loader->add_action( 'init', $plugin_admin, 'planting_plans_init' );
		$this->loader->add_action( 'init', $plugin_admin, 'planting_plan_taxonomies' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'planting_plan_updated_messages' );

		// testing rest api
		//$this->loader->add_action( 'rest_api_init', $plugin_admin, 'slug_add_post_data' );
		//$this->loader->add_action( 'rest_api_init', $plugin_admin, 'create_ACF_meta_in_REST' );
		//$this->loader->add_filter( 'rest_prepare_post', $plugin_admin, 'acf_to_rest_api' );
		
		// custom post type templates
		$this->loader->add_filter( 'single_template', $plugin_admin, 'load_scene_template' );
		$this->loader->add_filter( 'single_template', $plugin_admin, 'load_allotment_template' );
		$this->loader->add_filter( 'single_template', $plugin_admin, 'load_bed_template' );
		$this->loader->add_filter( 'single_template', $plugin_admin, 'load_plant_template' );
		$this->loader->add_filter( 'single_template', $plugin_admin, 'load_planting_plan_template' );

		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'cpt_add_meta_boxes' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'cpt_remove_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_cpt_template_meta_data' );
		//$this->loader->add_filter( 'single_template', $plugin_admin, 'custom_single_template' );

		/*
		 * [MM] 2025-01-22-12-11-000
		 * TESTING: GRAPHQL CUSTOM FIELDS for MUTATIONS
		*/
		add_filter('graphql_input_fields', function($input_fields, $type_name) {
			if ($type_name === "UpdatePreferencesInput") {

				$input_fields['version'] = [
					'type' => 'String',
					'description' => __('A string for preferences\'s version.', 'wp-graphql'),
				];
				
				$input_fields['doAutoLoadData'] = [
					'type' => 'Boolean',
					'description' => __('A true|false for preferences\'s doAutoLoadData.', 'wp-graphql'),
				];
				
				$input_fields['doAutoRotate'] = [
					'type' => 'Boolean',
					'description' => __('A true|false for preferences\'s doAutoRotate.', 'wp-graphql'),
				];
				
			}
			return $input_fields;
		}, 10, 2);
		
		/*
		 * [MM] 2025-01-22-12-11-001
		 * Example: Here is a basic example of registering a mutation:
			# This function registers a mutation to the Schema.
			# The first argument, in this case `updateThreeDPreferences`, is the name of the mutation in the Schema
			# The second argument is an array to configure the mutation.
			# The config array accepts 3 key/value pairs for: inputFields, outputFields and mutateAndGetPayload.
		*/
		register_graphql_mutation( 'updateThreeDPreferences', [

			// # inputFields expects an array of Fields to be used for inputting values to the mutation
			'inputFields'         => [
				'version' => [
					'type' => 'String',
					'description' => __( 'Description of the input field', 'threedgarden' ),
				]
			],

			// # outputFields expects an array of fields that can be asked for in response to the mutation
			// # the resolve function is optional, but can be useful if the mutateAndPayload doesn't return an array
			// # with the same key(s) as the outputFields
			'outputFields'        => [
				'exampleOutput' => [
					'type' => 'String',
					'description' => __( 'Description of the output field', 'threedgarden' ),
					'resolve' => function( $payload, $args, $context, $info ) {
								return isset( $payload['exampleOutput'] ) ? $payload['exampleOutput'] : null;
					}
				]
			],

			// # mutateAndGetPayload expects a function, and the function gets passed the $input, $context, and $info
			// # the function should return enough info for the outputFields to resolve with
			'mutateAndGetPayload' => function( $input, $context, $info ) {
				// Do any logic here to sanitize the input, check user capabilities, etc
				$exampleOutput = null;
				if ( ! empty( $input['version'] ) ) {
					$exampleOutput = 'Your input was: ' . $input['version'];
				}
				return [
					'exampleOutput' => $exampleOutput,
				];
			}
		] );
		/*
			// Registering the above mutation would allow for the following graphql mutation to be executed:

			mutation {
				updateThreeDPreferences(
					input: { clientMutationId: "example", version: "Test..." }
				) {
					clientMutationId
					exampleOutput
				}
			}

			// And the following graphql response would be provided:

			{
				"data": {
					"updateThreeDPreferences": {
						"clientMutationId": "example",
						"exampleOutput": "Your input was: Test..."
					}
				}
			}
		
		*/
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new ThreeD_Garden_Public( $this->get_plugin_name(), $this->get_version() );

		// css + js
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// custom fields testing
		//$this->loader->add_filter( 'the_content', $plugin_public, 'display_all_custom_fields' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ThreeD_Garden_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
