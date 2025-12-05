<?php

namespace Yoast\WP\SEO\Surfaces;

use Yoast\WP\SEO\Exceptions\Forbidden_Property_Mutation_Exception;
use Yoast\WP\SEO\Helpers;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Helpers_Surface.
 *
 * Surface for the indexables.
 *
 * @property Helpers\Asset_Helper                           $asset
 * @property Helpers\Author_Archive_Helper                  $author_archive
 * @property Helpers\Blocks_Helper                          $blocks
 * @property Helpers\Capability_Helper                      $capability
 * @property Helpers\Current_Page_Helper                    $current_page
 * @property Helpers\Date_Helper                            $date
 * @property Helpers\Environment_Helper                     $environment
 * @property Helpers\First_Time_Configuration_Notice_Helper $first_time_configuration_notice
 * @property Helpers\Home_Url_Helper                        $home_url
 * @property Helpers\Image_Helper                           $image
 * @property Helpers\Indexable_Helper                       $indexable
 * @property Helpers\Indexing_Helper                        $indexing
 * @property Helpers\Input_Helper                           $input
 * @property Helpers\Language_Helper                        $language
 * @property Helpers\Meta_Helper                            $meta
 * @property Helpers\Notification_Helper                    $notification
 * @property Helpers\Options_Helper                         $options
 * @property Helpers\Pagination_Helper                      $pagination
 * @property Helpers\Permalink_Helper                       $permalink
 * @property Helpers\Post_Helper                            $post
 * @property Helpers\Post_Type_Helper                       $post_type
 * @property Helpers\Primary_Term_Helper                    $primary_term
 * @property Helpers\Product_Helper                         $product
 * @property Helpers\Redirect_Helper                        $redirect
 * @property Helpers\Require_File_Helper                    $require_file
 * @property Helpers\Robots_Helper                          $robots
 * @property Helpers\Short_Link_Helper                      $short_link
 * @property Helpers\Site_Helper                            $site
 * @property Helpers\String_Helper                          $string
 * @property Helpers\Social_Profiles_Helper                 $social_profiles
 * @property Helpers\Taxonomy_Helper                        $taxonomy
 * @property Helpers\Url_Helper                             $url
 * @property Helpers\User_Helper                            $user
 * @property Helpers\Woocommerce_Helper                     $woocommerce
 * @property Helpers\Wordpress_Helper                       $wordpress
 */
class Helpers_Surface {

	/**
	 * The DI container.
	 *
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * The open_graph helper namespace
	 *
	 * @var Open_Graph_Helpers_Surface
	 */
	public $open_graph;

	/**
	 * The schema helper namespace
	 *
	 * @var Schema_Helpers_Surface
	 */
	public $schema;

	/**
	 * The twitter helper namespace
	 *
	 * @var Twitter_Helpers_Surface
	 */
	public $twitter;

	/**
	 * Loader constructor.
	 *
	 * @param ContainerInterface         $container  The dependency injection container.
	 * @param Open_Graph_Helpers_Surface $open_graph The OpenGraph helpers surface.
	 * @param Schema_Helpers_Surface     $schema     The Schema helpers surface.
	 * @param Twitter_Helpers_Surface    $twitter    The Twitter helpers surface.
	 */
	public function __construct(
		ContainerInterface $container,
		Open_Graph_Helpers_Surface $open_graph,
		Schema_Helpers_Surface $schema,
		Twitter_Helpers_Surface $twitter
	) {
		$this->container  = $container;
		$this->open_graph = $open_graph;
		$this->schema     = $schema;
		$this->twitter    = $twitter;
	}

	/**
	 * Magic getter for getting helper classes.
	 *
	 * @param string $helper The helper to get.
	 *
	 * @return mixed The helper class.
	 */
	public function __get( $helper ) {
		return $this->container->get( $this->get_helper_class( $helper ) );
	}

	/**
	 * Magic isset for ensuring helper exists.
	 *
	 * @param string $helper The helper to get.
	 *
	 * @return bool Whether the helper exists.
	 */
	public function __isset( $helper ) {
		return $this->container->has( $this->get_helper_class( $helper ) );
	}

	/**
	 * Prevents setting dynamic properties and unsetting declared properties
	 * from an inaccessible context.
	 *
	 * @param string $name  The property name.
	 * @param mixed  $value The property value.
	 *
	 * @return void
	 *
	 * @throws Forbidden_Property_Mutation_Exception Set is never meant to be called.
	 */
	public function __set( $name, $value ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- __set must have a name and value - PHPCS #3715.
		throw Forbidden_Property_Mutation_Exception::cannot_set_because_property_is_immutable( $name );
	}

	/**
	 * Prevents unsetting dynamic properties and unsetting declared properties
	 * from an inaccessible context.
	 *
	 * @param string $name The property name.
	 *
	 * @return void
	 *
	 * @throws Forbidden_Property_Mutation_Exception Unset is never meant to be called.
	 */
	public function __unset( $name ) {
		throw Forbidden_Property_Mutation_Exception::cannot_unset_because_property_is_immutable( $name );
	}

	/**
	 * Get the class name from a helper slug
	 *
	 * @param string $helper The name of the helper.
	 *
	 * @return string
	 */
	protected function get_helper_class( $helper ) {
		$helper = \implode( '_', \array_map( 'ucfirst', \explode( '_', $helper ) ) );

		return "Yoast\WP\SEO\Helpers\\{$helper}_Helper";
	}
}
