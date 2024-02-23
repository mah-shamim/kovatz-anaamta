<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content;

/**
 * Blocks dynamic content manager
 */
class Manager {

	private $_blocks = array();
	private $_dynamic_data_key = 'jetEngineDynamicData';

	public $data;

	public function __construct() {

		add_action( 'init', array( $this, 'init_dynamic_blocks' ), 99 );
		add_filter( 'jet-engine/blocks-views/editor-data', array( $this, 'editor_data' ) );
		add_filter( 'render_block', array( $this, 'update_block_content' ), 10, 2 );

	}

	public function init_dynamic_blocks() {

		require jet_engine()->blocks_views->component_path( 'dynamic-content/blocks/base.php' );
		require jet_engine()->blocks_views->component_path( 'dynamic-content/blocks/button.php' );
		require jet_engine()->blocks_views->component_path( 'dynamic-content/blocks/paragraph.php' );
		require jet_engine()->blocks_views->component_path( 'dynamic-content/blocks/heading.php' );
		require jet_engine()->blocks_views->component_path( 'dynamic-content/blocks/cover.php' );
		require jet_engine()->blocks_views->component_path( 'dynamic-content/blocks/container.php' );

		$this->register_block( new Blocks\Button() );
		$this->register_block( new Blocks\Paragraph() );
		$this->register_block( new Blocks\Heading() );
		$this->register_block( new Blocks\Cover() );
		$this->register_block( new Blocks\Container() );

		do_action( 'jet-engine/blocks-views/dynamic-content/init-blocks', $this );

		require jet_engine()->blocks_views->component_path( 'dynamic-content/data.php' );
		$this->data = new Data();

	}

	/**
	 * Update block data
	 *
	 * @param  [type] $prased_block [description]
	 * @param  [type] $source_block [description]
	 * @return [type]               [description]
	 */
	public function update_block_content( $block_content, $block_data ) {

		// Don't modify block content in the admin area except admin-ajax calls
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $block_content;
		}

		$block_name    = $block_data['blockName'];
		$dynamic_block = $this->get_block( $block_name );

		if ( ! $dynamic_block || ! $this->has_dynamic_data( $block_data ) ) {
			return $block_content;
		} else {

			if ( ! class_exists( __NAMESPACE__ . '\Dynamic_Block_Parser' ) ) {
				require jet_engine()->blocks_views->component_path( 'dynamic-content/block-parser.php' );
			}

			$parser = new Dynamic_Block_Parser(
				$dynamic_block, 
				$block_data['attrs'][ $this->_dynamic_data_key ],
				$this->data
			);

			$dynamic_block->set_parser( $parser );

			return $parser->apply_dynamic_data( $block_content, $block_data['attrs'] );
		}
	}

	/**
	 * Chak if given block has dynamic data to replace
	 *
	 * @return boolean [description]
	 */
	public function has_dynamic_data( $block_data = array() ) {

		if ( empty( $block_data['attrs'] ) || empty( $block_data['attrs'][ $this->_dynamic_data_key ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Register dynamic content support for selected block
	 *
	 * @param  [type] $instance [description]
	 * @return [type]           [description]
	 */
	public function register_block( $instance ) {
		$this->_blocks[ $instance->block_name() ] = $instance;
	}

	public function get_blocks() {
		return $this->_blocks;
	}

	public function get_block( $block_name ) {
		return isset( $this->_blocks[ $block_name ] ) ? $this->_blocks[ $block_name ] : false;
	}

	/**
	 * Register editor data
	 *
	 * @return [type] [description]
	 */
	public function editor_data( $data ) {

		$dynamic_data = array();

		foreach ( $this->get_blocks() as $block ) {
			$dynamic_data[ $block->block_name() ] = $block->get_block_atts();
		}

		$dynamic_data_sources = apply_filters( 'jet-engine/blocks-views/dynamic-content/data-sources', array(
			array(
				'value' => '',
				'label' => __( 'Select...', 'jet-engine' ),
			),
			array(
				'value' => 'object',
				'label' => __( 'Current object property', 'jet-engine' ),
			),
			array(
				'value' => 'custom',
				'label' => __( 'Custom data', 'jet-engine' ),
			),
		) );

		$data['dynamicKey']         = $this->_dynamic_data_key;
		$data['dynamicData']        = $dynamic_data;
		$data['dynamicDataSources'] = $dynamic_data_sources;
		$data['allowedContextList'] = jet_engine()->listings->allowed_context_list( 'blocks' );
		$data['macrosList']         = jet_engine()->listings->macros->get_all( true, true );

		return $data;
	}

}
