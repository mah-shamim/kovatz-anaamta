<?php
namespace Jet_Engine\Modules\Dynamic_Visibility;

class Condition_Checker {

	/**
	 * Check render conditions
	 *
	 * @param  [type] $result  [description]
	 * @param  [type] $element [description]
	 * @return [type]          [description]
	 */
	public function check_cond( $settings = array(), $dynamic_settings = array() ) {

		$is_enabled = ! empty( $settings['jedv_enabled'] ) ? $settings['jedv_enabled'] : false;
		$is_enabled = filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_enabled ) {
			return true;
		}

		$conditions     = $dynamic_settings['jedv_conditions'];
		$relation       = ! empty( $settings['jedv_relation'] ) ? $settings['jedv_relation'] : 'AND';
		$is_or_relation = 'OR' === $relation;
		$type           = ! empty( $settings['jedv_type'] ) ? $settings['jedv_type'] : 'show';
		$has_conditions = false;
		$result         = true;

		foreach ( $conditions as $index => $condition ) {

			$args = array(
				'type'      => $type,
				'condition' => null,
				'user_role' => null,
				'user_id'   => null,
				'field'     => null,
				'value'     => null,
				'data_type' => null,
				'context'   => null,
			);

			foreach ( $args as $arg => $default ) {
				$key = 'jedv_' . $arg;
				$args[ $arg ] = ! \Jet_Engine_Tools::is_empty( $condition, $key ) ? $condition[ $key ] : $default;
			}

			// Apply macros in value
			if ( null !== $args['value'] ) {
				$args['value'] = jet_engine()->listings->macros->do_macros( $args['value'] );
			}

			$is_dynamic_field = isset( $condition['__dynamic__']['jedv_field'] );
			$is_empty_field   = empty( $condition['jedv_field'] );

			$args['field_raw'] = ( ! $is_dynamic_field && ! $is_empty_field ) ? $condition['jedv_field'] : null;

			if ( empty( $args['condition'] ) ) {
				continue;
			}

			$condition_id       = $args['condition'];
			$condition_instance = Module::instance()->conditions->get_condition( $condition_id );

			if ( ! $condition_instance ) {
				continue;
			}

			if ( ! $has_conditions ) {
				$has_conditions = true;
			}

			$custom_value_key = 'value_' . $condition_instance->get_id();
			$custom_value = ! empty( $condition[ $custom_value_key ] ) ? $condition[ $custom_value_key ] : false;

			if ( $custom_value ) {
				$args['value'] = $custom_value;
			}

			$args['condition_settings'] = $condition;

			$args = apply_filters( 'jet-engine/modules/dynamic-visibility/condition/args', $args );

			$check = $condition_instance->check( $args );

			if ( 'show' === $type ) {
				if ( $is_or_relation ) {
					if ( $check ) {
						return true;
					}
				} elseif ( ! $check ) {
					return false;
				}
			} else {
				if ( $is_or_relation ) {
					if ( ! $check ) {
						return false;
					}
				} elseif ( $check ) {
					return true;
				}
			}
		}

		if ( ! $has_conditions ) {
			return true;
		}

		$result = ( 'show' === $type ) ? ! $is_or_relation : $is_or_relation;

		return $result;
	}

}
