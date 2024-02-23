<?php
/**
 * StoreApps In app offer
 *
 * @category    Class
 * @author      StoreApps
 * @package     StoreApps
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

/**
 * Class for handling in app offer for StoreApps
 */
class SA_TLWP_In_App_Offer {

	/**
	 * Variable to hold instance of this class
	 *
	 * @var $instance
	 */
	private static $instance = null;

	/**
	 * The plugin file
	 *
	 * @var string $plugin_file
	 */
	public $plugin_file = '';

	/**
	 * The plugin url
	 *
	 * @var string $plugin_file
	 */
	public $plugin_url = '';

	/**
	 * The prefix
	 *
	 * @var string $prefix
	 */
	public $prefix = '';

	/**
	 * The option name
	 *
	 * @var string $option_name
	 */
	public $option_name = '';

	/**
	 * The campaign
	 *
	 * @var string $campaign
	 */
	public $campaign = '';

	/**
	 * The start
	 *
	 * @var string $start
	 */
	public $start = '';

	/**
	 * The end
	 *
	 * @var string $end
	 */
	public $end = '';

	/**
	 * Is plugin page
	 *
	 * @var bool $end
	 */
	public $is_plugin_page = false;

	/**
	 * Constructor
	 *
	 * @param array $args Configuration.
	 */
	public function __construct( $args ) {

		$this->plugin_file    = ( ! empty( $args['file'] ) ) ? $args['file'] : '';
		$this->prefix         = ( ! empty( $args['prefix'] ) ) ? $args['prefix'] : '';
		$this->option_name    = ( ! empty( $args['option_name'] ) ) ? $args['option_name'] : '';
		$this->campaign       = ( ! empty( $args['campaign'] ) ) ? $args['campaign'] : '';
		$this->start          = ( ! empty( $args['start'] ) ) ? $args['start'] : '';
		$this->end            = ( ! empty( $args['end'] ) ) ? $args['end'] : '';
		$this->is_plugin_page = ( ! empty( $args['is_plugin_page'] ) ) ? $args['is_plugin_page'] : false;

		add_action( 'admin_footer', array( $this, 'admin_styles_and_scripts' ) );
		add_action( 'admin_notices', array( $this, 'in_app_offer' ) );
		add_action( 'wp_ajax_' . $this->prefix . '_dismiss_action', array( $this, 'dismiss_action' ) );

	}

	/**
	 * Get single instance of this class
	 *
	 * @param array $args Configuration.
	 * @return Singleton object of this class
	 */
	public static function get_instance( $args ) {
		// Check if instance is already exists.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $args );
		}

		return self::$instance;
	}

	/**
	 * Whether to show or not
	 *
	 * @return boolean
	 */
	public function is_show() {

		$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format', 'temporary-login-without-password' );
		$current_date    = strtotime( date_i18n( $timezone_format ) );
		$start           = strtotime( $this->start );
		$end             = strtotime( $this->end );
		if ( ( $current_date >= $start ) && ( $current_date <= $end ) ) {
			$option_value  = get_option( $this->option_name, 'yes' );
			$get_post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

			if ( ( 'product' === $get_post_type || $this->is_plugin_page ) && 'yes' === $option_value ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Admin styles & scripts
	 */
	public function admin_styles_and_scripts() {

		if ( $this->is_show() ) {

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('.sa_offer_container').on('click', '.sa_dismiss a', function(){
						jQuery.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type: 'post',
							dataType: 'json',
							data: {
								action: '<?php echo esc_html( $this->prefix ); ?>_dismiss_action',
								security: '<?php echo esc_html( wp_create_nonce( $this->prefix . '-dismiss-action' ) ); ?>'
							},
							success: function( response ){
								if ( response.success != undefined && response.success != '' && response.success == 'yes' ) {
									jQuery('.sa_offer_container').fadeOut(500, function(){ jQuery('.sa_offer_container').remove(); });
								}
							}
						});
						return false;
					});
				});
			</script>
			<?php

		}

	}

	/**
	 * The offer content
	 */
	public function in_app_offer() {

		if ( $this->is_show() ) {
			?>
			<div class="sa_offer_container"><?php $this->show_offer_content(); ?></div>
			<?php
		}
	}

	/**
	 * The offer content
	 */
	public function show_offer_content() {
		if ( ! wp_script_is( 'jquery' ) ) {
			wp_enqueue_script( 'jquery' );
		}

		?>
		<style type="text/css">
			.sa_offer {
				margin: 1em auto;
				text-align: center;
				font-size: 1.2em;
				line-height: 1em;
				padding: 1em;
			}
			.sa_offer_content img {
				width: 65%;
				margin: 0 auto;
			}
			.sa_dismiss {
				font-size: 0.5em;
				display: inline-block;
				width: 100%;
				text-align: center;
				letter-spacing: 2px;
			}
		</style>
		<div class="sa_offer">
			<div class="sa_offer_content">
				<a href="https://www.icegram.com/?utm_source=in_app&utm_medium=<?php echo esc_attr( $this->prefix ); ?>_banner&utm_campaign=<?php echo esc_attr( $this->campaign ); ?>" target="_blank">
					<img src="<?php echo esc_url( plugins_url( 'sa-includes/images/ig_bfcm2022.png', $this->plugin_file ) ); ?>" />
				</a>
				<div class="sa_dismiss"> <!-- Do not change this class -->
					<a href="javascript:void(0)" style="color: black; text-decoration: none;" title="<?php echo esc_attr__( 'Dismiss', 'temporary-login-without-password' ); ?>"><?php echo esc_html__( 'Hide this', 'temporary-login-without-password' ); ?></a>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(function(){
				jQuery('div.sa_offer').not(':eq(0)').hide();	// To hide offer div if present multiple times.
			});
		</script>
		<?php
	}

	/**
	 * Handle dismiss action
	 */
	public function dismiss_action() {

		check_ajax_referer( $this->prefix . '-dismiss-action', 'security' );

		update_option( $this->option_name, 'no', 'no' );

		wp_send_json( array( 'success' => 'yes' ) );

	}

}
