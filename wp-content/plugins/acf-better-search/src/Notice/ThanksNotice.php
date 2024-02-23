<?php

namespace AcfBetterSearch\Notice;

/**
 * {@inheritdoc}
 */
class ThanksNotice extends NoticeAbstract implements NoticeInterface {

	const NOTICE_OPTION    = 'acfbs_notice_hidden';
	const NOTICE_VIEW_PATH = 'components/notices/thanks.php';

	/**
	 * {@inheritdoc}
	 */
	public function get_option_name(): string {
		return self::NOTICE_OPTION;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_default_value(): string {
		return (string) strtotime( '+ 2 weeks' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool {
		return ( basename( $_SERVER['PHP_SELF'] ) === 'index.php' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_active(): bool {
		$option_value = get_option( self::NOTICE_OPTION, 0 );
		return ( $option_value < time() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_disable_value(): string {
		$is_permanent = ( isset( $_REQUEST['is_permanently'] ) && $_REQUEST['is_permanently'] ); // phpcs:ignore
		return (string) strtotime( ( $is_permanent ) ? '+10 years' : '+ 1 month' );
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
			'ajax_url'     => admin_url( 'admin-ajax.php' ),
			'close_action' => self::NOTICE_OPTION,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_ajax_action_to_disable(): string {
		return self::NOTICE_OPTION;
	}
}
