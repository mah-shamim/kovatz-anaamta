<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Data_Stores;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class Settings {

	private $stores_by_types = null;

	public function __construct() {
		add_action( 'jet-engine/data-stores/settings/custom-controls', array( $this, 'register_settings' ) );
		add_filter( 'jet-engine/data-stores/settings/args-to-save', array( $this, 'save_settings' ), 10, 2 );
		add_filter( 'jet-engine/custom-content-types/service-columns', array( $this, 'register_count_field' ), 10, 2 );
	}

	public function get_count_field_name( $store ) {
		$slug = str_replace( array( ' ', '-' ), '_', $store->get_slug() );
		return $slug . '_count';
	}

	public function get_stores_for_type( $type = null ) {

		if ( null === $this->stores_by_types ) {

			$stores_by_types = array();
			$data_stores = jet_engine()->modules->get_module( 'data-stores' );

			if ( $data_stores && $data_stores->instance && $data_stores->instance->stores ) {

				foreach ( $data_stores->instance->stores->get_stores() as $store ) {

					$related_cct = $store->get_arg( 'related_cct' );
					$is_cct      = $store->get_arg( 'is_cct' );

					if ( ! $is_cct || ! $related_cct ) {
						continue;
					}

					if ( empty( $stores_by_types[ $related_cct ] ) ) {
						$stores_by_types[ $related_cct ] = array();
					}

					$stores_by_types[ $related_cct ][] = $store;

				}
			}

			$this->stores_by_types = $stores_by_types;

		}

		if ( ! $type ) {
			return $this->stores_by_types;
		} else {
			return isset( $this->stores_by_types[ $type ] ) ? $this->stores_by_types[ $type ] : array();
		}

	}

	/**
	 * Regsiter data store count field for custom content type
	 *
	 * @return [type] [description]
	 */
	public function register_count_field( $fields = array(), $args = array() ) {

		if ( empty( $args['slug'] ) ) {
			return $fields;
		}

		$data_stores = $this->get_stores_for_type( $args['slug'] );

		if ( ! empty( $data_stores ) ) {
			foreach ( $data_stores as $store ) {

				if ( ! $store->can_count_posts() ) {
					continue;
				}

				$fields[] = array(
					'title'       => $store->get_name() . ' ' . __( 'count', 'jet-engine' ),
					'name'        => $this->get_count_field_name( $store ),
					'object_type' => 'service_field',
					'type'        => 'number',
				);

			}
		}

		return $fields;

	}

	public function save_settings( $args, $item ) {

		$args['is_cct'] = ! empty( $item['is_cct'] ) ? filter_var( $item['is_cct'], FILTER_VALIDATE_BOOLEAN ) : false;
		$args['related_cct'] = ! empty( $item['related_cct'] ) ? $item['related_cct'] : '';

		return $args;
	}

	public function register_settings() {

		$types_options = array(
			array(
				'value' => '',
				'label' => __( 'Select content type', 'jet-engine' ),
			),
		);

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {
			$types_options[] = array(
				'value' => $type,
				'label' => $instance->get_arg( 'name' ),
			);
		}

		?>
		<cx-vui-switcher
			label="<?php _e( 'Is Custom Content Type store', 'jet-engine' ); ?>"
			description="<?php _e( 'Is store for custom content type items', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:value="dataStores[ index ].is_cct"
			@input="setProp( index, 'is_cct', $event )"
		></cx-vui-switcher>
		<cx-vui-select
			label="<?php _e( 'Content type', 'jet-engine' ); ?>"
			description="<?php _e( 'Select content type for current store', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:size="'fullwidth'"
			:options-list="<?php echo htmlspecialchars( json_encode( $types_options ) ) ?>"
			:value="dataStores[ index ].related_cct"
			@input="setProp( index, 'related_cct', $event )"
			:conditions="[
				{
					'input':   dataStores[ index ].is_cct,
					'compare': 'equal',
					'value':   true,
				}
			]"
		></cx-vui-select>
		<cx-vui-component-wrapper
			:conditions="[
				{
					'input':   dataStores[ index ].is_cct,
					'compare': 'equal',
					'value':   true,
				},
			]"
		>
			<div class="cx-vui-component__meta">
				<label class="cx-vui-component__label"><?php
					_e( 'Note:', 'jet-engine' );
				?></label>
				<div class="cx-vui-component__desc"><?php
					printf(
						__( 'After Data Stores setting save, you need to go to <a target="_blank" href="%s">Content Type settings</a> and click Save button to update Content Type DB table', 'jet-engine' ),
						add_query_arg( array( 'page' => 'jet-engine-cct' ), admin_url( 'admin.php' ) )
					);
				?></div>
			</div>
		</cx-vui-component-wrapper>
		<?php
	}

}
