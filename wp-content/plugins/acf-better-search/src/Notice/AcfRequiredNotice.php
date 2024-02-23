<?php

namespace AcfBetterSearch\Notice;

/**
 * {@inheritdoc}
 */
class AcfRequiredNotice extends NoticeAbstract implements NoticeInterface {

	const NOTICE_OPTION    = 'acfbs_notice_acf_required';
	const NOTICE_VIEW_PATH = 'components/notices/acf.php';

	/**
	 * {@inheritdoc}
	 */
	public function get_option_name(): string {
		return self::NOTICE_OPTION;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_active(): bool {
		return ( ! function_exists( 'acf_get_setting' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_disable_value(): string {
		return 'yes';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_output_path(): string {
		return self::NOTICE_VIEW_PATH;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return mixed[]
	 */
	public function get_vars_for_view(): array {
		return [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_ajax_action_to_disable(): string {
		return self::NOTICE_OPTION;
	}
}
