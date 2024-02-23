<?php
namespace Jet_Engine\Relations;

/**
 * Admin filters compatibility class
 */
class Admin_Filters {

	private $allowed_relations = array();
	private $stack             = array();

	public function __construct() {

		if ( ! $this->set_relations_for_filters() ) {
			return;
		}

		add_filter( 'jet-engine/post-types/admin-filters-types', array( $this, 'register_type' ) );
		add_action( 'jet-engine/post-types/admin-filters/custom-controls', array( $this, 'register_controls' ) );
		add_action( 'jet-engine/post-types/admin-filters/custom-filter/related_items', array( $this, 'render_filter' ), 10, 3 );
		add_filter( 'jet-engine/admin-filters/apply-filter/related_items', array( $this, 'apply_filter' ), 10, 4 );

	}

	public function apply_filter( $query, $filter, $value, $admin_filters ) {

		if ( ! $value ) {
			return $query;
		}

		$rel_id = ! empty( $filter['rel_id'] ) ? $filter['rel_id'] : false;

		if ( ! $rel_id ) {
			return $query;
		}

		$rel = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $rel ) {
			return $query;
		}

		$screen = get_current_screen();

		if ( empty( $screen->post_type ) ) {
			return $query;
		}

		if ( $rel->is_parent( 'posts', $screen->post_type ) ) {
			$ids = $rel->get_parents( $value, 'ids' );
		} else {
			$ids = $rel->get_children( $value, 'ids' );
		}

		if ( empty( $ids ) ) {
			$ids = 'not-found';
		}

		if ( empty( $this->stack ) || 'not-found' === $ids ) {
			$this->stack = $ids;
		} elseif ( is_array( $this->stack ) ) {
			$this->stack = array_intersect( $this->stack, $ids );
		}

		$query->query_vars['post__in'] = $this->stack;

		return $query;
	}

	/**
	 * Render admin filter dropdown
	 *
	 * @param  [type] $filter [description]
	 * @param  [type] $index  [description]
	 * @return [type]         [description]
	 */
	public function render_filter( $filter, $index, $admin_filters ) {

		$rel_id = ! empty( $filter['rel_id'] ) ? $filter['rel_id'] : false;

		if ( ! $rel_id ) {
			return;
		}

		$rel = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $rel ) {
			return;
		}

		$screen = get_current_screen();

		if ( empty( $screen->post_type ) ) {
			return;
		}

		if ( $rel->is_parent( 'posts', $screen->post_type ) ) {
			$options_from = 'child_object_id';
			$type_from    = $rel->get_args( 'child_object' );
		} else {
			$options_from = 'parent_object_id';
			$type_from    = $rel->get_args( 'parent_object' );
		}

		$ids = $rel->db->raw_query( "SELECT DISTINCT $options_from AS id FROM %table% WHERE rel_id = $rel_id;" );

		$options = $admin_filters->add_placeholder( $filter );
		$value   = $admin_filters->get_active_filter_value( $index );

		if ( ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				$options .= sprintf(
					'<option value="%1$s" %3$s>%2$s</option>',
					$id->id,
					jet_engine()->relations->types_helper->get_type_item_title( $type_from, $id->id, $rel ),
					( $value == $id->id ) ? 'selected' : ''
				);
			}
		}

		printf( '<select name="%1$s">%2$s</select>', $admin_filters->get_filter_name( $index ), $options );

	}

	/**
	 * Render custom controls for the relations admin filter
	 *
	 * @return [type] [description]
	 */
	public function register_controls() {

		$allowed_relations = array(
			array(
				'value' => '',
				'label' => __( 'Select relation...', 'jet-engine' ),
			)
		);

		foreach ( $this->allowed_relations as $rel_id => $rel ) {
			$allowed_relations[] = array(
				'value' => $rel_id,
				'label' => $rel->get_relation_name(),
			);
		}

		?>
		<cx-vui-select
			label="<?php _e( 'Relation', 'jet-engine' ); ?>"
			description="<?php _e( 'Select JetEngine relation to get items from', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-if="'related_items' === filter.type"
			:options-list="<?php echo htmlspecialchars( json_encode( $allowed_relations ) ); ?>"
			:value="adminFilters[ index ].rel_id"
			@input="setFieldProp( filter._id, 'rel_id', $event, adminFilters )"
		></cx-vui-select>
		<?php
	}

	/**
	 * Register new filters type for relations
	 *
	 * @return [type] [description]
	 */
	public function register_type( $types ) {

		$types[] = array(
			'value' => 'related_items',
			'label' => __( 'Filter by related items', 'jet-engine' ),
		);

		return $types;
	}

	/**
	 * We need only relations where at least one item of type Posts
	 *
	 * @return boolean [description]
	 */
	public function set_relations_for_filters() {

		$found     = false;
		$relations = jet_engine()->relations->get_active_relations();

		if ( ! empty( $relations ) ) {
			foreach ( $relations as $rel ) {
				if ( jet_engine()->relations->types_helper->object_is( $rel->get_args( 'parent_object' ), 'posts' ) || jet_engine()->relations->types_helper->object_is( $rel->get_args( 'child_object' ), 'posts' ) ) {
					$this->allowed_relations[ $rel->get_id() ] = $rel;
					$found = true;
				}
			}
		}

		return $found;

	}

}
