<?php
namespace Jet_Engine\Modules\Dynamic_Visibility;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'dynamic-visibility';

	/**
	 * @var Conditions\Manager
	 */
	public $conditions = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init module components
	 *
	 * @return void
	 */
	public function init() {

		require jet_engine()->modules->modules_path( 'dynamic-visibility/inc/conditions/manager.php' );
		require jet_engine()->modules->modules_path( 'dynamic-visibility/inc/conditions-checker.php' );
		require jet_engine()->modules->modules_path( 'dynamic-visibility/inc/elementor-integration.php' );
		require jet_engine()->modules->modules_path( 'dynamic-visibility/inc/blocks-integration.php' );

		new Elementor_Integration();
		new Blocks_Integration();

		$this->conditions = new Conditions\Manager();

	}

	public function get_condition_controls() {

		$data = array();

		global $wp_roles;
		$user_roles = array();

		foreach ( $wp_roles->roles as $role_id => $role ) {
			$user_roles[ $role_id ] = $role['name'];
		}

		$data['jedv_condition'] = array(
			'type'        => 'select',
			'label'       => __( 'Condition', 'jet-engine' ),
			'label_block' => true,
			'groups'      => $this->conditions->get_grouped_conditions_for_options(),
		);

		$data['jedv_user_role'] = array(
			'label'       => __( 'User role', 'jet-engine' ),
			'type'        => 'select2',
			'multiple'    => true,
			'options'     => $user_roles,
			'placeholder' => __( 'Select role...', 'jet-engine' ),
			'label_block' => true,
			'condition'   => array(
				'jedv_condition' => array( 'user-role', 'user-role-not' ),
			),
		);

		$data['jedv_user_id'] = array(
			'label'       => __( 'User IDs', 'jet-engine' ),
			'description' => __( 'Set comma separated IDs list (10, 22, 19 etc.). Note: ID Guest user is 0', 'jet-engine' ),
			'label_block' => true,
			'type'        => 'text',
			'condition'   => array(
				'jedv_condition' => array( 'user-id', 'user-id-not' ),
			),
		);

		$field_categories = array();

		if ( class_exists( '\Jet_Engine_Dynamic_Tags_Module' ) && class_exists( '\Elementor\Modules\DynamicTags\Module' ) ) {
			$field_categories = array(
				\Elementor\Modules\DynamicTags\Module::BASE_GROUP,
				\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
				\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
				\Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY,
				\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
				\Elementor\Modules\DynamicTags\Module::MEDIA_CATEGORY,
				\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY,
				\Elementor\Modules\DynamicTags\Module::NUMBER_CATEGORY,
				\Elementor\Modules\DynamicTags\Module::COLOR_CATEGORY,
				\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
			);
		}

		$data['jedv_field'] = array(
			'label'       => __( 'Field', 'jet-engine' ),
			'description' => __( 'Enter meta field name or select dynamic tag to compare value against. <br><b>Note!</b> If your meta field contains array, for example JetEngine Checkbox field etc, you need to set meta field name manually (not with dynamic capability)', 'jet-engine' ),
			'type'        => 'text',
			'label_block' => true,
			'has_html'    => true,
			'dynamic' => array(
				'active' => true,
				'categories' => $field_categories,
			),
			'condition'   => array(
				'jedv_condition' => Module::instance()->conditions->get_conditions_for_fields(),
			),
		);

		$value_categories = array();

		if ( class_exists( '\Jet_Engine_Dynamic_Tags_Module' ) ) {
			$value_categories[] = \Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY;
		}

		$data['jedv_value'] = array(
			'label'       => __( 'Value', 'jet-engine' ),
			'description' => __( 'Set value to compare. Separate values with commas to set values list.', 'jet-engine' ),
			'type'        => 'textarea',
			'label_block' => true,
			'dynamic' => array(
				'active' => true,
				'categories' => $value_categories,

			),
			'condition'   => array(
				'jedv_condition' => Module::instance()->conditions->get_conditions_with_value_detect(),
			),
		);

		$data['jedv_context'] = array(
			'label'       => __( 'Context', 'jet-engine' ),
			'description' => __( 'Context of object to get value from - current post by default or current listing item object', 'jet-engine' ),
			'type'        => 'select',
			'label_block' => true,
			'default'     => 'default',
			'options'     => array(
				'default'         => __( 'Default', 'jet-engine' ),
				'current_listing' => __( 'Current listing item object', 'jet-engine' ),
			),
			'condition'   => array(
				'jedv_condition' => Module::instance()->conditions->get_conditions_for_fields(),
			),
		);

		$data = array_merge( $data, Module::instance()->conditions->add_condition_specific_controls() );

		$data['jedv_data_type'] = array(
			'type'        => 'select',
			'label'       => __( 'Data type', 'jet-engine' ),
			'label_block' => true,
			'default'     => 'chars',
			'options'     => Module::instance()->get_data_types(),
			'condition'   => array(
				'jedv_condition' => Module::instance()->conditions->get_conditions_with_type_detect(),
			),
		);

		return $data;

	}

	public function get_data_types() {
		
		/**
		 * Filter data types for condition comparison
		 *
		 * @var array
		 */
		$data_types = apply_filters( 'jet-engine/modules/dynamic-visibility/data-types', array(
			'chars'   => __( 'Chars (alphabetical comparison)', 'jet-engine' ),
			'numeric' => __( 'Numeric', 'jet-engine' ),
			'date'    => __( 'Datetime', 'jet-engine' )
		) );

		return $data_types;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Module
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}
