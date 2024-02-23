<?php
namespace Jet_Engine\Query_Builder;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define query types class
 */
class Query_Editor {

	private $_types = array();

	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_query_types' ) );
		add_action( 'jet-engine/rest-api/init-endpoints', array( $this, 'register_query_types' ) );

		add_action( 'jet-engine/query-builder/editor/before-enqueue-scripts', array( $this, 'enqueue_editor_components' ) );
	}

	/**
	 * Register allowed query types instatnces
	 *
	 * @return [type] [description]
	 */
	public function register_query_types() {

		require_once Manager::instance()->component_path( 'editor/base.php' );
		require_once Manager::instance()->component_path( 'editor/posts.php' );
		require_once Manager::instance()->component_path( 'editor/terms.php' );
		require_once Manager::instance()->component_path( 'editor/users.php' );
		require_once Manager::instance()->component_path( 'editor/comments.php' );
		require_once Manager::instance()->component_path( 'editor/sql.php' );
		require_once Manager::instance()->component_path( 'editor/repeater.php' );
		require_once Manager::instance()->component_path( 'editor/current-wp-query.php' );

		$this->register_type( new Query_Editor\Posts_Query() );
		$this->register_type( new Query_Editor\Terms_Query() );
		$this->register_type( new Query_Editor\Users_Query() );
		$this->register_type( new Query_Editor\Comments_Query() );
		$this->register_type( new Query_Editor\SQL_Query() );
		$this->register_type( new Query_Editor\Repeater_Query() );
		$this->register_type( new Query_Editor\Current_WP_Query() );

		do_action( 'jet-engine/query-builder/query-editor/register', $this );

	}

	/**
	 * Register new type by its instance
	 * @param  [type] $type_instance [description]
	 * @return [type]                [description]
	 */
	public function register_type( $type_instance ) {
		$this->_types[ $type_instance->get_id() ] = $type_instance;
	}

	/**
	 * Returns editor components types
	 *
	 * @return [type] [description]
	 */
	public function get_editor_components_map() {

		$res = array();

		foreach ( $this->get_types() as $type ) {
			if ( $type->editor_component_name() ) {
				$res[ $type->get_id() ] = $type->editor_component_name();
			}
		}

		return $res;

	}

	/**
	 * Returns registered types list
	 *
	 * @return [type] [description]
	 */
	public function get_types() {
		return $this->_types;
	}

	public function enqueue_editor_components() {

		$has_templates = false;

		foreach ( $this->get_types() as $type ) {

			if ( $type->editor_component_file() ) {

				$data        = $type->editor_component_data();
				$handle      = 'jet-query-component-' . $type->get_id();
				$object_name = str_replace( '-', '_', $handle );

				wp_enqueue_script(
					$handle,
					$type->editor_component_file(),
					array(),
					jet_engine()->get_version(),
					true
				);

				if ( ! empty( $data ) ) {
					wp_localize_script( $handle, $object_name, $data );
				}

			}

			if ( $type->editor_component_name() && $type->editor_component_template() ) {
				$has_templates = true;
			}

		}

		if ( $has_templates ) {
			add_action( 'admin_footer', array( $this, 'print_editor_templates' ) );
		}

	}

	public function print_editor_templates() {
		foreach ( $this->get_types() as $type ) {
			if ( $type->editor_component_name() && $type->editor_component_template() ) {
				printf( '<script type="text/x-template" id="%1$s">%2$s</script>', $type->editor_component_name(), $type->editor_component_template() );
			}
		}
	}

	public function get_types_for_js() {

		$res = array(
			array(
				'value' => '',
				'label' => __( 'Select query type...', 'jet-engine' ),
			)
		);

		foreach ( $this->get_types() as $type_id => $type ) {
			$res[] = array(
				'value' => $type_id,
				'label' => $type->get_name(),
			);
		}

		return $res;

	}

}
