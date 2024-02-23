<?php
/**
 * Listing items data manager
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Listings_Data' ) ) {

	/**
	 * Define Jet_Engine_Listings_Data class
	 */
	class Jet_Engine_Listings_Data {

		/**
		 * Current listing object
		 *
		 * @var object
		 */
		private $current_object = null;

		/**
		 * Current listing document
		 *
		 * @var array
		 */
		private $current_listing = false;

		/**
		 * <ain listing document for current page
		 * @var null
		 */
		private $main_listing = null;

		/**
		 * Default main object holder
		 *
		 * @var mixed
		 */
		private $default_object = null;

		/**
		 * Default user object holder
		 *
		 * @var WP_User
		 */
		private $current_user = null;

		/**
		 * Processed post object
		 *
		 * @var WP_Post
		 */
		private $current_post = null;

		/**
		 * Processed term object
		 *
		 * @var WP_Term
		 */
		private $current_term = null;

		/**
		 * Listing settings defaults
		 * @var array
		 */
		private $defaults = false;

		/**
		 * Repeater iteration index
		 *
		 * @var integer
		 */
		public $repeater_index = 0;

		public $user_fields = array();

		/**
		 * Class constructor
		 */
		public function __construct() {
			add_action( 'the_post', array( $this, 'maybe_set_current_object' ), 10, 2 );
		}

		/**
		 * Automatically setup current object for post loops started inside the page
		 *
		 * @param  [type] $post  [description]
		 * @param  [type] $query [description]
		 * @return [type]        [description]
		 */
		public function maybe_set_current_object( $post = false, $query = false ) {

			if ( ! $post ) {
				return;
			}

			if ( ! $query ) {
				$this->set_current_object( $post );
				return;
			}

			if ( $query->queried_object && 'WP_Post' === get_class( $query->queried_object ) && $post->ID ) {
				$is_main_query = $query->is_main_query() && $post->ID === $query->queried_object_id;
			} else {
				$is_main_query = $query->is_main_query();
			}

			$is_main_query = apply_filters( 'jet-engine/listings/data/the-post/is-main-query', $is_main_query, $post, $query );

			if ( $is_main_query ) {
				$current_object = $this->get_current_object();

				if ( $current_object && 'WP_Post' === get_class( $current_object ) ) {
					$this->reset_current_object();
				}

			} else {
				$this->set_current_object( $post );
			}

		}

		/**
		 * Set current listing from outside
		 *
		 * @param void
		 */
		public function set_listing( $listing_doc = null ) {

			if ( ! $listing_doc ) {
				return;
			}

			if ( $listing_doc->get_settings( 'is_main' ) ) {
				$this->main_listing = $listing_doc;
			}

			$this->current_listing = $listing_doc;
		}

		/**
		 * Set current listing by listing ID
		 *
		 * @param void
		 */
		public function set_listing_by_id( $listing_id ) {

			if ( jet_engine()->has_elementor() ) {
				$doc = Elementor\Plugin::$instance->documents->get_doc_for_frontend( $listing_id );
			} else {
				$listing_settings = get_post_meta( $listing_id, '_elementor_page_settings', true );

				if ( empty( $listing_settings ) ) {
					$listing_settings = array();
				}

				$source          = ! empty( $listing_settings['listing_source'] ) ? $listing_settings['listing_source'] : 'posts';
				$post_type       = ! empty( $listing_settings['listing_post_type'] ) ? $listing_settings['listing_post_type'] : 'post';
				$tax             = ! empty( $listing_settings['listing_tax'] ) ? $listing_settings['listing_tax'] : 'category';
				$repeater_source = ! empty( $listing_settings['repeater_source'] ) ? $listing_settings['repeater_source'] : '';
				$repeater_field  = ! empty( $listing_settings['repeater_field'] ) ? $listing_settings['repeater_field'] : '';

				$doc = jet_engine()->listings->get_new_doc( array(
					'listing_source'    => $source,
					'listing_post_type' => $post_type,
					'listing_tax'       => $tax,
					'is_main'           => true,
					'repeater_source'   => $repeater_source,
					'repeater_field'    => $repeater_field,
				), $listing_id );
			}

			jet_engine()->listings->data->set_listing( $doc );
		}

		/**
		 * Get listing type.
		 *
		 * @param  $listing_id
		 * @return string
		 */
		public function get_listing_type( $listing_id ) {
			
			$listing_type = get_post_meta( $listing_id, '_listing_type', true );

			if ( ! $listing_type ) {
				// Check edge cases when listing type is not defined for some reason
				if ( ! jet_engine()->has_elementor() || jet_engine()->blocks_views->is_blocks_listing( $listing_id ) ) {
					$listing_type = 'blocks';
				} else {
					$listing_type = 'elementor';
				}

			}

			return apply_filters( 'jet-engine/data/listing-type', $listing_type );
		}

		/**
		 * Reset current listing object
		 *
		 * @return void
		 */
		public function reset_listing() {
			$this->current_listing = $this->main_listing;
			$this->reset_current_object();
		}

		/**
		 * Returns current listing object
		 *
		 * @return [type] [description]
		 */
		public function get_listing( $listing_id = false ) {

			if ( ! $this->current_listing ) {
				if ( ! $listing_id ) {
					$default_settings      = $this->setup_default_listing();
					$this->current_listing = jet_engine()->listings->get_new_doc( $default_settings, get_the_ID() );
				} else {
					$this->set_listing_by_id( $listing_id );
				}
			}

			return $this->current_listing;
		}

		/**
		 * Retuns current object fields array
		 * @return [type] [description]
		 */
		public function get_object_fields( $where = 'elementor', $blocks_values_key = 'values' ) {

			$groups = apply_filters( 'jet-engine/listing/data/object-fields-groups', array(
				array(
					'label'  => __( 'Post', 'jet-engine' ),
					'options' => apply_filters( 'jet-engine/listing/data/post-fields', array(
						'post_id'       => __( 'Post ID', 'jet-engine' ),
						'post_title'    => __( 'Title', 'jet-engine' ),
						'post_name'     => __( 'Post Slug', 'jet-engine' ),
						'post_type'     => __( 'Post Type', 'jet-engine' ),
						'post_date'     => __( 'Date', 'jet-engine' ),
						'post_modified' => __( 'Date Modified', 'jet-engine' ),
						'post_content'  => __( 'Content', 'jet-engine' ),
						'post_excerpt'  => __( 'Excerpt', 'jet-engine' ),
						'post_status'   => __( 'Post Status', 'jet-engine' ),
					)
				) ),
				array(
					'label'  => __( 'Term', 'jet-engine' ),
					'options' => apply_filters( 'jet-engine/listing/data/term-fields', array(
						'term_id'     => __( 'Term ID', 'jet-engine' ),
						'name'        => __( 'Term name', 'jet-engine' ),
						'slug'        => __( 'Term slug', 'jet-engine' ),
						'description' => __( 'Term description', 'jet-engine' ),
						'count'       => __( 'Posts count', 'jet-engine' ),
						'parent'      => __( 'Parent term ID', 'jet-engine' ),
					)
				) ),
				array(
					'label'  => __( 'User', 'jet-engine' ),
					'options' => $this->get_user_object_fields(),
				),
				array(
					'label'  => __( 'Comment', 'jet-engine' ),
					'options' => array(
						'comment_ID'           => __( 'ID', 'jet-engine' ),
						'comment_post_ID'      => __( 'Post ID', 'jet-engine' ),
						'comment_author'       => __( 'Author', 'jet-engine' ),
						'comment_author_email' => __( 'Author E-mail', 'jet-engine' ),
						'comment_author_url'   => __( 'Author URL', 'jet-engine' ),
						'comment_author_IP'    => __( 'Author IP', 'jet-engine' ),
						'comment_date'         => __( 'Date', 'jet-engine' ),
						'comment_date_gmt'     => __( 'Date GMT', 'jet-engine' ),
						'comment_content'      => __( 'Content', 'jet-engine' ),
						'comment_karma'        => __( 'Karma', 'jet-engine' ),
						'comment_approved'     => __( 'Approved', 'jet-engine' ),
						'comment_agent'        => __( 'Agent', 'jet-engine' ),
						'comment_type'         => __( 'Type', 'jet-engine' ),
						'comment_parent'       => __( 'Parent', 'jet-engine' ),
						'user_id'              => __( 'User ID', 'jet-engine' ),
					)
				),
			) );

			if ( 'blocks' === $where ) {

				$result = array();

				foreach ( $groups as $group ) {

					$values = array();

					foreach ( $group['options'] as $key => $value ) {
						$values[] = array(
							'value' => $key,
							'label' => $value,
						);
					}

					$values_key = ! empty( $blocks_values_key ) ? $blocks_values_key : 'values';

					$result[] = array(
						'label'     => $group['label'],
						$values_key => $values,
					);

				}

				return $result;

			} else {
				return $groups;
			}

		}

		/**
		 * Return user object fields
		 * 
		 * @return array
		 */
		public function get_user_object_fields() {
			return apply_filters( 'jet-engine/listing/data/user-fields', array(
					'ID'              => __( 'ID', 'jet-engine' ),
					'user_login'      => __( 'Login', 'jet-engine' ),
					'user_nicename'   => __( 'Nickname', 'jet-engine' ),
					'user_email'      => __( 'E-mail', 'jet-engine' ),
					'user_url'        => __( 'URL', 'jet-engine' ),
					'user_registered' => __( 'Registration Date', 'jet-engine' ),
					'display_name'    => __( 'Display Name', 'jet-engine' ),
				)
			);
		}

		/**
		 * Checkl if requested property is property of user object
		 *
		 * @param  [type]  $prop [description]
		 * @return boolean       [description]
		 */
		public function is_user_prop( $prop ) {
			return in_array( $prop, array_keys( $this->get_user_object_fields() ) );
		}

		/**
		 * Get listing default property
		 *
		 * @param  string $prop [description]
		 * @return [type]       [description]
		 */
		public function listing_defaults( $prop = 'listing_source' ) {

			if ( ! empty( $this->defaults ) ) {
				return isset( $this->defaults[ $prop ] ) ? $this->defaults[ $prop ] : false;
			}

			$listing = $this->get_listing();

			return isset( $listing[ $prop ] ) ? $listing[ $prop ] : false;

		}

		/**
		 * Returns all listing settings
		 *
		 * @return [type] [description]
		 */
		public function get_listing_settings( $listing_id = false ) {

			$listing = $this->get_listing( $listing_id );

			if ( ! $listing ) {
				return $this->defaults;
			} else {
				return $listing->get_settings();
			}

		}

		/**
		 * Setup default listing settings
		 *
		 * @return [type] [description]
		 */
		public function setup_default_listing() {

			$default = array(
				'listing_source'    => 'posts',
				'listing_post_type' => 'post',
				'listing_tax'       => 'category',
			);

			$default_object = $this->get_default_object();

			if ( ! $default_object ) {
				$this->defaults = $default;
				return $this->defaults;
			}

			$listing = apply_filters( 'jet-engine/listing/data/custom-listing', false, $this, $default_object );

			if ( ! $listing ) {

				if ( isset( $default_object->post_type ) ) {
					$this->defaults = array(
						'listing_source'    => 'posts',
						'listing_post_type' => $default_object->post_type,
						'listing_tax'       => 'category',
					);
				} else {
					$this->defaults = array(
						'listing_source'    => 'terms',
						'listing_post_type' => 'post',
						'listing_tax'       => $default_object->taxonomy,
					);
				}

			} else {
				$this->defaults = $listing;
			}

			return $this->defaults;

		}

		/**
		 * Returns listing source
		 *
		 * @return string
		 */
		public function get_listing_source() {
			$listing = $this->get_listing();
			return $listing->get_settings( 'listing_source' );
		}

		/**
		 * Returns post type for query
		 *
		 * @return string
		 */
		public function get_listing_post_type() {

			$listing = $this->get_listing();

			if ( ! $listing ) {

				$post_type = get_post_type();

				$blacklisted = array(
					'elementor_library',
					'jet-theme-core',
				);

				if ( $post_type && ! in_array( $post_type, $blacklisted ) ) {
					return $post_type;
				} else {
					return $this->listing_defaults( 'listing_post_type' );
				}

			} else {
				return $listing->get_settings( 'listing_post_type' );
			}
		}

		/**
		 * Returns taxonomy for query
		 *
		 * @return string
		 */
		public function get_listing_tax() {
			$listing = $this->get_listing();
			return $listing->get_settings( 'listing_tax' );
		}

		/**
		 * Set $current_object property
		 *
		 * @param object $object
		 */
		public function set_current_object( $object = null, $clear_hook = false ) {

			if ( $clear_hook ) {
				remove_action( 'the_post', array( $this, 'maybe_set_current_object' ), 10, 2 );
			}

			if ( ! $object || ! is_object( $object ) ) {
				return;
			}

			if ( $object === $this->current_object ) {
				return;
			}

			$class = get_class( $object );

			switch ( $class ) {
				case 'WP_Post':
					$this->current_post = $object;
					break;

				case 'WP_Term':
					$this->current_term = $object;
					break;

				case 'WP_User':
					$this->current_user = $object;
					break;

			}

			$this->current_object = $object;

			do_action( 'jet-engine/listings/data/set-current-object', $this->current_object, $this );

		}

		/**
		 * Set $current_object property
		 *
		 * @param object $object
		 */
		public function reset_current_object() {
			do_action( 'jet-engine/listings/data/reset-current-object', $this );
			$this->current_object = null;
		}

		/**
		 * Returns current user object
		 *
		 * @return [type] [description]
		 */
		public function get_current_user_object() {

			if ( ! $this->current_user ) {
				$this->current_user = wp_get_current_user();
			}

			return $this->current_user;

		}

		/**
		 * Returns queried user object
		 *
		 * @return [type] [description]
		 */
		public function get_queried_user_object() {

			$user_object = false;

			if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
				$profile_builder = jet_engine()->modules->get_module( 'profile-builder' );
				$user_object     = $profile_builder->instance->query->get_queried_user();
			}

			if ( ! $user_object ) {
				if ( is_author() ) {
					$user_object = get_queried_object();
				} else {
					$user_object = $this->get_current_user_object();
				}
			}

			$user_object = apply_filters( 'jet-engine/listings/data/queried-user', $user_object );

			return $user_object;

		}

		public function get_current_author_object() {

			$user_object = false;

			$post_id = get_the_ID();

			if ( $post_id ) {

				$user_id = false;

				global $authordata;

				if ( $authordata ) {
					$user_id = get_the_author_meta( 'ID' );
				} else {

					$post = get_post( $post_id );

					if ( $post ) {
						$user_id = get_the_author_meta( 'ID', $post->post_author );
					}

				}

				if ( $user_id ) {
					$user_object = get_user_by( 'ID', $user_id );
				}

			}

			if ( ! $user_object ) {
				if ( is_author() ) {
					$user_object = get_queried_object();
				} else {
					$user_object = $this->get_current_user_object();
				}
			}

			$user_object = apply_filters( 'jet-engine/listings/data/current-author', $user_object );

			return $user_object;

		}

		/**
		 * Returns $current_object property
		 *
		 * @return object
		 */
		public function get_current_object() {

			if ( null === $this->current_object ) {
				$this->current_object = $this->get_default_object();
			}

			return $this->current_object;

		}

		/**
		 * Returns $current_object title.
		 * @param $object
		 *
		 * @return int|string
		 */
		public function get_current_object_title( $object = null ) {

			if ( ! $object ) {
				$object = $this->get_current_object();
			}

			$result = '';

			if ( ! $object || ! is_object( $object ) ) {
				return $result;
			}

			$class = get_class( $object );

			switch ( $class ) {
				case 'WP_Post':
					$result = $object->post_title;
					break;

				case 'WP_Term':
					$result = $object->name;
					break;

				default:
					$result = apply_filters( 'jet-engine/listing/current-object-title', $result, $object );

			}

			return $result;

		}

		/**
		 * Returns $current_object ID.
		 * @param $object
		 *
		 * @return int|string
		 */
		public function get_current_object_id( $object = null ) {

			if ( ! $object ) {
				$object = $this->get_current_object();
			}

			if ( ! $object || ! is_object( $object ) ) {
				return false;
			}

			$class = get_class( $object );

			switch ( $class ) {
				case 'WP_Post':
				case 'WP_User':
					$obj_id = $object->ID;
					break;

				case 'WP_Term':
					$obj_id = $object->term_id;
					break;

				case 'WP_Comment':
					$obj_id = $object->comment_ID;
					break;

				case 'Jet_Engine_Queried_Repeater_Item':
					$obj_id = $object->get_id();
					break;

				default:
					$obj_id = apply_filters( 'jet-engine/listing/custom-post-id', get_the_ID(), $object );
			}

			return apply_filters( 'jet-engine/listing/current-object-id', $obj_id, $object );
		}

		/**
		 * Returns default object
		 *
		 * @return [type] [description]
		 */
		public function get_default_object() {

			if ( null !== $this->default_object ) {
				return $this->default_object;
			}

			$default_object = false;

			if ( ! $this->current_user ) {
				$this->current_user = wp_get_current_user();
			}

			global $post;

			if ( is_singular() ) {
				$default_object = $this->current_post = $post;
			} elseif ( is_tax() || is_category() || is_tag() || is_author() ) {
				$default_object     = $this->current_term = get_queried_object();
				$this->current_post = $post;
			} elseif ( wp_doing_ajax() ) {
				if ( isset( $_REQUEST['editor_post_id'] ) ) {
					$post_id = $_REQUEST['editor_post_id'];
				} elseif ( isset( $_REQUEST['post_id'] ) ) {
					$post_id = $_REQUEST['post_id'];
				} else {
					$post_id = false;
				}

				if ( ! $post_id ) {
					$default_object = $this->current_post = false;
				} else {
					$default_object = $this->current_post = get_post( $post_id );
				}

			} elseif ( is_archive() || is_home() || is_post_type_archive() ) {
				$default_object = $this->current_post = $post;
			} elseif ( $post ) {
				$default_object = $this->current_post = $post;
			}

			$this->default_object = apply_filters( 'jet-engine/listings/data/default-object', $default_object, $this );

			return $this->default_object;

		}

		public function get_object_by_context( $context = null ) {

			if ( ! $context || 'default_object' === $context ) {
				return null;
			}

			switch ( $context ) {

				case 'wp_user':
					return wp_get_current_user();

				case 'wp_object':
					return jet_engine()->listings->objects_stack->get_root_object();

				case 'current_user':
					return jet_engine()->listings->data->get_current_user_object();

				case 'queried_user':
					return jet_engine()->listings->data->get_queried_user_object();

				case 'current_post_author':
					return jet_engine()->listings->data->get_current_author_object();

				default:
					return apply_filters( 'jet-engine/listings/data/object-by-context/' . $context, null );
			}

		}

		/**
		 * Returns requested property from current object
		 *
		 * @param  [type] $property [description]
		 * @return [type]           [description]
		 */
		public function get_prop( $property = null, $object = null ) {

			if ( $this->is_user_prop( $property ) ) {

				if ( $object ) {
					$current_user = $object;
				} else {
					$current_user = $this->current_user;
				}

				if ( ! $current_user ) {
					return false;
				}

				$vars = get_object_vars( $current_user );
				$vars = ! empty( $vars['data'] ) ? (array) $vars['data'] : array();

				if ( 'user_nicename' === $property && $current_user && isset( $current_user->ID ) ) {
					$vars['user_nicename'] = get_user_meta( $current_user->ID, 'nickname', true );
				}

			} else {

				if ( ! $object ) {
					$object = $this->get_current_object();
				}

				if ( ! $object ) {
					return false;
				}

				$vars = get_object_vars( $object );
				$vars = apply_filters( 'jet-engine/listings/data/object-vars', $vars, $object );

				if ( 'post_id' === $property && 'WP_Post' === get_class( $object ) ) {
					$vars['post_id'] = $vars['ID'];
				}

			}

			if ( $this->prevent_prop( $property, $object ) ) {
				return null;
			}

			// Return property if it was found in the object variables
			if ( isset( $vars[ $property ] ) ) {
				return $vars[ $property ];
			}

			// This will work if we want to call method from the class instead of property
			if ( is_object( $object ) && method_exists( $object, $property ) ) {
				return call_user_func( array( $object, $property ) );
			}

			/**
			 * The last chance for 3rd parties to return values form current object by passed property
			 * for example if your property was changed before input into function
			 */
			return apply_filters( 'jet-engine/listings/data/prop-not-found', false, $property, $object );

		}

		/**
		 * Prevent property from returning value.
		 * May be useable for cases like trying to get post_content of listing item, which has dynamic field with post content inside to avoid infinity looping
		 *
		 * @param  [type] $fields [description]
		 * @return [type]         [description]
		 */
		public function prevent_prop( $property, $object ) {

			$prevent_post_types = array(
				jet_engine()->post_type->slug(),
				'jet-theme-core',
				'elementor_library',
				'revision',
			);

			$prevent = ( 'post_content' === $property && in_array( $object->post_type, $prevent_post_types ) );
			return apply_filters( 'jet-engine/listings/data/prevent-prop', $prevent, $property, $object );
		}

		/**
		 * Remove tabs and accordions from allowed fields list
		 *
		 * @param  [type] $fields [description]
		 * @return [type]         [description]
		 */
		public function sanitize_meta_fields( $fields ) {
			return array_filter( $fields, function( $field ) {
				if ( ! empty( $field['object_type'] ) && 'field' !== $field['object_type'] ) {
					return false;
				} else {
					return true;
				}
			} );
		}

		/**
		 * Returns option value by combined key
		 *
		 * @param  [type] $key [description]
		 * @return [type]      [description]
		 */
		public function get_option( $key = null ) {

			if ( ! jet_engine()->options_pages || ! $key ) {
				return null;
			}

			$data = explode( '::', $key );

			if ( 2 !== count( $data ) ) {
				return null;
			}

			$page_slug = $data[0];
			$option    = $data[1];

			if ( ! $page_slug || ! $option ) {
				return null;
			}

			$page = isset( jet_engine()->options_pages->registered_pages[ $page_slug ] ) ? jet_engine()->options_pages->registered_pages[ $page_slug ] : false;

			if ( ! $page ) {
				return;
			}

			return $page->get( $option );

		}

		/**
		 * Returns object publication date dependes on object type
		 * 
		 * @param  [type] $object [description]
		 * @return [type]         [description]
		 */
		public function get_object_date( $object = null ) {

			if ( ! $object ) {
				$object = $this->get_current_object();
			}

			if ( ! $object ) {
				return false;
			}

			$class = get_class( $object );

			switch ( $class ) {
				case 'WP_Post':
					return $object->post_date;

				case 'WP_Term':
					return false;

				case 'WP_User':
					return $object->user_registered;

				case 'WP_Comment':
					return $object->comment_date;

				default:
					return apply_filters( 'jet-engine/listings/data/object-date', null, $object );

			}

		}

		/**
		 * Returns current meta
		 *
		 * @param  [type] $key [description]
		 * @return [type]      [description]
		 */
		public function get_meta( $key = null, $object = null, $source = null ) {

			if ( in_array( $key, $this->user_fields ) ) {

				if ( $object && 'WP_User' === get_class( $object ) ) {
					$user = $object;
				} else {
					$user = $this->get_queried_user_object();
				}

				if ( ! $user ) {
					return false;
				} else {
					return apply_filters(
						'jet-engine/listing/data/get-user-meta',
						get_user_meta( $user->ID, $key, true ),
						$key,
						$user->ID
					);
				}

			}

			if ( ! $object ) {
				$object = $this->get_current_object();
			}

			if ( ! $object ) {
				return false;
			}

			$class  = get_class( $object );
			$result = '';

			if ( ! $source && $this->current_listing ) {
				$source = $this->current_listing->get_settings( 'listing_source' );
			}

			if ( 'repeater' === $source ) {
				return $this->get_repeater_value( $key );
			}

			$object_id = $this->get_current_object_id( $object );

			switch ( $class ) {
				case 'WP_Post':

					if ( jet_engine()->relations->legacy->is_relation_key( $key ) ) {
						$single = false;
					} else {
						$single = true;
					}

					return apply_filters(
						'jet-engine/listing/data/get-post-meta',
						get_post_meta( $object_id, $key, $single ),
						$key,
						$object_id
					);

				case 'WP_Term':
					return apply_filters(
						'jet-engine/listing/data/get-term-meta',
						get_term_meta( $object_id, $key, true ),
						$key,
						$object_id
					);

				case 'WP_User':
					return apply_filters(
						'jet-engine/listing/data/get-user-meta',
						get_user_meta( $object_id, $key, true ),
						$key,
						$object_id
					);

				case 'WP_Comment':
					return apply_filters(
						'jet-engine/listing/data/get-comment-meta',
						get_comment_meta( $object_id, $key, true ),
						$key,
						$object_id
					);

				default:

					$result = null;

					if ( isset( $object->$key ) ) {
						$result = $object->$key;
					}

					if ( $source ) {
						return apply_filters( 
							'jet-engine/listings/data/get-meta/' . $source, 
							$result, 
							$key, 
							$object
						);
					}

			}

		}

		/**
		 * Checks if context was changed, send this trigger to get_meta function to ensure correct data will be get
		 * @param  string $key            [description]
		 * @param  string $object_context [description]
		 * @return [type]                 [description]
		 */
		public function get_meta_by_context( $key = '', $object_context = 'default_object' ) {
			$source = ( $object_context && 'default_object' !== $object_context ) ? $object_context : null;
			return $this->get_meta( $key, $this->get_object_by_context( $object_context ), $source );
		}

		/**
		 * Increase repeater index
		 *
		 * @return [type] [description]
		 */
		public function increase_index() {
			$this->repeater_index++;
		}

		/**
		 * Reset repeater index
		 *
		 * @return [type] [description]
		 */
		public function reset_index() {
			$this->repeater_index = 0;
		}

		/**
		 * Get repeater index
		 *
		 * @return int
		 */
		public function get_index() {
			return $this->repeater_index;
		}

		/**
		 * Set repeater index
		 *
		 * @param int $index
		 * @return void
		 */
		public function set_index( $index ) {
			$this->repeater_index = $index;
		}

		/**
		 * Returns repeater value
		 *
		 * @return [type] [description]
		 */
		public function get_repeater_value( $field ) {

			$source_field    = $this->current_listing->get_settings( 'repeater_field' );
			$repeater_source = $this->current_listing->get_settings( 'repeater_source' );
			$index           = $this->repeater_index;
			$object          = $this->get_current_object();

			switch ( $repeater_source ) {
				case 'jet_engine':
					
					$meta_value = $this->get_meta( $source_field, $this->get_current_object(), 'object' );

					if ( empty( $meta_value ) || ! is_array( $meta_value ) ) {
						return false;
					}

					$meta_value = array_values( $meta_value );

					if ( empty( $meta_value[ $index ] ) ) {
						return false;
					} else {
						return isset( $meta_value[ $index ][ $field ] ) ? $meta_value[ $index ][ $field ] : false;
					}

				case 'jet_engine_options':

					$source_option = $this->current_listing->get_settings( 'repeater_option' );

					if ( ! $source_option ) {
						return false;
					}

					$meta_value = $this->get_option( $source_option );

					if ( empty( $meta_value ) || ! is_array( $meta_value ) ) {
						return false;
					}

					$meta_value = array_values( $meta_value );

					if ( empty( $meta_value[ $index ] ) ) {
						return false;
					} else {
						return isset( $meta_value[ $index ][ $field ] ) ? $meta_value[ $index ][ $field ] : false;
					}

				case 'acf':
					return $this->get_acf_repeater_value( $object, $source_field, $field );

				default:
					return apply_filters(
						'jet-engine/listings/data/repeater-value/' . $repeater_source,
						false,
						$object,
						$source_field,
						$field,
						$index,
						$this
					);
			}

		}

		/**
		 * Returns value of ACF repeater field
		 *
		 * @param  [type] $parent_field [description]
		 * @param  [type] $child_field  [description]
		 * @return [type]               [description]
		 */
		public function get_acf_repeater_value( $object, $parent_field, $child_field ) {
			$field_key = $parent_field . '_' . $this->repeater_index . '_' . $child_field;
			return $this->get_meta( $field_key, $object, 'object' );
		}

		/**
		 * Get permalink to current post/term
		 *
		 * @return string
		 */
		public function get_current_object_permalink( $object = null ) {

			if ( ! $object ) {
				$object = $this->get_current_object();
			}

			if ( ! $object || ! is_object( $object ) ) {
				return null;
			}

			$class  = get_class( $object );
			$result = '';

			switch ( $class ) {
				case 'WP_Post':
					return get_permalink( $object->ID );

				case 'WP_Term':
					return get_term_link( $object->term_id );

				case 'WP_User':
					return apply_filters( 'jet-engine/listings/data/user-permalink', false, $object );
			}

			return null;

		}

		/**
		 * Returns available list sources
		 *
		 * @return [type] [description]
		 */
		public function get_field_sources() {

			$sources = array(
				'object'    => __( 'Post/Term/User/Object Data', 'jet-engine' ),
				'meta'      => __( 'Meta Data', 'jet-engine' ),
				'query_var' => __( 'Query Variable', 'jet-engine' ),
			);

			if ( jet_engine()->options_pages ) {
				$sources['options_page'] = __( 'Options', 'jet-engine' );
			}

			if ( jet_engine()->relations ) {
				$sources['relations_hierarchy'] = __( 'Relations Hierarchy', 'jet-engine' );
			}

			$source = false;

			if ( $this->current_listing ) {
				$source = $this->current_listing->get_settings( 'listing_source' );
			}

			$repeater_sources = apply_filters( 'jet-engine/listing/repeater-listing-sources', array( 'repeater' ) );

			if ( in_array( $source, $repeater_sources ) ) {
				$sources['repeater_field'] = __( 'Repeater Field', 'jet-engine' );
			}

			return apply_filters( 'jet-engine/listings/data/sources', $sources );
		}

		/**
		 * Set $current_object property by object id and class name.
		 *
		 * @param mixed  $object_id  Object ID
		 * @param string $class      Object class name
		 * @param bool   $clear_hook Clear or not the `the_post` hook
		 */
		public function set_current_object_by_id( $object_id = null, $class = null, $clear_hook = false ) {
			$object = $this->get_object_by_id( $object_id, $class );
			$this->set_current_object( $object, $clear_hook );
		}

		/**
		 * Get object by object id and class name.
		 *
		 * @param mixed  $object_id Object ID
		 * @param string $class     Object class name
		 *
		 * @return object|null
		 */
		public function get_object_by_id( $object_id = null, $class = null ) {

			$object = null;

			if ( ! $object_id || ! $class ) {
				return $object;
			}

			switch ( $class ) {
				case 'WP_Post':
					$object = get_post( $object_id );
					break;

				case 'WP_Term':
					$object = get_term( $object_id );
					break;

				case 'WP_User':
					$object = get_user_by( 'ID', $object_id );
					break;

				case 'WP_Comment':
					$object = get_comment( $object_id );
					break;

				default:
					$object = apply_filters(
						'jet-engine/listings/data/object-by-id',
						null, $object_id, $class
					);
			}

			return $object;
		}

	}

}
