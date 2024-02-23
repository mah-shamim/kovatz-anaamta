<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Menu {

	public function __construct() {

		add_filter(
			'jet-engine/profile-builder/render/profile-menu-items',
			array( $this, 'filter_menu_items' ), 10, 2
		);

	}

	public function get_available_menu_roles( $context = 'account_page', $for_js = false ) {

		$settings = Module::instance()->settings->get();

		switch ( $context ) {
			case 'user_page':
				$items = ! empty( $settings['user_page_structure'] ) ? $settings['user_page_structure'] : array();
				break;

			default:
				$items = ! empty( $settings['account_page_structure'] ) ? $settings['account_page_structure'] : array();
				break;
		}

		if ( empty( $items ) ) {
			return array();
		}

		$raw_roles = array();

		foreach ( $items as $item ) {
			if ( ! empty( $item['roles'] ) ) {
				$raw_roles = array_merge( $raw_roles, $item['roles'] );
			}
		}

		global $wp_roles;

		$roles     = $wp_roles->roles;
		$result    = array();
		$raw_roles = array_unique( $raw_roles );

		if ( ! empty( $raw_roles ) ) {
			foreach ( $raw_roles as $role ) {

				if ( isset( $roles[ $role ] ) ) {

					if ( $for_js ) {
						$result[] = array(
							'value' => $role,
							'label' => $roles[ $role ]['name'],
						);
					} else {
						$result[ $role ] = $roles[ $role ]['name'];
					}

				}

			}
		}

		return $result;

	}

	public function filter_menu_items( $items = array(), $args = array() ) {

		if ( empty( $items ) ) {
			return $items;
		}

		switch ( $args['menu_context'] ) {
			case 'user_page':
				$user = Module::instance()->query->get_queried_user();
				break;

			default:
				$user = wp_get_current_user();
				break;
		}

		if ( ! $user ) {
			return array();
		}

		$result     = array();
		$show_roles = ! empty( $args['roles'] ) ? $args['roles'] : array();

		foreach ( $items as $item ) {

			if ( empty( $item['roles'] ) ) {
				$intersect = $user->roles;
			} else {
				$intersect = array_intersect( $user->roles, $item['roles'] );

				if ( ! empty( $intersect ) ) {
					$intersect = $item['roles'];
				}

			}

			if ( ! empty( $intersect ) ) {
				if ( ! empty( $show_roles ) ) {
					$intersect = array_intersect( $intersect, $show_roles );
				}
			}

			if ( ! empty( $intersect ) ) {
				$result[] = $item;
			}

		}

		return $result;

	}

	public function get_profile_menu( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'menu_context'       => 'account_page',
			'menu_layout'        => 'horizontal',
			'menu_layout_mobile' => 'vertical',
			'add_main_slug'      => false,
			'roles'              => false,
			'custom_class'       => '',
		) );

		$settings = Module::instance()->settings->get();

		switch ( $args['menu_context'] ) {
			case 'user_page':
				$page  = 'single_user_page';
				$items = ! empty( $settings['user_page_structure'] ) ? $settings['user_page_structure'] : array();
				break;

			default:
				$page  = 'account_page';
				$items = ! empty( $settings['account_page_structure'] ) ? $settings['account_page_structure'] : array();
				break;
		}

		$items = apply_filters( 'jet-engine/profile-builder/render/profile-menu-items', $items, $args );

		if ( empty( $items ) ) {
			return;
		}

		$base_class = 'jet-profile-menu';
		$classes    = array(
			$base_class,
			'layout--' . $args['menu_layout'],
			'layout-tablet--' . $args['menu_layout_tablet'],
			'layout-mobile--' . $args['menu_layout_mobile'],
			'context--' . $args['menu_context']
		);

		if ( ! empty( $args['custom_class'] ) ) {
			$classes[] = $args['custom_class'];
		}

		ob_start();

		do_action( 'jet-engine/profile-builder/render/before-profile-menu', $args );

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		foreach ( $items as $index => $item ) {

			if ( ! Module::instance()->query->is_subpage_visible( $item ) ) {
				continue;
			}

			if ( ! empty( $item['hide'] ) ) {
				continue;
			}

			do_action( 'jet-engine/profile-builder/render/before-profile-menu-item', $item, $args );

			if ( empty( $args['add_main_slug'] ) ) {
				$slug = ( 0 < $index ) ? $item['slug'] : null;
			} else {
				$slug = $item['slug'];
			}

			$item_html = sprintf(
				'<div class="%3$s__item %4$s"><a class="%3$s__item-link" href="%2$s">%1$s</a></div>',
				$item['title'],
				Module::instance()->settings->get_subpage_url( $slug, $page ),
				$base_class,
				( Module::instance()->query->is_subpage_now( $slug ) ? 'is-active' : '' )
			);

			echo apply_filters( 'jet-engine/profile-builder/render/profile-menu-item', $item_html, $item, $args );

			do_action( 'jet-engine/profile-builder/render/after-profile-menu-item', $item, $args );

		}

		echo '</div>';

		do_action( 'jet-engine/profile-builder/render/after-profile-menu', $args );

		$result = ob_get_clean();

		return $result;

	}

}
