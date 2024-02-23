<?php
/**
 * Jet Dashboard Module
 *
 * Version: 2.0.9
 */
namespace Jet_Knowledge_Base_Search;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Module {

	/**
	 * API url.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var srting.
	 */
	private $api_url = 'https://crocoblock.com/wp-json/knowledge-base-search/v1/search/';
	
	/**
	 * Enabled/disabled trigger
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var srting.
	 */
	private $enabled = false;

	/**
	 * Module directory path.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var srting.
	 */
	private $path;

	/**
	 * Module directory URL.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var srting.
	 */
	private $url;

	/**
	 * Nonce name to secure ajax requests
	 * @var string
	 */
	private $nonce_name = 'jet-knowledge-base-search';

	public function __construct( $args = array() ) {

		$this->path = ! empty( $args['path'] ) ? $args['path'] : false;
		$this->url  = ! empty( $args['url'] ) ? $args['url'] : false;

		if ( ! $this->path || ! $this->url ) {
			wp_die(
				'CX_Vue_UI not initialized. Module URL and Path should be passed into constructor',
				'CX_Vue_UI Error'
			);
		}

		add_action( 'wp_ajax_jet_knowledge_base_search', array( $this, 'search_handler' ) );
		add_action( 'admin_footer', array( $this, 'init' ) );
	}

	public function enable() {
		$this->enabled = true;
	}

	public function search_handler() {
		
		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! wp_verify_nonce( $nonce, $this->nonce_name ) ) {
			wp_send_json_error( 'You can`t access this link' );
		}

		$query = ! empty( $_REQUEST['query'] ) ? $_REQUEST['query'] : false;

		if ( ! $query ) {
			wp_send_json_success( array() );
		}

		$response = wp_remote_get( add_query_arg( array( 's' => $query ), $this->api_url ) );
		$search_results = wp_remote_retrieve_body( $response );
		$search_results = json_decode( $search_results );

		wp_send_json_success( $search_results );

	}

	public function init() {

		if ( ! $this->enabled ) {
			return;
		}

		$this->assets();
		$this->template();

	}

	public function assets() {
	
		wp_enqueue_script( 'jet-kbs', $this->url . 'assets/js/script.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_style( 'jet-kbs', $this->url . 'assets/css/style.css', array(), '1.0.0' );

		wp_localize_script( 'jet-kbs', 'JetKBSData', array(
			'nonce' => wp_create_nonce( $this->nonce_name ),
		) );

	}

	public function template() {
		?>
		<div class="jet-kbs-box" style="display: none;">
			<div class="jet-kbs-bubble is-hidden">
				<div class="jet-kbs-ask-tooltip is-hidden">
					<?php _e( 'Search in Crocoblock documentation for the answer:', 'jet-engine' ); ?>
				</div>
				<div class="jet-kbs-searching is-hidden"><?php _e( 'Searching...', 'jet-engine' ); ?></div>
				<div class="jet-kbs-search-scroll">
					<div class="jet-kbs-search-results is-hidden"></div>
					<div class="jet-kbs-no-results is-hidden">
						<?php _e( 'No results found for your request...', 'jet-engine' ); ?>
					</div>
					<div class="jet-kbs-ask-support is-hidden">
						<div class="jet-kbs-ask-support__tip is-hidden"><?php 
							_e( 'Still not found your answer?', 'jet-engine' ); 
						?></div>
						<div class="jet-kbs-ask-support__link">
							<a href="https://support.crocoblock.com/support/home/" target="_blank">Contact Support</a>
						</div>
					</div>
				</div>
				<div class="jet-kbs-ask-input is-hidden">
					<textarea class="jet-kbs-ask-input__field" row="3" placeholder="<?php _e( 'Here you can describe your problem in a few words...', 'jet-engine' ); ?>"></textarea>
					<button type="button" class="jet-kbs-ask-input__search"><?php _e( 'Search for help', 'jet-engine' ); ?></button>
				</div>
			</div>
			<div class="jet-kbs-trigger">
				<div class="jet-kbs-trigger__close is-hidden"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 10.93 5.719-5.72c.146-.146.339-.219.531-.219.404 0 .75.324.75.749 0 .193-.073.385-.219.532l-5.72 5.719 5.719 5.719c.147.147.22.339.22.531 0 .427-.349.75-.75.75-.192 0-.385-.073-.531-.219l-5.719-5.719-5.719 5.719c-.146.146-.339.219-.531.219-.401 0-.75-.323-.75-.75 0-.192.073-.384.22-.531l5.719-5.719-5.72-5.719c-.146-.147-.219-.339-.219-.532 0-.425.346-.749.75-.749.192 0 .385.073.531.219z" fill="currentColor"/></svg></div>
				<div class="jet-kbs-trigger__open">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm8.975 16.383l-2.607-1.485c.402-.884.632-1.863.632-2.898s-.23-2.014-.633-2.897l2.607-1.485c.651 1.325 1.026 2.809 1.026 4.382s-.375 3.057-1.025 4.383zm-18.975-4.383c0-1.573.375-3.057 1.025-4.383l2.607 1.485c-.402.884-.632 1.863-.632 2.898s.23 2.014.633 2.897l-2.607 1.485c-.651-1.325-1.026-2.809-1.026-4.382zm5 0c0-2.757 2.243-5 5-5s5 2.243 5 5-2.243 5-5 5-5-2.243-5-5zm9.396-8.968l-1.484 2.608c-.888-.407-1.872-.64-2.912-.64s-2.024.233-2.912.64l-1.484-2.608c1.328-.654 2.817-1.032 4.396-1.032s3.068.378 4.396 1.032zm-8.792 17.936l1.484-2.608c.888.407 1.872.64 2.912.64s2.024-.233 2.912-.64l1.484 2.608c-1.328.654-2.817 1.032-4.396 1.032s-3.068-.378-4.396-1.032zm2.824-8.418c0 .4-.294.872-1.121.872-.26 0-.574-.058-.807-.188l.144-.584c.219.116.451.188.704.188.239 0 .376-.089.376-.246 0-.137-.103-.222-.417-.328-.513-.181-.766-.465-.766-.837 0-.496.431-.844 1.053-.844.273 0 .506.048.704.14l.034.014-.164.574c-.178-.092-.355-.147-.581-.147-.239 0-.342.106-.342.212 0 .137.116.205.465.335.492.18.718.446.718.839zm1.586-1.975c-.786 0-1.34.591-1.34 1.442 0 .844.52 1.408 1.299 1.408.827 0 1.36-.571 1.36-1.453 0-.837-.526-1.397-1.319-1.397zm-.007 2.283c-.369 0-.608-.335-.608-.851 0-.526.239-.865.608-.865.41 0 .595.427.595.851 0 .527-.233.865-.595.865zm3.493-.308c0 .4-.294.872-1.121.872-.26 0-.574-.058-.807-.188l.144-.584c.219.116.451.188.704.188.239 0 .376-.089.376-.246 0-.137-.103-.222-.417-.328-.506-.181-.766-.465-.766-.837 0-.496.431-.844 1.053-.844.273 0 .506.048.704.14l.034.014-.164.573c-.178-.092-.355-.147-.581-.147-.239 0-.342.106-.342.212 0 .137.116.205.465.335.492.181.718.447.718.84z" fill="currentColor"/></svg>
					<span><?php _e( 'Got Stuck? Click Here', 'jet-engine' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

}
