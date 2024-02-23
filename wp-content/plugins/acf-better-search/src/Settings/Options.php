<?php

namespace AcfBetterSearch\Settings;

/**
 * .
 */
class Options {

	const OPTION_FIELDS_TYPES  = 'fields_types';
	const OPTION_WHOLE_PHRASES = 'whole_phrases';
	const OPTION_WHOLE_WORDS   = 'whole_words';
	const OPTION_REGEX_SPENCER = 'regex_spencer';
	const OPTION_MODE_LITE     = 'lite_mode';
	const OPTION_MODE_SELECTED = 'selected_mode';

	/**
	 * @return string[]
	 */
	public function get_fields_settings(): array {
		$field_types = [
			'text'     => __( 'Text', 'acf-better-search' ),
			'textarea' => __( 'Text Area', 'acf-better-search' ),
			'number'   => __( 'Number', 'acf-better-search' ),
			'email'    => __( 'Email', 'acf-better-search' ),
			'url'      => __( 'Url', 'acf-better-search' ),
			'file'     => sprintf(
			/* translators: %1$s: open em tag, %2$s: close em tag */
				__( 'File %1$s(it does not work in "Lite Mode")%2$s', 'acf-better-search' ),
				'<em>',
				'</em>'
			),
			'wysiwyg'  => __( 'Wysiwyg Editor', 'acf-better-search' ),
			'select'   => __( 'Select', 'acf-better-search' ),
			'checkbox' => __( 'Checkbox', 'acf-better-search' ),
			'radio'    => __( 'Radio Button', 'acf-better-search' ),
		];

		if ( is_plugin_active( 'advanced-custom-fields-table-field/acf-table.php' ) ) {
			$field_types['table'] = __( 'Table', 'acf-better-search' );
		}

		return apply_filters( 'acfbs_field_types', $field_types );
	}

	/**
	 * @param mixed[] $config .
	 *
	 * @return mixed[]
	 */
	public function get_features_default_settings( array $config = [] ): array {
		return [
			self::OPTION_WHOLE_PHRASES => [
				'label'     => __( 'Search for whole phrases instead of each single word of phrase', 'acf-better-search' ),
				'is_active' => true,
			],
			self::OPTION_WHOLE_WORDS   => [
				'label'     => sprintf(
				/* translators: %1$s: open em tag, %2$s: close em tag */
					__( 'Search for whole words instead of fragments within longer words %1$s(slower search)%2$s', 'acf-better-search' ),
					'<em>',
					'</em>'
				),
				'is_active' => true,
			],
		];
	}

	/**
	 * @param mixed[] $config .
	 *
	 * @return mixed[]
	 */
	public function get_features_advanced_settings( array $config = [] ): array {
		return [
			self::OPTION_MODE_LITE     => [
				'label'     => sprintf(
				/* translators: %1$s: open strong tag, %2$s: close strong tag, %3$s: open em tag, %4$s: close em tag */
					__( 'Use %1$s"Lite Mode"%2$s - does not check field types %3$s(faster search, but less accurate)%4$s', 'acf-better-search' ),
					'<strong>',
					'</strong>',
					'<em>',
					'</em>'
				),
				'is_active' => true,
			],
			self::OPTION_MODE_SELECTED => [
				'label'     => sprintf(
				/* translators: %1$s: open strong tag, %2$s: close strong tag, %3$s: open em tag, %4$s: close em tag */
					__( 'Use %1$s"Selected Mode"%2$s - use only selected fields for searching %3$s(edit group of ACF fields and check option for selected fields; it does not work in "Lite Mode")%4$s', 'acf-better-search' ),
					'<strong>',
					'</strong>',
					'<em>',
					'</em>'
				),
				'is_active' => ( ! isset( $config['lite_mode'] ) || ! $config['lite_mode'] ),
			],
			self::OPTION_REGEX_SPENCER => [
				'label'     => sprintf(
				/* translators: %1$s: open em tag, %2$s: close em tag */
					__( 'Use implementation of regular expression by Henry Spencer to search for whole words %1$s(for newer versions of MySQL where default does not work)%2$s', 'acf-better-search' ),
					'<em>',
					'</em>'
				),
				'is_active' => ( isset( $config[ self::OPTION_WHOLE_WORDS ] ) && $config[ self::OPTION_WHOLE_WORDS ] ),
			],
		];
	}
}
