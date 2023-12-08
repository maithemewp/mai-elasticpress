<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Init Elasticsearch PHP Client
 */
use Elasticsearch\ClientBuilder;
use ElasticPress\Indexables as Indexables;
use ElasticPress\Utils;

/**
 * Setup the class.
 *
 * Original code from:
 * @link https://github.com/grossherr/elasticpress-autosuggest-endpoint/
 * @link https://gist.github.com/tamara-m/6b8bdb61aa9cf9b2a59a63ffa9e0d4f7
 */
class Mai_Elasticpress_Autosuggest_Endpoint {

	/**
	 * Construct the class.
	 */
	function __construct() {
		$this->hooks();
	}

	/**
	 * Add hooks.
	 *
	 * @since 0.8.0
	 *
	 * @return void
	 */
	function hooks() {
		add_action( 'rest_api_init', [ $this, 'register_endpoint' ] );
	}

	/**
	 * Register Elasticpress Autosuggest Endpoint.
	 * You have to specify via Elasticpress settings or `EP_AUTOSUGGEST_ENDPOINT` constant like this:
	 * @link https://yourdomain.com/wp-json/mai-elasticpress/v1/autosuggest/
	 *
	 * @since 0.8.0
	 *
	 * @return void
	 */
	function register_endpoint() {
		register_rest_route( 'mai-elasticpress/v1', '/autosuggest/', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'callback' ],
			'permission_callback' => '__return_true',
		]);
	}

	/**
	 * Elasticpress Autosuggest Endpoint Callback.
	 *
	 * Gets host and index name dynamically. Otherwise,
	 * if not specified, host would default to localhost:9200
	 * and index name would default to 'index'
	 *
	 * @since 0.8.0
	 *
	 * @param WP_REST_Request $data
	 *
	 * @return array|callable
	 */
	function callback( WP_REST_Request $data ) {
		$client = ClientBuilder::create();
		$client->setHosts( [ ElasticPress\Utils\get_host() ] );
		$client = $client->build();
		$params = $data->get_json_params();

		$response = $client->search( [
			'index' => Indexables::factory()->get( 'post' )->get_index_name(),
			'body'  => $params,
		] );

		return $response;
	}
}
