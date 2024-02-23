<?php
namespace Jet_Engine\Modules\Profile_Builder\Blocks;

use Jet_Engine\Modules\Profile_Builder\Module;

class Profile_Menu extends \Jet_Engine_Blocks_Views_Type_Base {

	/**
	 * Returns block name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return 'profile-menu';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {
		return array(
			'menu_context' => array(
				'type'    => 'string',
				'default' => 'account_page',
			),
			'account_roles' => array(
				'default' => array(),
			),
			'user_roles' => array(
				'default' => array(),
			),
			'add_main_slug' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'menu_layout' => array(
				'type'    => 'string',
				'default' => 'horizontal',
			),
			'menu_layout_tablet' => array(
				'type'    => 'string',
				'default' => 'horizontal',
			),
			'menu_layout_mobile' => array(
				'type'    => 'string',
				'default' => 'horizontal',
			),
		);
	}

	public function render_callback( $attributes = array() ) {

		$context = ! empty( $attributes['menu_context'] ) ? $attributes['menu_context'] : 'account_page';

		if ( 'user_page' === $context ) {
			$roles = ! empty( $attributes['user_roles'] ) ? $attributes['user_roles'] : false;
		} else {
			$roles = ! empty( $attributes['account_roles'] ) ? $attributes['account_roles'] : false;
		}

		$add_main_slug = ! empty( $attributes['add_main_slug'] ) ? $attributes['add_main_slug'] : false;
		$add_main_slug = filter_var( $add_main_slug, FILTER_VALIDATE_BOOLEAN );

		// For preview in Editor.
		if ( $this->is_edit_mode() ) {
			remove_filter(
				'jet-engine/profile-builder/render/profile-menu-items',
				array( Module::instance()->frontend->menu, 'filter_menu_items' )
			);
		}

		ob_start();

		Module::instance()->frontend->profile_menu( array(
			'menu_context'       => $context,
			'roles'              => $roles,
			'add_main_slug'      => $add_main_slug,
			'menu_layout'        => ! empty( $attributes['menu_layout'] ) ? $attributes['menu_layout'] : 'horizontal',
			'menu_layout_tablet' => ! empty( $attributes['menu_layout_tablet'] ) ? $attributes['menu_layout_tablet'] : 'horizontal',
			'menu_layout_mobile' => ! empty( $attributes['menu_layout_mobile'] ) ? $attributes['menu_layout_mobile'] : 'horizontal',
		) );

		return ob_get_clean();
	}

}
