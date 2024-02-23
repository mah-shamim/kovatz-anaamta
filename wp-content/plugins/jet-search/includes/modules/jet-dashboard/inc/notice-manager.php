<?php

namespace Jet_Dashboard;

use Jet_Dashboard\Dashboard as Dashboard;
use Jet_Dashboard\Utils as Utils;

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/**
 * Notice_Manager Class
 */
class Notice_Manager
{

	/**
	 *
	 */
	const EXCLUDE_PAGES = [ 'plugins.php', 'plugin-install.php', 'plugin-editor.php' ];

	/**
	 * [$registered_notices description]
	 * @var array
	 */
	public $registered_notices = array();

	/**
	 * [get_registered_plugins description]
	 * @return [type] [description]
	 */
	/*public function get_registered_notices( $page_slug = false ) {

		var_dump($page_slug);

		if ( ! $page_slug ) {
			return $this->registered_notices;
		}

		$page_notices = array_filter( $this->registered_notices, function( $notice ) {
			return $page_slug === $notice['page'];
		} );

		if ( ! empty( $page_notices ) ) {
			return $page_notices;
		}

		return false;
	}*/

	/**
	 * @return void
	 */
	public function admin_notices() {
		//$this->maybe_show_empty_license();
	}

	/**
	 * @param $page_slug
	 * @return array|false
	 */
	public function get_registered_notices( $page_slug = false ) {

		if ( !$page_slug ) {
			return $this->registered_notices;
		}

		$page_notices = array_filter( $this->registered_notices, function ( $notice ) use ( $page_slug ) {

			if ( is_array( $notice['page'] ) && in_array( $page_slug, $notice['page'] ) ) {
				return true;
			} elseif ( $page_slug === $notice['page'] ) {
				return true;
			}

			return false;
		} );

		if ( !empty( $page_notices ) ) {
			return array_values( $page_notices );
		}

		return false;
	}

	/**
	 * [get_registered_plugins description]
	 * @return [type] [description]
	 */
	public function register_notice( $notice_args = array() ) {

		/*Dashboard::get_instance()->notice_manager->register_notice( array(
			'id'      => 'alert-notice-1',
			'page'    => 'welcome-page',
			'preset'  => 'alert',
			'type'    => 'info',
			'title'   => 'Info',
			'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
			'buttons' => array(
			),
		) );*/

		$defaults = array(
			'id' => false,
			'page' => array(),
			'preset' => 'alert', // alert, notice
			'type' => 'info', // info, success, danger, error
			'typeBgColor' => false, // info, success, danger, error
			'duration' => false,
			'icon' => false,
			'title' => '',
			'message' => '',
			'buttons' => array(),
			'customClass' => '',
		);

		$notice_args = wp_parse_args( $notice_args, $defaults );

		if ( !$notice_args['id'] || !$notice_args['page'] || empty( $notice_args['message'] ) ) {
			return false;
		}

		if ( ! is_array( $notice_args['page'] ) ) {
			$pages[] = $notice_args['page'];
			$notice_args['page'] = $pages;
		}

		$this->registered_notices[] = $notice_args;
	}

	/**
	 * @return void
	 */
	public function maybe_show_empty_license() {
		$license_list = Utils::get_license_list();

		$this->print_admin_notice( [
			'id' => 'jet-empty-license',
			'title' => esc_html__( 'Your site.', 'jet-dashboard' ),
			'description' => esc_html__( 'Your site database needs to be updated to the latest version.', 'jet-dashboard' ),
			'type' => 'info',
			'button' => [
				'text' => esc_html__( 'Update Now', 'jet-dashboard' ),
				'url' => '#',
				'class' => 'e-button e-button--cta',
			],
		] );


	}

	/**
	 * @param array $options
	 * @return void
	 */
	public function print_admin_notice( array $options ) {
		global $pagenow;

		if ( in_array( $pagenow, self::EXCLUDE_PAGES ) ) {
			return;
		}

		$default_options = [
			'id' => null,
			'title' => '',
			'description' => '',
			'classes' => [ 'notice', 'jet-notice' ], // We include WP's default notice class so it will be properly handled by WP's js handler
			'type' => '',
			'dismissible' => true,
			'icon' => '<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 40C31.0457 40 40 31.0457 40 20C40 8.95431 31.0457 0 20 0C8.95431 0 0 8.95431 0 20C0 31.0457 8.95431 40 20 40Z" fill="url(#infoNoticeType)"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M28.269 15.9655C27.1641 17.1814 25.5461 17.22 24.1649 16.0427C21.6392 13.8811 18.2058 13.9969 16.0353 16.3322C14.0424 18.4938 14.1213 21.7555 16.1932 23.8398C18.3045 25.9241 21.6786 26.0785 23.9479 24.1101C24.9541 23.2415 26.0196 22.7398 27.3418 23.3574C29.0979 24.1678 29.532 26.3488 28.1902 27.7191C26.0196 29.9385 23.2769 30.9421 20.9288 31C13.6478 30.9807 8.55692 25.4609 9.03049 19.1114C9.46459 13.5917 14.516 8.51582 21.1853 9.03693C23.7702 9.22992 26.0788 10.2142 27.9534 12.0284C29.2163 13.225 29.3543 14.7882 28.269 15.9655Z" fill="white"></path><defs><linearGradient id="infoNoticeType" x1="36.25" y1="9.375" x2="5.9375" y2="34.0624" gradientUnits="userSpaceOnUse"><stop stop-color="#3DDDC1"></stop><stop offset="1" stop-color="#5099E6"></stop></linearGradient></defs></svg>',
			'button' => [],
			'button_secondary' => [],
		];

		$options = array_replace_recursive( $default_options, $options );

		$notice_classes = $options['classes'];
		$dismiss_button = '';
		$icon = '';

		if ( $options['type'] ) {
			$notice_classes[] = 'jet-notice--' . $options['type'];
		}

		if ( $options['dismissible'] ) {
			$label = esc_html__( 'Dismiss this notice.', 'elementor' );
			$notice_classes[] = 'is-dismissible';
			$notice_classes[] = 'jet-notice--dismissible';
			$dismiss_button = '<i class="jet-notice__dismiss" role="button" aria-label="' . $label . '" tabindex="0"></i>';
		}

		if ( $options['icon'] ) {
			$notice_classes[] = 'jet-notice--extended';
			$icon = '<div class="jet-notice__icon-wrapper">' . $options['icon'] . '</div>';
		}

		$wrapper_attributes = [
			'class' => $notice_classes,
		];

		if ( $options['id'] ) {
			$wrapper_attributes['data-notice_id'] = $options['id'];
		}

    ?><div <?php echo Utils::print_html_attributes( $wrapper_attributes ); ?>>
        <span class="jet-notice__type-line"></span>
		<?php //echo $dismiss_button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="jet-notice__aside">
			<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <div class="jet-notice__content">
			<?php if ( $options['title'] ) { ?>
                <h3 class="jet-notice__title"><?php echo wp_kses_post( $options['title'] ); ?></h3>
			<?php } ?>

			<?php if ( $options['description'] ) { ?>
                <p class="jet-notice__description"><?php echo wp_kses_post( $options['description'] ); ?></p>
			<?php } ?>

			<?php if ( ! empty( $options['button']['text'] ) || ! empty( $options['button_secondary']['text'] ) ) { ?>
                <div class="jet-notice__actions">
					<?php
					foreach ( [ $options['button'], $options['button_secondary'] ] as $index => $button_settings ) {
						if ( empty( $button_settings['variant'] ) && $index ) {
							$button_settings['variant'] = 'outline';
						}

						if ( empty( $button_settings['text'] ) ) {
							continue;
						}

						//$button = new Button( $button_settings );
						$this->print_button( $button_settings );
					} ?>
                </div>
			<?php } ?>
        </div>
        </div><?php
	}

	public function print_button( $options = [] ) {

		$default_options = [
			'classes' => [ 'jet-button' ],
			'icon' => '',
			'new_tab' => false,
			'text' => '',
			'type' => '',
			'url' => '',
			'variant' => '',
			'before' => '',
		];

		$options = array_replace_recursive( $default_options, $options );

		if ( empty( $options['text'] ) ) {
			return;
		}

		$html_tag = ! empty( $options['url'] ) ? 'a' : 'button';
		$before = '';
		$icon = '';
		$attributes = [];

		if ( ! empty( $options['icon'] ) ) {
			$icon = '<i class="' . esc_attr( $options['icon'] ) . '"></i>';
		}

		$classes = $options['classes'];

		if ( ! empty( $options['type'] ) ) {
			$classes[] = 'jet-button--' . $options['type'];
		}

		if ( ! empty( $options['variant'] ) ) {
			$classes[] = 'jet-button--' . $options['variant'];
		}

		if ( ! empty( $options['before'] ) ) {
			$before = '<span>' . wp_kses_post( $options['before'] ) . '</span>';
		}

		if ( ! empty( $options['url'] ) ) {
			$attributes['href'] = $options['url'];
			if ( $options['new_tab'] ) {
				$attributes['target'] = '_blank';
			}
		}

		$attributes['class'] = $classes;

		$html = $before . '<' . $html_tag . ' ' . Utils::print_html_attributes( $attributes ) . '>';
		$html .= $icon;
		$html .= '<span>' . sanitize_text_field( $options['text'] ) . '</span>';
		$html .= '</' . $html_tag . '>';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * @since 2.9.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ], 20 );
	}

}
