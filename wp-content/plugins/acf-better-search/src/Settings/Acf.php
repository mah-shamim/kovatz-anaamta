<?php

namespace AcfBetterSearch\Settings;

use AcfBetterSearch\HookableInterface;

/**
 * .
 */
class Acf implements HookableInterface {

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'acf/render_field_settings', [ $this, 'add_field_settings' ] );
	}

	/**
	 * @param mixed[] $field .
	 *
	 * @return void
	 */
	public function add_field_settings( array $field ) {
		$config = apply_filters( 'acfbs_config', [] );
		if ( ! $config[ Options::OPTION_MODE_SELECTED ] || ! in_array( $field['type'], $config[ Options::OPTION_FIELDS_TYPES ] ) ) {
			return;
		}

		acf_render_field_setting(
			$field,
			[
				'label'        => __( 'Allow use for search?', 'acf-better-search' ),
				'instructions' => sprintf(
				/* translators: %1$s: open strong tag, %2$s: close strong tag */
					__( 'Only values from fields with selected this option will be used by %1$sACF: Better Search%2$s plugin.', 'acf-better-search' ),
					'<strong>',
					'</strong>'
				),
				'name'         => 'acfbs_allow_search',
				'type'         => 'true_false',
				'ui'           => 1,
			],
			true
		);
	}
}
