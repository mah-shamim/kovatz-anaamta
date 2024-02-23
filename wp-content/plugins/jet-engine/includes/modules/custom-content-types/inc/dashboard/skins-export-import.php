<?php

namespace Jet_Engine\Modules\Custom_Content_Types\Dashboard;

use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Engine\Modules\Custom_Content_Types\Factory;

class Skins_Export_Import {
	
	public $key = 'custom_content_types';

	public function __construct() {

		add_filter( 'jet-engine/dashboard/export/items',   array( $this, 'register_export_item' ) );
		add_filter( 'jet-engine/dashboard/export/content', array( $this, 'export_sample_content_types' ) );

		add_action( 'jet-engine/dashboard/templates/export/after-items', array( $this, 'print_export_items' ) );

		add_action( 'jet-engine/dashboard/import/process', array( $this, 'process_import' ), 10, 2 );

	}

	public function register_export_item( $items ) {

		$items[] = array(
			'key'     => $this->key,
			'var'     => $this->key,
			'cb'      => array( $this, 'export_content_types' ),
			'default' => array(),
		);

		return $items;
	}

	public function export_content_types( $content_types = array(), $export_instance = null ) {

		if ( ! is_array( $content_types ) ) {
			$content_types = array( $content_types );
		}

		$export_instance->id .= implode( '', $content_types );

		return Module::instance()->manager->data->get_raw( array( 'id' => $content_types ) );
	}

	public function export_sample_content_types( $result ) {

		$content_types = ! empty( $_REQUEST[ $this->key ] ) ? $_REQUEST[ $this->key ] : array();

		if ( empty( $content_types ) ) {
			return $result;
		}

		$content = array();

		foreach ( $content_types as $type_id ) {
			$content_type = Module::instance()->manager->get_content_type_by_id( intval( $type_id ) );

			if ( ! $content_type ) {
				continue;
			}

			$items  = $content_type->db->query( array(), 1 );
			$fields = $content_type->get_formatted_fields();

			if ( empty( $items ) ) {
				continue;
			}

			$item = $items[0];

			foreach ( $fields as $field ) {

				if ( 'field' === $field['object_type'] && 'media' === $field['type'] && ! empty( $item[ $field['name'] ] ) ) {

					$img_data = \Jet_Engine_Tools::get_attachment_image_data_array( $item[ $field['name'] ], 'url' );

					if ( empty( $img_data ) || empty( $img_data['url'] ) ) {
						continue;
					}

					$item[ $field['name'] ] = array(
						'media'  => true,
						'url'    => $img_data['url'],
						'format' => ! empty( $field['value_format'] ) ? $field['value_format'] : 'id',
					);
				}
			}

			$content[] = $item;
		}

		if ( ! empty( $content ) ) {
			$result[ $this->key ] = $content;
		}

		return $result;
	}
	
	public function print_export_items() {

		$types_options = array();
		$content_types = Module::instance()->manager->get_content_types();

		foreach ( $content_types as $type => $instance ) {
			$types_options[] = array(
				'value' => $instance->type_id,
				'label' => $instance->get_arg( 'name' ),
			);
		}

		?>
		<div class="jet-engine-skins-settings-item">
			<cx-vui-checkbox
				name="<?php echo $this->key; ?>"
				label="<?php _e( 'Custom Content Types', 'jet-engine' ); ?>"
				return-type="array"
				:wrapper-css="[ 'vertical-fullwidth' ]"
				:options-list="<?php echo htmlspecialchars( json_encode( $types_options ) ) ?>"
				v-model="skin.<?php echo $this->key; ?>"
			></cx-vui-checkbox>
		</div>
		<?php
	}

	public function process_import( $content, $import_instance ) {

		$this->import_content_types( $content, $import_instance );
		$this->import_sample_content_types( $content, $import_instance );

	}

	public function import_content_types( $content, $import_instance ) {

		$content_types = isset( $content[ $this->key ] ) ? $content[ $this->key ] : array();

		if ( empty( $content_types ) ) {
			return;
		}

		foreach ( $content_types as $content_type ) {

			unset( $content_type['id'] );

			$content_type['slug']        = Module::instance()->manager->data->sanitize_slug( $content_type['slug'] );
			$content_type['labels']      = maybe_unserialize( $content_type['labels'] );
			$content_type['args']        = maybe_unserialize( $content_type['args'] );
			$content_type['meta_fields'] = maybe_unserialize( $content_type['meta_fields'] );

			$is_content_type_exists = Module::instance()->manager->get_content_types( $content_type['slug'] );

			if ( $is_content_type_exists ) {
				$import_instance->errors[] = '<b>' . $content_type['args']['name'] . '</b> ' . __( 'Content Type already exists', 'jet-engine' );
				continue;
			}

			$id = Module::instance()->manager->data->update_item_in_db( $content_type );

			if ( $id ) {

				Module::instance()->manager->data->after_item_update( $content_type, true );

				if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Factory' ) ) {
					require Module::instance()->module_path( 'factory.php' );
				}

				$instance = new Factory( $content_type['args'], $content_type['meta_fields'], $id );
				Module::instance()->manager->register_instance( $content_type['args']['slug'], $instance );

				if ( empty( $import_instance->log[ $this->key ] ) ) {
					$import_instance->log[ $this->key ] = array( 'items' => array() );
				}

				$import_instance->log[ $this->key ]['items'][] = $content_type['args']['name'];
			}

		}

		if ( ! empty( $import_instance->log[ $this->key ] ) ) {
			$import_instance->log[ $this->key ]['label'] = __( 'Custom Content Types', 'jet-engine' );
		}

	}

	public function import_sample_content_types( $content, $import_instance ) {

		$sample_content_types = isset( $content['content'][ $this->key ] ) ? $content['content'][ $this->key ] : array();

		if ( empty( $sample_content_types ) ) {
			return;
		}

		foreach ( $sample_content_types as $content_type_item ) {

			if ( empty( $content_type_item['cct_slug'] ) ) {
				continue;
			}

			$content_type = Module::instance()->manager->get_content_types( $content_type_item['cct_slug'] );

			if ( ! $content_type ) {
				continue;
			}

			unset( $content_type_item['_ID'] );

			$content_type_item = $import_instance->prepare_meta( $content_type_item );

			$handler = $content_type->get_item_handler();
			$item_id = $handler->update_item( $content_type_item );

			if ( $item_id ) {

				if ( empty( $import_instance->log[ 'sample_' . $this->key ] ) ) {
					$import_instance->log[ 'sample_' . $this->key ] = array( 'items' => array() );
				}

				$import_instance->log[ 'sample_' . $this->key ]['items'][] = $content_type->get_arg( 'name' ) . ' ' . __( 'Sample Item', 'jet-engine' );
			}
		}

		if ( ! empty( $import_instance->log[ 'sample_' . $this->key ] ) ) {
			$import_instance->log[ 'sample_' . $this->key ]['label'] = __( 'Content Types', 'jet-engine' );
		}

	}
}