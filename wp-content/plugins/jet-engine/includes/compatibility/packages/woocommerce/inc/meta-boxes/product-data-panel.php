<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Meta_Boxes;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
	require jet_engine()->plugin_path( 'includes/components/meta-boxes/post.php' );
}

class Product_Data_Panel extends \Jet_Engine_CPT_Meta {

	/**
	 * Args.
	 *
	 * Meta box arguments holder.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @var mixed
	 */
	public $args;

	/**
	 * Fields.
	 *
	 * Meta box fields holder.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Builder.
	 *
	 * A reference to an instance of the interface builder class.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @var \CX_Interface_Builder
	 */
	public $builder;

	public function __construct( $meta_box ) {

		$this->args = $meta_box['args'];

		if ( ! jet_engine()->meta_boxes->conditions->check_conditions( '', $this->args ) ) {
			return;
		}

		$object_name = $this->args['name'] . ' ' . __( '( WC product data fields)', 'jet-engine' );

		jet_engine()->meta_boxes->store_fields( $object_name, $meta_box['meta_fields'], 'woocommerce_product_data' );

		if ( ! empty( $this->args['show_edit_link'] ) ) {
			$this->add_edit_link( add_query_arg(
				[
					'page'            => 'jet-engine-meta',
					'cpt_meta_action' => 'edit',
					'id'              => $meta_box['id'],
				],
				admin_url( 'admin.php' )
			) );
		}

		if ( ! empty( $this->args['hide_field_names'] ) ) {
			$this->hide_field_names = $this->args['hide_field_names'];
		}

		$this->fields = $this->prepare_meta_fields( $meta_box['meta_fields'] );

		add_action( 'admin_enqueue_scripts', [ $this, 'register_fields' ], 0 );

		$panel = $this->args['wc_product_data_panel'] ?? 'custom';

		if ( 'custom' === $panel ) {
			add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_meta_box_product_data_tab' ] );
			add_action( 'woocommerce_product_data_panels', [ $this, 'creat_meta_box_product_data_panel' ] );
		} else {
			add_action( 'woocommerce_product_options_' . $panel, [ $this, 'add_meta_box_product_data_content' ] );
		}

		add_action( 'woocommerce_process_product_meta', [ $this, 'save_meta_box_option_fields' ] );

	}

	/**
	 * Init builder.
	 *
	 * Initialize cherry X interface builder.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function init_builder() {

		$this->builder = $this->get_builder_for_meta( [
			'views' => [
				'control' => jet_engine()->plugin_path( 'includes/compatibility/packages/woocommerce/inc/templates/admin/control.php' ),
			],
		] );

		//self::$wrappers_hooked = false;

	}

	/**
	 * Register fields.
	 *
	 * Register cherry X interface builder meta fields.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_fields( $hook ) {

		if ( ! $this->is_allowed_on_current_admin_hook( $hook ) ) {
			return;
		}

		$this->init_builder();

		foreach ( $this->fields as $key => $field ) {
			if ( ! $key ) {
				continue;
			}

			$field['id'] = $field['id'] ?? $key;

			$this->register_builder_field( get_the_ID(), $key, $field );
		}

	}

	/**
	 * Add meta box product data tab.
	 *
	 * Add custom JetEngine meta box tab to WC product data panel.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @param array $tabs List of registered tabs.
	 *
	 * @return mixed
	 */
	public function add_meta_box_product_data_tab( $tabs ) {

		$classes = [];

		if ( ! empty( $this->args['wc_product_data_exclude_include'] ) && 'none' !== $this->args['wc_product_data_exclude_include'] ) {
			$product_types = $this->args['wc_product_data_product_types'] ?? [];

			if ( ! empty( $product_types ) ) {
				foreach ( $product_types as $product_type ) {
					$classes[] = $this->args['wc_product_data_exclude_include'] . '_' . $product_type;
				}
			}
		}

		$name = $this->args['name'] ?? __( 'JetEngine Meta Box', 'jet-engine' );
		$id   = preg_replace( '/[\s+\-]/', '_', strtolower( $name ) );

		$tabs[ $id ] = [
			'label'    => esc_attr( $name ),
			'target'   => esc_attr( $id ),
			'class'    => $classes,
			'priority' => $this->args['wc_product_data_priority'] ?? 80,
		];

		return $tabs;

	}

	/**
	 * Create meta box product data panel.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function creat_meta_box_product_data_panel() {

		$name = $this->args['name'] ?? __( 'JetEngine Meta Box', 'jet-engine' );
		$id   = preg_replace( '/[\s+\-]/', '_', strtolower( $name ) );
		?>

		<div id="<?php echo esc_attr( $id ); ?>" class="panel woocommerce_options_panel hidden">
			<?php $this->add_meta_box_product_data_content(); ?>
		</div>

		<?php
	}

	/**
	 * Add meta box product data content.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function add_meta_box_product_data_content() {
		?>
		<div class='options_group jet-engine-meta-wrap'>
			<?php
			if ( $this->edit_link ) {
				$this->render_edit_link();
			}

			$this->builder->render();
			?>
		</div>
		<?php
	}

	/**
	 * Save meta box option fields.
	 *
	 * Save product meta values for JetEngine meta fields in product data panels.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @param int $post_id WP post id.
	 *
	 * @return void
	 */
	public function save_meta_box_option_fields( $post_id ) {
		foreach ( $this->fields as $key => $field ) {
			if ( isset( $_POST[ $key ] ) ) {
				$_POST[ $key ] = $this->sanitize_meta( $field, $_POST[ $key ] );

				update_post_meta( $post_id, $key, $_POST[ $key ] );
			}
		}
	}

	/**
	 * Register builder field.
	 *
	 * Register fields in interface builder.
	 *
	 * @since  3.2.0
	 * @since  3.2.7 Added checkbox save as array option handle.
	 * @access public
	 *
	 * @param string|int $post_id WP post ID.
	 * @param string     $key     Field name key.
	 * @param array      $field   List of field properties.
	 *
	 * @return void
	 */
	public function register_builder_field( $post_id, $key, $field ) {

		$disallowed_types = [ 'html', 'map', 'repeater', 'settings', 'wysiwyg' ];

		if ( in_array( $field['type'], $disallowed_types ) ) {
			return;
		}

		$value      = get_post_meta( $post_id, $key, true );
		$input_type = ! empty( $field['input_type'] ) ? $field['input_type'] : false;

		if ( ! $input_type ) {
			$input_type = ! empty( $field['type'] ) ? $field['type'] : $input_type;
		}

		switch ( $input_type ) {
			case 'date':
				if ( ! empty( $field['is_timestamp'] ) && \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					$value = date( 'Y-m-d', $value );
				}
				break;

			case 'datetime':
			case 'datetime-local':
				if ( ! empty( $field['is_timestamp'] ) && \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					$value = date( 'Y-m-d\TH:i', $value );
				}
				break;
			case 'checkbox':
				if ( ! empty( $field['is_array'] ) ) {

					if ( ! is_array( $value ) ) {
						$value = [ $value ];
					}

					$result = [];

					foreach ( $value as $val ) {
						$result[ $val ] = 'true';
					}

					foreach ( $field['options'] as $opt_val => $opt_label ) {
						if ( ! in_array( $opt_val, $value ) ) {
							$result[ $opt_val ] = 'false';
						}
					}

					$value = $result;
				}
				break;
		}

		if ( ! empty( $value ) ) {
			$field['value'] = $value;
		}

		if ( ! empty( $field['allow_custom_value'] ) ) {
			$field['allow_custom_value'] = false;
		}

		$element           = $field['element'] ?? 'control';
		$register_callback = 'register_' . $element;

		if ( method_exists( $this->builder, $register_callback ) ) {
			call_user_func( [ $this->builder, $register_callback ], $field );
		}

	}

	/**
	 * To timestamp.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @param array $field Field parameters.
	 *
	 * @return boolean
	 */
	public function to_timestamp( $field ) {

		if ( empty( $field['input_type'] ) || empty( $field['is_timestamp'] ) ) {
			return false;
		}

		if ( ! in_array( $field['input_type'], [ 'date', 'datetime-local' ] ) ) {
			return false;
		}

		return ( true === $field['is_timestamp'] );

	}

	/**
	 * Sanitize meta.
	 *
	 * Sanitize product meta values for JetEngine meta fields.
	 *
	 * @since  3.2.7
	 * @access public
	 *
	 * @param array $field       Fields option list.
	 * @param mixed $field_value Field value.
	 *
	 * @return mixed
	 */
	public function sanitize_meta( $field, $field_value ) {

		if ( $this->to_timestamp( $field ) ) {
			return apply_filters( 'cx_post_meta/strtotime', strtotime( $field_value ), $field_value );
		}

		if ( 'checkbox' === $field['type'] && ! empty( $field['is_array'] ) ) {
			$result = [];

			if ( in_array( 'true', $field_value ) || in_array( 'false', $field_value ) ) {
				foreach ( $field_value as $raw_key => $raw_value ) {
					$value = filter_var( $raw_value, FILTER_VALIDATE_BOOLEAN );

					if ( $value ) {
						$result[] = $raw_key;
					}
				}
			}

			return $result;
		}

		return $field_value;

	}

	public function is_allowed_on_current_admin_hook( $hook ) {

		if ( null !== $this->is_allowed_on_admin_hook ) {
			return $this->is_allowed_on_admin_hook;
		}

		$allowed_hooks = array(
			'post-new.php',
			'post.php',
		);

		if ( ! in_array( $hook, $allowed_hooks ) ) {
			$this->is_allowed_on_admin_hook = false;
			return $this->is_allowed_on_admin_hook;
		}

		if ( 'product' !== get_post_type() ) {
			$this->is_allowed_on_admin_hook = false;
			return $this->is_allowed_on_admin_hook;
		}

		$this->is_allowed_on_admin_hook = true;
		return $this->is_allowed_on_admin_hook;
	}

}
