<?php

namespace AcfBetterSearch\Notice;

/**
 * Interface for class that supports notice displayed in admin panel.
 */
interface NoticeInterface {

	/**
	 * Returns name for option that specifies whether to display notice.
	 *
	 * @return string
	 */
	public function get_option_name(): string;

	/**
	 * Returns default value for option that specifies whether to display notice.
	 *
	 * @return string|null
	 */
	public static function get_default_value();

	/**
	 * Returns status if notice is available.
	 *
	 * @return bool
	 */
	public function is_available(): bool;

	/**
	 * Returns status if notice is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool;

	/**
	 * Returns value of option meaning to hide notice.
	 *
	 * @return string
	 */
	public function get_disable_value(): string;

	/**
	 * Returns server path for view template.
	 *
	 * @return string
	 */
	public function get_output_path(): string;

	/**
	 * Returns variables with values using in view template.
	 *
	 * @return mixed[]|null
	 */
	public function get_vars_for_view();

	/**
	 * Returns name of action using in WP Ajax.
	 *
	 * @return string|null
	 */
	public function get_ajax_action_to_disable();
}
