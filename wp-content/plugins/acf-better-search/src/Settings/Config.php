<?php

namespace AcfBetterSearch\Settings;

use AcfBetterSearch\HookableInterface;

/**
 * .
 */
class Config implements HookableInterface {

	/**
	 * @var Options
	 */
	private $options;

	/**
	 * @var mixed[]
	 */
	private $config;

	public function __construct( Options $options = null ) {
		$this->options = $options ?: new Options();
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'acfbs_config', [ $this, 'get_config' ], 10, 2 );
	}

	/**
	 * @param mixed[] $value    .
	 * @param bool    $is_force .
	 *
	 * @return mixed[]
	 */
	public function get_config( array $value, bool $is_force = false ): array {
		if ( $this->config && ! $is_force ) {
			return $this->config;
		}

		$types  = get_option( 'acfbs_fields_types', [ 'text', 'textarea', 'wysiwyg' ] );
		$config = array_merge(
			[
				Options::OPTION_FIELDS_TYPES => $types ?: [],
			],
			$this->get_features_config()
		);

		$this->config = $config;
		return $config;
	}

	/**
	 * @return mixed[]
	 */
	private function get_features_config(): array {
		$features = array_merge(
			$this->options->get_features_default_settings(),
			$this->options->get_features_advanced_settings()
		);

		$list = [];
		foreach ( $features as $key => $label ) {
			$value        = get_option( sprintf( 'acfbs_%s', $key ), false ) ? true : false;
			$list[ $key ] = $value;
		}
		return $list;
	}
}
