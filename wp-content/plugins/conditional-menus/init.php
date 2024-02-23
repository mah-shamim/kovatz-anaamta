<?php
/*
Plugin Name:  Conditional Menus
Plugin URI:   https://themify.me/conditional-menus
Version:      1.2.5 
Author:       Themify
Author URI:   https://themify.me/
Description:  This plugin enables you to set conditional menus per posts, pages, categories, archive pages, etc.
Text Domain:  themify-cm
Domain Path:  /languages
License:      GNU General Public License v2.0
License URI:  http://www.gnu.org/licenses/gpl-2.0.html
*/

/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 */

if ( !defined( 'ABSPATH' ) ) exit;

register_activation_hook( __FILE__, array( 'Themify_Conditional_Menus', 'activate' ) );

class Themify_Conditional_Menus {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 5 );
		add_action( 'plugins_loaded', array( $this, 'setup' ), 10 );
		add_action( 'wpml_after_startup', array( $this, 'wpml_after_startup' ) );
		add_filter( 'plugin_row_meta', array( $this, 'themify_plugin_meta'), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'action_links') );
	}

	public function constants() {
		if( ! defined( 'THEMIFY_CM_URI' ) ){
			define( 'THEMIFY_CM_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}
	}

	public function themify_plugin_meta( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$row_meta = array(
			  'changelogs'    => '<a href="' . esc_url( 'https://themify.org/changelogs/' ) . basename( dirname( $file ) ) .'.txt" target="_blank" aria-label="' . esc_attr__( 'Plugin Changelogs', 'themify-cm' ) . '">' . esc_html__( 'View Changelogs', 'themify-cm' ) . '</a>'
			);
	 
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}
	public function action_links( $links ) {
		if ( is_plugin_active( 'themify-updater/themify-updater.php' ) ) {
			$tlinks = array(
			 '<a href="' . admin_url( 'index.php?page=themify-license' ) . '">'.__('Themify License', 'themify-cm') .'</a>',
			 );
		} else {
			$tlinks = array(
			 '<a href="' . esc_url('https://themify.me/docs/themify-updater-documentation') . '">'. __('Themify Updater', 'themify-cm') .'</a>',
			 );
		}
		return array_merge( $links, $tlinks );
	}
	public function i18n() {
		load_plugin_textdomain( 'themify-cm', false, '/languages' );
	}

	public function setup() {
		if ( is_admin() ) {
			add_action( 'load-nav-menus.php', array( $this, 'init' ) );
			add_action( 'wp_ajax_themify_cm_get_conditions', array( $this, 'ajax_get_conditions' ) );
			add_action( 'wp_ajax_themify_cm_create_inner_page', array( $this, 'ajax_create_inner_page' ) );
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'activation_redirect' ) );
			add_action( 'wp_delete_nav_menu', array( $this, 'wp_delete_nav_menu' ) );
		} else {
			add_filter( 'wp_nav_menu_args', array( $this, 'setup_menus' ) );
			add_filter( 'theme_mod_nav_menu_locations', array( $this, 'theme_mod_nav_menu_locations' ), 99 );
		}
	}

	public function get_options() {
		remove_filter( 'theme_mod_nav_menu_locations', array( $this, 'theme_mod_nav_menu_locations' ), 99 );
		$options = get_theme_mod( 'themify_conditional_menus', array() );
		$options = wp_parse_args( $options, get_nav_menu_locations() );
		if( ! is_admin() ) {
			add_filter( 'theme_mod_nav_menu_locations', array( $this, 'theme_mod_nav_menu_locations' ), 99 );
		}
		return $options;
	}

	public function theme_mod_nav_menu_locations( $locations = array() ) {
		if( ! empty( $locations ) ) {
			$menu_assignments = $this->get_options();
			$hasLng=function_exists( 'pll_current_language' ) && function_exists( 'pll_default_language' );
			foreach( $locations as $location => $menu_id ) {
				if( empty( $menu_assignments[$location] ) ) continue;

				$menus = $menu_assignments[$location];

				// PolyLang support
				if( $hasLng===true ) {
					if( pll_current_language() !== pll_default_language() ) {
						$polylang_location = $location . '___' . pll_current_language();
						
						if( ! empty( $menu_assignments[$polylang_location] ) ) {
							$menus = $menu_assignments[$polylang_location];
						}
					}
				}

				if( is_array( $menus ) ) {
					foreach( $menus as $id => $new_menu ) {
						if ( empty( $new_menu['menu'] ) || empty( $new_menu['condition'] ) ) {
							continue;
						}
						if( $this->check_visibility( $new_menu['condition'] ) ) {
							if( $new_menu[ 'menu' ] == 0 ) {
								unset( $locations[$location] );
							} else {
								$locations[$location] = $new_menu[ 'menu' ];
							}
						}
					}
				}
			}
		}

		return $locations;
	}

	/**
	 * Where magic happens.
	 * Filters wp_nav_menu_args to dynamically swap parameters sent to it to change what menu displayed.
	 *
	 * @return array
	 */
	public function setup_menus( $args ) {
		$menu_assignments = $this->get_options();
		if (
			! isset( $args['menu'] ) // if $args['menu'] is set, bail. Only swap menus in nav menu locations.
			&& ! empty( $args['theme_location'] ) && isset( $menu_assignments[ $args['theme_location'] ] )
		) {
			if( is_array( $menu_assignments[$args['theme_location']] ) && ! empty( $menu_assignments[$args['theme_location']] ) ) {
				foreach( $menu_assignments[$args['theme_location']] as $id => $new_menu ) {
					if( $new_menu['menu'] == '' || $new_menu['condition'] == '' ) {
						continue;
					}
					if( $this->check_visibility( $new_menu['condition'] ) ) {
						if( $new_menu[ 'menu' ] == 0 ) {
							add_filter( 'pre_wp_nav_menu', array( $this, 'disable_menu' ), 10, 2 );
							$args['echo'] = false;
						} else {
							$args['menu'] = $new_menu[ 'menu' ];
							/* reset theme_location arg, add filter for 3rd party plugins */
							$args['theme_location'] = apply_filters( 'conditional_menus_theme_location', '', $new_menu, $args );
						}
					}
				}
			}
		}

		return $args;
	}

	public function disable_menu( $output, $args ) {
		remove_filter( 'pre_wp_nav_menu', array( $this, 'disable_menu' ), 10, 2 );
		return '';
	}

	public function init() {
		if( isset( $_GET['action'] ) && 'locations' === $_GET['action'] ) {
			$this->save_options();
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue' ) );
		}
	}

	public function save_options() {
		if( isset( $_POST['menu-locations'] ) ) {
		    $themify_cm = isset( $_POST['themify_cm'] ) ? $_POST['themify_cm'] : array();
		    set_theme_mod( 'themify_conditional_menus', $themify_cm );
		}
	}

	public function ajax_get_conditions() {
		check_ajax_referer( 'themify_cm_nonce', 'nonce' );
		include trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/conditions.php';
		die;
	}

	public function admin_enqueue() {
		global $_wp_registered_nav_menus;
		$version='1.2.3';
		self::themify_enque_style( 'themify-conditional-menus', THEMIFY_CM_URI . 'assets/admin.css', null, $version );
		wp_enqueue_script( 'themify-conditional-menus', self::themify_enque(THEMIFY_CM_URI . 'assets/admin.js'), array( 'jquery', 'jquery-ui-tabs' ), $version, true );
		wp_localize_script( 'themify-conditional-menus', 'themify_cm', array(
			'nonce' => wp_create_nonce( 'themify_cm_nonce' ),
			'nav_menus' => array_keys( $_wp_registered_nav_menus ),
			'options' => $this->get_options(),
			'lang' => array(
				'conditions' => __( '+ Conditions', 'themify-cm' ),
				'add_assignment' => __( '+ Conditional Menu', 'themify-cm' ),
				'disable_menu' => __( 'Disable Menu', 'themify-cm' ),
			),
		) );
	}

	/**
	 * Check if an item is visible for the current context
	 *
	 * @return bool
	 */
	public function check_visibility( $logic ) {
		parse_str( $logic, $logic );
		$query_object = get_queried_object();

		// Logged-in check
		if( isset( $logic['general']['logged'] ) ) {
			if( ! is_user_logged_in() ) {
				return false;
			}
			unset( $logic['general']['logged'] );
			if( empty( $logic['general'] ) ) {
			    unset( $logic['general'] );
			}
		}

		// User role check
		if ( ! empty( $logic['roles'] )
			// check if *any* of user's role(s) matches
			&& ! count( array_intersect( wp_get_current_user()->roles, array_keys( $logic['roles'], true ) ) )
		) {
			return false; // bail early.
		}
		unset( $logic['roles'] );

		if( ! empty( $logic ) ) {
			if( ( isset( $logic['general']['home'] ) && is_front_page())
				|| (isset( $logic['general']['404'] ) &&  is_404() )
				|| (isset( $logic['general']['page'] ) &&  is_page() &&  ! is_front_page() )
				|| (isset( $logic['general']['single'] ) && is_single() )
				|| ( isset( $logic['general']['search'] )  && is_search() )
				|| ( isset( $logic['general']['author'] ) && is_author() )
				|| ( isset( $logic['general']['category'] ) && is_category())
				|| ( isset($logic['general']['tag']) && is_tag() )
				|| ( isset($logic['general']['date']) && is_date() )
				|| ( isset($logic['general']['year'])  && is_year())
				|| ( isset($logic['general']['month']) && is_month())
				|| (isset($logic['general']['day']) && is_day())
				|| ( is_singular() && isset( $logic['general'][$query_object->post_type] ) && $query_object->post_type !== 'page' && $query_object->post_type !== 'post' )
				|| ( is_tax() && isset( $logic['general'][$query_object->taxonomy] ) )
				|| ( is_post_type_archive() && isset( $logic['general'][ $query_object->name . '_archive' ] ) )
			) {
				return true;
			} else { // let's dig deeper into more specific visibility rules
				if( ! empty( $logic['tax'] ) ) {
					if(is_singular()){
						if( !empty($logic['tax']['category_single'])){
							if ( empty( $logic['tax']['category_single']['category'] ) ) {
								$cat = get_the_category();
								if(!empty($cat)){
									foreach($cat as $c){
										if($c->taxonomy === 'category' && isset($logic['tax']['category_single']['category'][$c->slug])){
											return true;
										}
									}
								}
								unset($logic['tax']['category_single']['category']);
							}
							foreach ($logic['tax']['category_single'] as $key => $tax) {
								$terms = get_the_terms( get_the_ID(), $key);
								if ( $terms !== false && !is_wp_error($terms) && is_array($terms) ) {
									foreach ( $terms as $term ) {
										if( isset($logic['tax']['category_single'][$key][$term->slug]) ){
											return true;
										}
									}
								}
							}
						}
					} else {
						foreach( $logic['tax'] as $tax => $terms ) {
							$terms = array_keys( $terms );
							if( ( $tax === 'category' && is_category( $terms ) )
								|| ( $tax === 'post_tag' && is_tag( $terms ) )
								|| ( is_tax( $tax, $terms ) )
							) {
								return true;
							}
						}
					}
				}

				if ( ! empty( $logic['post_type'] ) ) {

					foreach( $logic['post_type'] as $post_type => $posts ) {
						$posts = array_keys( $posts );

						if (
							// Post single
							( $post_type === 'post' && is_single( $posts ) )
							// Page view
							|| ( $post_type === 'page' && (
								( 
									( is_page( $posts )
									// check for pages that have a Parent, the slug for these pages are stored differently.
									|| ( isset( $query_object->post_parent ) && $query_object->post_parent > 0 &&
									     ( in_array( '/' . str_replace( strtok( get_home_url(), '?'), '', remove_query_arg( 'lang', get_permalink( $query_object->ID ) ) ), $posts ) ||
									     in_array( str_replace( strtok( get_home_url(), '?'), '', remove_query_arg( 'lang', get_permalink( $query_object->ID ) ) ), $posts ) ||
									     in_array( '/'.$this->child_post_name($query_object).'/', $posts ) )
									  )
								) )
								|| ( ! is_front_page() && is_home() &&  in_array( get_post_field( 'post_name', get_option( 'page_for_posts' ) ), $posts,true ) ) // check for Posts page
								|| ( class_exists( 'WooCommerce' ) && function_exists( 'is_shop' ) && is_shop() && in_array( get_post_field( 'post_name', wc_get_page_id( 'shop' ) ), $posts )  ) // check for WC Shop page
							) )
							// Custom Post Types single view check
							|| ( is_singular( $post_type ) && in_array( $query_object->post_name, $posts,true ) )
							|| ( is_singular( $post_type ) && isset( $query_object->post_parent ) && $query_object->post_parent > 0 && in_array( '/'.$this->child_post_name($query_object).'/', $posts,true ) )
							// for all posts of a post type.
							|| ( is_singular( $post_type ) && get_post_type() === $post_type && in_array( 'E_ALL', $posts ) )
						) {
							return true;
						}
					}
				}
			}
			return false;
		}

		return true;
	}

	/**
	 * Render pagination for specific page.
	 *
	 * @param Integer $current_page The current page that needs to be rendered.
	 * @param Integer $num_of_pages The number of all pages.
	 *
	 * @return String The HTML with pagination.
	 */
	function create_page_pagination( $current_page, $num_of_pages ) {
		$links_in_the_middle = 4;
		$links_in_the_middle_min_1 = $links_in_the_middle - 1;
		$first_link_in_the_middle   = $current_page - floor( $links_in_the_middle_min_1 / 2 );
		$last_link_in_the_middle    = $current_page + ceil( $links_in_the_middle_min_1 / 2 );
		if ( $first_link_in_the_middle <= 0 ) {
			$first_link_in_the_middle = 1;
		}
		if ( ( $last_link_in_the_middle - $first_link_in_the_middle ) != $links_in_the_middle_min_1 ) {
			$last_link_in_the_middle = $first_link_in_the_middle + $links_in_the_middle_min_1;
		}
		if ( $last_link_in_the_middle > $num_of_pages ) {
			$first_link_in_the_middle = $num_of_pages - $links_in_the_middle_min_1;
			$last_link_in_the_middle  = (int) $num_of_pages;
		}
		if ( $first_link_in_the_middle <= 0 ) {
			$first_link_in_the_middle = 1;
		}
		$pagination = '';
		if ( $current_page != 1 ) {
			$pagination .= '<a href="' . ( $current_page - 1 ) . '" class="prev page-numbers ti-angle-left"/>';
		}
		if ( $first_link_in_the_middle >= 3 && $links_in_the_middle < $num_of_pages ) {
			$pagination .= '<a href="1" class="page-numbers">1</a>';

			if ( $first_link_in_the_middle != 2 ) {
				$pagination .= '<span class="page-numbers extend">...</span>';
			}
		}
		for ( $i = $first_link_in_the_middle; $i <= $last_link_in_the_middle; $i ++ ) {
			if ( $i == $current_page ) {
				$pagination .= '<span class="page-numbers current">' . $i . '</span>';
			} else {
				$pagination .= '<a href="' . $i . '" class="page-numbers">' . $i . '</a>';
			}
		}
		if ( $last_link_in_the_middle < $num_of_pages ) {
			if ( $last_link_in_the_middle != ( $num_of_pages - 1 ) ) {
				$pagination .= '<span class="page-numbers extend">...</span>';
			}
			$pagination .= '<a href="' . $num_of_pages . '" class="page-numbers">' . $num_of_pages . '</a>';
		}
		if ( $current_page != $last_link_in_the_middle ) {
			$pagination .= '<a href="' . ( $current_page + $i ) . '" class="next page-numbers ti-angle-right"></a>';
		}

		return $pagination;
	}

	function ajax_create_inner_page() {
		check_ajax_referer( 'themify_cm_nonce', 'nonce' );
		if ( empty( $_POST['type'] ) ) {
			die;
		}
		$type = explode( ':', $_POST['type'] );
		$paged = isset( $_POST['paged'] ) ? (int) $_POST['paged'] : 1;
		echo $this->create_inner_page( $type[0], $type[1], $paged );
		die;
	}

	/**
	 * Renders pages, posts types and categories items based on current page.
	 *
	 * @param string $type The type of items to render.
	 *
	 * @return array The HTML to render items as HTML and original values.
	 */
	function create_inner_page( $item_type, $type, $paged = 1 ) {
		$posts_per_page = 26;
		$output = '';
		if ( 'post_type' === $item_type ) {
			$query = new WP_Query( array( 'post_type' => $type, 'posts_per_page' => $posts_per_page, 'post_status' => 'publish', 'order' => 'ASC', 'orderby' => 'title', 'paged' => $paged ) );
			if ( $query->have_posts() ) {
				$num_of_single_pages = $query->found_posts;
				$num_of_pages        = (int) ceil( $num_of_single_pages / $posts_per_page );
				$output .= '<div class="themify-visibility-items-page themify-visibility-items-page-' . $paged . '">';
				foreach ( $query->posts as $post ) :
					$post->post_name = $this->child_post_name($post);
					if ( $post->post_parent > 0 ) {
						$post->post_name = '/' . $post->post_name . '/';
					}
					/* note: slugs are more reliable than IDs, they stay unique after export/import */
					$output .= '<label><input type="checkbox" name="' . esc_attr( 'post_type[' . $type . '][' . $post->post_name . ']' ) . '" /><span data-tooltip="'.get_permalink($post->ID).'">' . esc_html( $post->post_title ) . '</span></label>';
				endforeach;

				if ( $num_of_pages > 1 ) {
					$output .= '<div class="themify-visibility-pagination">';
					$output .= $this->create_page_pagination( $paged, $num_of_pages );
					$output .= '</div>';
				}
				$output .= '</div><!-- .themify-visibility-items-page -->';
			}
		} else if ( 'tax' === $item_type || 'in_tax' === $item_type ) {
			$total = wp_count_terms( [ 'taxonomy' => $type, 'hide_empty' => false ] );
			if ( ! is_wp_error( $total ) && ! empty( $total ) ) {
				$prefix = 'tax' === $item_type ? "tax[{$type}]" : "tax[category_single][{$type}]";
				$terms = get_terms( array( 'taxonomy' => $type, 'hide_empty' => false, 'number' => $posts_per_page, 'offset' => ( $paged - 1 ) * $posts_per_page ) );
				$num_of_pages = (int) ceil( $total / $posts_per_page );
				$output .= '<div class="themify-visibility-items-page themify-visibility-items-page-' . $paged . '">';
				foreach ( $terms as $term ) :
					$data = ' data-slug="'.$term->slug.'"';
					if ( $term->parent != '0' ) {
						$parent  = get_term( $term->parent, $type );
						$data .= ' data-parent="'.$parent->slug.'"';
					}
					$output  .= '<label><input'.$data.' type="checkbox" name="' . $prefix . '[' . $term->slug . ']" /><span data-tooltip="'.get_term_link($term).'">' . $term->name . '</span></label>';
				endforeach;
				if ( $num_of_pages > 1 ) {
					$output .= '<div class="themify-visibility-pagination">';
					$output .= $this->create_page_pagination( $paged, $num_of_pages );
					$output .= '</div>';
				}
				$output .= '</div><!-- .themify-visibility-items-page -->';
			}
		}

		return $output;
	}

	private function child_post_name($post) {
		$str = $post->post_name;

		if ( $post->post_parent > 0 ) {
			$parent = get_post($post->post_parent);
			if ( $parent ) {
				$parent->post_name = $this->child_post_name($parent);
				$str = $parent->post_name . '/' . $str;
			}
		}

		return $str;
	}

	public function add_plugin_page() {
		add_management_page(
			__( 'Themify Conditional Menus', 'themify-cm' ),
			__( 'Conditional Menus', 'themify-cm' ),
			'manage_options',
			'conditional-menus',
			array( $this, 'create_admin_page' ),
			99
		);
	}

	public function create_admin_page() {
		include( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'docs/index.html' );
	}

	public static function activate( $network_wide ) {
		if( version_compare( get_bloginfo( 'version' ), '3.9', '<' ) ) {
			/* the plugin requires at least 3.9 */
			deactivate_plugins( basename( __FILE__ ) ); // Deactivate the plugin
		} else {
			if( ! $network_wide && ! isset( $_GET['activate-multi'] ) ) {
				add_option( 'themify_conditional_menus_activation_redirect', true );
			}
		}
	}

	public function activation_redirect() {
		if( get_option( 'themify_conditional_menus_activation_redirect', false ) ) {
			delete_option( 'themify_conditional_menus_activation_redirect' );
			wp_redirect( admin_url( 'admin.php?page=conditional-menus' ) );
		}
	}

	/**
	 * Disable WPML nav menu filtering in the Menu Locations manager
	 *
	 * @since 1.0.2
	 */
	public function wpml_after_startup() {
		global $pagenow;
		if( isset( $_GET['action'] ) && 'locations' === $_GET['action'] && is_admin() && $pagenow === 'nav-menus.php' ) {
		    remove_all_filters( 'get_terms', 1 );
		}
	}

	/**
	 * Remove menu assignments when the menu gets deleted
	 *
	 * @since 1.0.7
	 */
	function wp_delete_nav_menu( $menu_id ) {
		$options = get_theme_mod( 'themify_conditional_menus', array() );
		if( ! empty( $options ) ) {
			foreach( $options as $location => $assignments ) {
				if( is_array( $assignments ) && ! empty( $assignments ) ) {
					foreach( $assignments as $key => $menu ) {
						if( $menu['menu'] == $menu_id ) {
							unset( $options[$location][$key] );
						}
					}
				}
			}
		}
		set_theme_mod( 'themify_conditional_menus', $options );
	}
	
	private static function themify_enque($url){
	    static $is=null;
	    if($is===null){
		$is=  function_exists('themify_enque');
	    }
	    if($is===true){
		return themify_enque($url);
	    }
	    return $url;
	}
	
	private static function themify_enque_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all' ){
	    static $is=null;
	    if($is===null){
		$is=  function_exists('themify_is_themify_theme') && themify_is_themify_theme();
	    }
	    if($is===true){
		themify_enque_style($handle,$src,$deps,$ver,$media);
	    }
	    else{
		wp_enqueue_style($handle,$src,$deps,$ver,$media);
	    }
	}
}
$themify_cm = new Themify_Conditional_Menus;
