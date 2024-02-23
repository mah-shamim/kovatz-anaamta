<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Meta_Boxes;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Product_Data_Panel' ) ) {
	require_once jet_engine()->plugin_path( 'includes/compatibility/packages/woocommerce/inc/meta-boxes/product-data-panel.php' );
}

class Product_Variation_Panel extends Product_Data_Panel {

	public function __construct( $meta_box ) {

		if ( ! jet_engine()->meta_boxes->conditions->check_conditions( '', $meta_box['args'] ) ) {
			return;
		}

		$object_name = $meta_box['args']['name'] . ' ' . __( '( WC product variation fields)', 'jet-engine' );

		jet_engine()->meta_boxes->store_fields( $object_name, $meta_box['meta_fields'], 'woocommerce_product_variation' );

		if ( ! empty( $meta_box['args']['show_edit_link'] ) ) {
			$this->add_edit_link( add_query_arg(
				[
					'page'            => 'jet-engine-meta',
					'cpt_meta_action' => 'edit',
					'id'              => $meta_box['id'],
				],
				admin_url( 'admin.php' )
			) );
		}

		if ( ! empty( $meta_box['args']['hide_field_names'] ) ) {
			$this->hide_field_names = $meta_box['args']['hide_field_names'];
		}

		$this->fields  = $this->prepare_meta_fields( $meta_box['meta_fields'] );

		//$this->init_builder();
		add_action( 'admin_enqueue_scripts', [ $this, 'maybe_enqueue_custom_scripts' ], 0 );

		$position = $meta_box['args']['wc_product_variation_position'] ?? 'woocommerce_product_after_variable_attributes';

		add_action( $position, [ $this, 'add_meta_box_variation_content' ], 10, 3 );
		add_action( 'woocommerce_save_product_variation', [ $this, 'save_variation_meta_box_fields' ], 10, 2 );

	}

	/**
	 * Maybe enqueue custom scripts.
	 *
	 * Enqueue some custom scripts if certain fields meets in meta box.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function maybe_enqueue_custom_scripts( $hook ) {

		if ( ! $this->is_allowed_on_current_admin_hook( $hook ) ) {
			return;
		}

		$this->init_builder();

		foreach ( $this->fields as $field ) {
			switch ( $field['type'] ) {
				// --- Make this part better in the future.
				case 'colorpicker':
					wp_enqueue_script(
						'cx-colorpicker-alpha',
						jet_engine()->plugin_url( 'framework/interface-builder/assets/lib/colorpicker/wp-color-picker-alpha.min.js' ),
						[ 'wp-color-picker' ],
						'1.0.0',
						true
					);

					break;
				// --- end of the part.

				default:
					break;
			}
		}
	}

	/**
	 * Add meta box variation content.
	 *
	 * Display JetEngine meta box field inside WC product variation panel.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @param int      $loop           Position in the loop.
	 * @param array    $variation_data Variation data.
	 * @param \WP_Post $variation      Post data.
	 *
	 * @return void
	 */
	public function add_meta_box_variation_content( $loop, $variation_data, $variation ) {

		$this->init_builder();

		foreach ( $this->fields as $key => $field ) {
			if ( ! $key ) {
				continue;
			}

			if ( 'iconpicker' === $field['type'] ) {
				continue;
			}

			$field['id']   = ! empty( $field['id'] ) ? $field['id'] . '[' . $loop . ']' : $key . '[' . $loop . ']';
			$field['name'] = ! empty( $field['name'] ) ? $field['name'] . '[' . $loop . ']' : $key . '[' . $loop . ']';

			if ( ! empty( $field['required'] ) ) {
				$field['required'] = false;
			}

			$this->register_builder_field( $variation->ID, $key, $field );
		}

		if ( $this->edit_link ) {
			echo '<div class="jet-engine-meta-box-link-wrapper">';
			$this->render_edit_link();
			echo '</div>';
		}

		$this->builder->render();

	}

	/**
	 * Save variation meta box fields.
	 *
	 * Save product variation meta values for JetEngine meta fields in product variation panels.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @param int $variation_id Variation ID.
	 * @param int $i            Field index.
	 *
	 * @return void
	 */
	public function save_variation_meta_box_fields( $variation_id, $i ) {
		foreach ( $this->fields as $key => $field ) {
			if ( isset( $_POST[ $key ][ $i ] ) ) {
				if ( 'stepper' === $field['type'] ) {
					if ( ! empty( $field['min_value'] ) && $field['min_value'] > $_POST[ $key ][ $i ] ) {
						$_POST[ $key ][ $i ] = $field['min_value'];
					}

					if ( ! empty( $field['max_value'] ) && $field['max_value'] < $_POST[ $key ][ $i ] ) {
						$_POST[ $key ][ $i ] = $field['max_value'];
					}
				}

				$_POST[ $key ][ $i ] = $this->sanitize_meta( $field, $_POST[ $key ][ $i ] );

				update_post_meta( $variation_id, $key, $_POST[ $key ][ $i ] );
			}
		}
	}

}