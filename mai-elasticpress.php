<?php

/**
 * Plugin Name:     Mai Elasticpress
 * Plugin URI:      https://bizbudding.com/
 * Description:     Elasticpress helper plugin for BizBudding/Mai Theme.
 * Version:         0.4.0
 *
 * Author:          BizBudding
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Mai_Elasticpress Class.
 *
 * @since 0.1.0
 */
final class Mai_Elasticpress {

	/**
	 * @var   Mai_Elasticpress The one true Mai_Elasticpress
	 * @since 0.1.0
	 */
	private static $instance;

	/**
	 * Main Mai_Elasticpress Instance.
	 *
	 * Insures that only one instance of Mai_Elasticpress exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   0.1.0
	 * @static  var array $instance
	 * @uses    Mai_Elasticpress::setup_constants() Setup the constants needed.
	 * @uses    Mai_Elasticpress::includes() Include the required files.
	 * @uses    Mai_Elasticpress::hooks() Activate, deactivate, etc.
	 * @see     Mai_Elasticpress()
	 * @return  object | Mai_Elasticpress The one true Mai_Elasticpress
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup.
			self::$instance = new Mai_Elasticpress;
			// Methods.
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-elasticpress' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-elasticpress' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'MAI_ELASTICPRESS_VERSION' ) ) {
			define( 'MAI_ELASTICPRESS_VERSION', '0.4.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'MAI_ELASTICPRESS_PLUGIN_DIR' ) ) {
			define( 'MAI_ELASTICPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Includes Path.
		// if ( ! defined( 'MAI_ELASTICPRESS_INCLUDES_DIR' ) ) {
		// 	define( 'MAI_ELASTICPRESS_INCLUDES_DIR', MAI_ELASTICPRESS_PLUGIN_DIR . 'includes/' );
		// }

		// Plugin Folder URL.
		if ( ! defined( 'MAI_ELASTICPRESS_PLUGIN_URL' ) ) {
			define( 'MAI_ELASTICPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'MAI_ELASTICPRESS_PLUGIN_FILE' ) ) {
			define( 'MAI_ELASTICPRESS_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name
		if ( ! defined( 'MAI_ELASTICPRESS_BASENAME' ) ) {
			define( 'MAI_ELASTICPRESS_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function includes() {
		// Include vendor libraries.
		require_once __DIR__ . '/vendor/autoload.php';
		// Includes.
		// foreach ( glob( MAI_ELASTICPRESS_INCLUDES_DIR . '*.php' ) as $file ) { include $file; }
	}

	/**
	 * Run the hooks.
	 *
	 * @since   0.1.0
	 * @return  void
	 */
	public function hooks() {
		add_action( 'plugins_loaded', [ $this, 'updater' ] );
		add_action( 'plugins_loaded', [ $this, 'run' ] );
	}

	/**
	 * Setup the updater.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 *
	 * @since 0.1.0
	 *
	 * @uses https://github.com/YahnisElsts/plugin-update-checker/
	 *
	 * @return void
	 */
	public function updater() {
		// Bail if current user cannot manage plugins.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		// Bail if plugin updater is not loaded.
		if ( ! class_exists( 'Puc_v4_Factory' ) ) {
			return;
		}

		// Setup the updater.
		$updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/maithemewp/mai-elasticpress/', __FILE__, 'mai-elasticpress' );

		// Set the branch that contains the stable release.
		$updater->setBranch( 'main' );

		// Maybe set github api token.
		if ( defined( 'MAI_GITHUB_API_TOKEN' ) ) {
			$updater->setAuthentication( MAI_GITHUB_API_TOKEN );
		}

		// Add icons for Dashboard > Updates screen.
		if ( function_exists( 'mai_get_updater_icons' ) && $icons = mai_get_updater_icons() ) {
			$updater->addResultFilter(
				function ( $info ) use ( $icons ) {
					$info->icons = $icons;
					return $info;
				}
			);
		}
	}

	/**
	 * Runs plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return
	 */
	function run() {
		add_filter( 'mai_styles_config',                          [ $this, 'load_css' ] );
		add_filter( 'ep_highlighting_class',                      [ $this, 'highlighting_class' ] );
		add_filter( 'comments_template_top_level_query_args',     [ $this, 'add_query_arg' ] );
		add_filter( 'comments_template_query_args',               [ $this, 'add_query_arg' ] );

		// Mai Theme v2.
		if ( class_exists( 'Mai_Engine ') ) {
			add_filter( 'ep_post_thumbnail_image_size',               [ $this, 'change_image_size' ] );
			add_filter( 'mai_post_grid_query_args',                   [ $this, 'edit_query' ], 10, 2 );
			add_filter( 'acf/load_field/key=mai_grid_block_query_by', [ $this, 'add_related_choice' ] );
		}

		// Genesis.
		add_action( 'after_setup_theme', [ $this, 'register_sidebar' ] );
		add_action( 'genesis_before',    [ $this, 'display_sidebar' ], 12 );
	}

	/**
	 * Move Before Header template part inside site-header.
	 * This allows it to be sticky with the header.
	 *
	 * In order to not break the header JS you need to rename the class
	 * anything but before-header.
	 *
	 * @param array $config The template parts config.
	 *
	 * @return array
	 */
	function load_css( $styles ) {
		$styles['elasticpress'] = [
			'location'  => 'public',
			'src'       => MAI_ELASTICPRESS_PLUGIN_URL . 'css/maiep-instant-results.css',
			'ver'       => MAI_ELASTICPRESS_VERSION,
			'in_footer' => true,
			'condition' => function() {
				return $this->has_feature( 'instant-results' );
			},
		];

		return $styles;
	}

	/**
	 * Removes ep highlight class so it inherits Mai styles.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function highlighting_class( $class ) {
		return '';
	}

	/**
	 * Adds `ep_integrate` arg to the query.
	 *
	 * @since 0.2.0
	 *
	 * @return array
	 */
	function add_query_arg( $args ) {
		if ( is_admin() ) {
			return $args;
		}

		$args['ep_integrate'] = true;

		return $args;
	}

	/**
	 * Changes image size to use Mai Theme size.
	 *
	 * @since 0.2.2
	 *
	 * @param string $size The existing image size. Defaults to 'thumbnail'.
	 *
	 * @return string
	 */
	function change_image_size( $size ) {
		return 'landscape-md';
	}

	/**
	 * Modify Mai Post Grid query args.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	function edit_query( $query_args, $args ) {
		if ( ! $this->has_feature( 'related_posts' ) ) {
			return $query_args;
		}

		if ( ! isset( $args['type'] ) || 'post' !== $args['type'] ) {
			return $query_args;
		}

		if ( isset( $args['query_by'] ) && $args['query_by'] && 'ep_related' === $args['query_by'] ) {
			$query_args['ep_integrate'] = true;
			$query_args['more_like']    = get_the_ID();
		}

		return $query_args;
	}

	/**
	 * Adds Related as an "Get Entries By" choice.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function add_related_choice( $field ) {
		$field['choices'][ 'ep_related' ] = __( 'Related (Elasticpress)', 'mai-elasticpress' );

		if ( ! $this->has_feature( 'related_posts' ) ) {
			$field['choices'][ 'ep_related' ] .= ' [' . __( 'Feature Disabled!', 'mai-elasticpress' ) . ']';
		}

		return $field;
	}

	/**
	 * Checks if a feature is active.
	 *
	 * @since 0.1.0
	 *
	 * @param string $feature The feature to check.
	 *
	 * @return bool
	 */
	function has_feature( $feature ) {
		return ElasticPress\Features::factory()->get_registered_feature( $feature )->is_active();
	}

	/**
	 * Registers widget area.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function register_sidebar() {
		if ( ! function_exists( 'genesis_register_sidebar' ) ) {
			return;
		}

		genesis_register_sidebar(
			[
				'id'          => 'maiep-search-results',
				'name'        => __( 'Search Results (Mai Elasticpress)', 'mai-elasticpress' ),
				'description' => __( 'This is the widget that appears on search results.', 'mai-elasticpress' ),
			]
		);
	}

	/**
	 * Swaps search results sidebar for custom sidebar.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function display_sidebar() {
		if ( ! is_search() ) {
			return;
		}

		ob_start();
		genesis_widget_area( 'maiep-search-results',
			[
				'before' => '<aside class="widget-area maiep-widget-area">',
				'after'  => '</aside>',
			]
		);
		$widget_area = ob_get_clean();
		$hook        = 'genesis_before_loop';

		if ( function_exists( 'mai_has_page_header' ) && mai_has_page_header() ) {
			$hook = 'mai_page_header';
		}

		// Display mobile filters.
		if ( $widget_area && function_exists( 'mai_get_accordion' ) && function_exists( 'mai_get_accordion_item' ) ) {
			add_action( $hook, function() use ( $widget_area ) {
				printf( '<style>%s</style>', file_get_contents( MAI_ELASTICPRESS_PLUGIN_DIR . '/css/maiep-search-results.css' ) );
				echo mai_get_accordion(
					[
						'class'   => 'maiep-accordion',
						'content' => mai_get_accordion_item(
							[
								'title'   => wp_kses_post( apply_filters( 'maiep_filter_text', __( 'Filter Results', 'mai-elasticpress' ) ) ),
								'content' => $widget_area,
							]
						),
					]
				);
			}, 12 );
		}

		// Remove default sidebar.
		remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );

		// Add our new sidebar, with inline styles.
		add_action( 'genesis_sidebar', function() use ( $widget_area ) {
			echo $widget_area;
		});
	}
}

/**
 * The main function for that returns Mai_Elasticpress
 *
 * The main function responsible for returning the one true Mai_Elasticpress
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_Elasticpress(); ?>
 *
 * @since 0.1.0
 *
 * @return object|Mai_Elasticpress The one true Mai_Elasticpress Instance.
 */
add_action( 'plugins_loaded', 'mai_elasticpress_plugin' );
function mai_elasticpress_plugin() {
	if ( ! class_exists( 'ElasticPress\Feature' ) ) {
		return;
	}

	return Mai_Elasticpress::instance();
}

// Get Mai_Elasticpress Running.
mai_elasticpress_plugin();
