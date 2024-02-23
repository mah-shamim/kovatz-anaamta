<?php

namespace AcfBetterSearch\Notice;

/**
 * Abstract class for class that supports data field in plugin settings.
 */
abstract class NoticeAbstract implements NoticeInterface {

	/**
	 * {@inheritdoc}
	 */
	public static function get_default_value() {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_ajax_action_to_disable() {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_vars_for_view() {
		return [];
	}
}
