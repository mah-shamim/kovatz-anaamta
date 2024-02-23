<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

class Export {

	public $hook = 'jet_engine_cct_do_export';
	public $skip = array( 'cct_status' );

	public function __construct() {
		add_action( 'wp_ajax_' . $this->hook, array( $this, 'do_export' ) );
	}

	public function do_export() {

		$req = $_POST;

		if ( empty( $req['_wpnonce'] ) || ! wp_verify_nonce( $req['_wpnonce'], $this->hook ) ) {
			wp_die( 'The link is expired, please try again' );
		}

		$content_type = ! empty( $req['content_type'] ) ? $req['content_type'] : false;

		if ( ! $content_type ) {
			wp_die( 'Content type not found in the request' );
		}

		$content_type = Module::instance()->manager->get_content_types( $content_type );

		if ( ! $content_type ) {
			wp_die( 'Content type is not registered' );
		}

		if ( ! $content_type->user_has_access() ) {
			wp_die( 'Access denied' );
		}

		$query  = ! empty( $req['query_data'] ) ? json_decode( wp_unslash( $req['query_data'] ), true ) : array();
		$offset = isset( $query['offset'] ) ? absint( $query['offset'] ) : 0;
		$order  = isset( $query['order'] ) ? $query['order'] : array();
		$args   = isset( $query['args'] ) ? $query['args'] : array();
		$status = isset( $query['status'] ) ? $query['status'] : '';

		if ( $status ) {
			$args[] = array(
				'field'    => 'cct_status',
				'operator' => '=',
				'value'    => $status,
			);
		}

		$query_args = $content_type->prepare_query_args( $args );
		$items      = $content_type->db->query( $query_args, 0, $offset, $order );

		$this->send_items( $items, $content_type );

	}

	public function send_items( $items = array(), $content_type = null ) {

		$filename = 'export-' . $content_type->get_arg( 'slug' ) . '-' . date( 'd-m-Y' ) . '.csv';
		$fields   = $content_type->get_formatted_fields();
		$headers  = array();

		foreach ( $fields as $name => $field ) {
			if ( ! in_array( $name, $this->skip ) ) {
				$headers[] = $name;
			}
		}

		$separator = apply_filters( 'jet-engine/custom-content-types/export/cvs-separator', ',' );

		$file = implode( $separator, $headers ) . PHP_EOL;

		foreach ( $items as $item ) {

			$preapred_item = array();

			foreach ( $headers as $key ) {
				$value = isset( $item[ $key ] ) ? $item[ $key ] : '';

				if ( $value ) {
					$value = $content_type->format_value_by_type( $key, $value );

					// Escaping a double quote.
					$value = str_replace( '"', '""', $value );

				} elseif ( is_array( $value ) && empty( $value ) ) {
					$value = null;
				}

				$value = apply_filters( 'jet-engine/custom-content-types/export/value', $value, $key, $content_type, $item );

				$preapred_item[] = $value;
			}

			$file .= '"' . implode( '"' . $separator . '"', $preapred_item ) . '"' . PHP_EOL;
		}

		\Jet_Engine_Tools::file_download( $filename, $file, 'text/csv' );

	}

	public function ui( $slug = false ) {

		if ( ! $slug ) {
			return;
		}

		Module::instance()->query_dialog()->assets();
		$ajaxurl = admin_url( 'admin-ajax.php' );

		?>
		<form id="jet_cct_export_form" action="<?php echo $ajaxurl; ?>" method="POST">
			<input type="hidden" name="action" value="<?php echo $this->hook; ?>">
			<input type="hidden" name="content_type" value="<?php echo $slug; ?>">
			<input type="hidden" name="query_data" value="" id="jet_cct_query_data">
			<?php wp_nonce_field( $this->hook ); ?>
			<button type="button" class="page-title-action" id="jet_cct_export" data-fetch-path="<?php echo Module::instance()->query_dialog()->api_path(); ?>" data-type="<?php echo $slug; ?>"><?php
				_e( 'Export items to CSV', 'jet-engine' );
			?></button>
		</form>
		<?php

		ob_start();
		?>

		var $jetExpTrigger = document.getElementById( 'jet_cct_export' );
		var $jetExpResult  = document.getElementById( 'jet_cct_query_data' );

		new JetQueryDialog({
			trigger: $jetExpTrigger,
			resultTarget: $jetExpResult,
			contentType: $jetExpTrigger.dataset.type,
			fetchPath: $jetExpTrigger.dataset.fetchPath,
			hasOffset: false,
			onSend: function( value, inputEvent ) {
				var $form = document.getElementById( 'jet_cct_export_form' );
				$form.submit();
			}
		});
		<?php

		$init_ui = ob_get_clean();

		wp_add_inline_script( 'jet-engine-cct-query-dialog', $init_ui );

	}

}
