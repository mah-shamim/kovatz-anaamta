<?php

/**
 * Payment class.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
abstract class WPForms_Payment {

	/**
	 * Payment addon version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Payment name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Payment name in slug format.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Load priority.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $priority = 10;

	/**
	 * Payment icon.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $icon;

	/**
	 * Form data and settings.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	public $form_data;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->init();

		// Add to list of available payments.
		add_filter( 'wpforms_payments_available', array( $this, 'register_payment' ), $this->priority, 1 );

		// Fetch and store the current form data when in the builder.
		add_action( 'wpforms_builder_init', array( $this, 'builder_form_data' ) );

		// Output builder sidebar.
		add_action( 'wpforms_payments_panel_sidebar', array( $this, 'builder_sidebar' ), $this->priority );

		// Output builder content.
		add_action( 'wpforms_payments_panel_content', array( $this, 'builder_output' ), $this->priority );
	}

	/**
	 * All systems go. Used by subclasses.
	 *
	 * @since 1.0.0
	 */
	public function init() {
	}

	/**
	 * Add to list of registered payments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payments
	 *
	 * @return array
	 */
	public function register_payment( $payments = array() ) {

		$payments[ $this->slug ] = $this->name;

		return $payments;
	}

	/********************************************************
	 * Builder methods - these methods _build_ the Builder. *
	 ********************************************************/

	/**
	 * Fetch and store the current form data when in the builder.
	 *
	 * @since 1.1.0
	 */
	public function builder_form_data() {

		if ( ! empty( $_GET['form_id'] ) ) {
			$this->form_data = wpforms()->form->get(
				absint( $_GET['form_id'] ),
				array(
					'content_only' => true,
				)
			);
		}
	}

	/**
	 * Display content inside the panel sidebar area.
	 *
	 * @since 1.0.0
	 */
	public function builder_sidebar() {

		$configured = ! empty( $this->form_data['payments'][ $this->slug ]['enable'] ) ? 'configured' : '';

		echo '<a href="#" class="wpforms-panel-sidebar-section icon ' . $configured . ' wpforms-panel-sidebar-section-' . esc_attr( $this->slug ) . '" data-section="' . esc_attr( $this->slug ) . '">';

		echo '<img src="' . esc_url( $this->icon ) . '">';

		echo esc_html( $this->name );

		echo '<i class="fa fa-angle-right wpforms-toggle-arrow"></i>';

		if ( ! empty( $configured ) ) {
			echo '<i class="fa fa-check-circle-o"></i>';
		}

		echo '</a>';
	}

	/**
	 * Wraps the builder content with the required markup.
	 *
	 * @since 1.0.0
	 */
	public function builder_output() {

		?>
		<div class="wpforms-panel-content-section wpforms-panel-content-section-<?php echo esc_attr( $this->slug ); ?>"
			id="<?php echo esc_attr( $this->slug ); ?>-provider">

			<div class="wpforms-panel-content-section-title">

				<?php echo esc_html( $this->name ); ?>

			</div>

			<div class="wpforms-payment-settings wpforms-clear">

				<?php $this->builder_content(); ?>

			</div>

		</div>
		<?php
	}

	/**
	 * Display content inside the panel content area.
	 *
	 * @since 1.0.0
	 */
	public function builder_content() {

	}
}
