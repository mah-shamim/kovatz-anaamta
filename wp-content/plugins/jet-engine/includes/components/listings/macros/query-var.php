<?php
namespace Jet_Engine\Macros;

/**
 * Returns queried variable.
 */
class Query_Var extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'query_var';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Query Variable', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'var_name' => array(
				'label'   => __( 'Variable Name', 'jet-engine' ),
				'type'    => 'text',
				'default' => '',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$variable = ! empty( $args['var_name'] ) ? $args['var_name'] : null;

		if ( ! $variable ) {
			return null;
		}

		global $wp_query;

		if ( isset( $wp_query->query_vars[ $variable ] ) ) {
			return $wp_query->query_vars[ $variable ];
		} elseif ( isset( $_REQUEST[ $variable ] ) ) {
			if ( ! is_array( $_REQUEST[ $variable ] ) ) {
				return esc_attr( $_REQUEST[ $variable ] );
			} else {
				return $_REQUEST[ $variable ];
			}
		}

		return null;
	}
}