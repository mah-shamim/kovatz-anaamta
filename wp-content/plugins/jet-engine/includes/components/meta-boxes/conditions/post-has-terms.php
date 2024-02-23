<?php
namespace Jet_Engine\Components\Meta_Boxes\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Base class
 */
class Post_Has_Terms extends Base {

	/**
	 * Returns conditions name to show in options
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Post Has Taxonomy Terms', 'jet-engine' );
	}

	/**
	 * Returns appropriate setting key for this condition
	 *
	 * @return [type] [description]
	 */
	public function get_key() {
		return 'post_has_terms';
	}

	/**
	 * Check condition
	 *
	 * @return [type] [description]
	 */
	public function check_condition( $args = array() ) {

		$settings = isset( $args['settings'] ) ? $args['settings'] : array();
		$request  = isset( $args['request'] ) ? $args['request'] : array();

		$tax_to_check   = isset( $settings['tax'] ) ? $settings['tax'] : false;
		$terms_to_check = isset( $settings['terms'] ) ? $settings['terms'] : array();

		if ( ! $tax_to_check ) {
			return true;
		}

		$terms = $this->get_terms_from_request( $tax_to_check, $request );

		if ( empty( $terms_to_check ) ) {
			return ! empty( $terms );
		}

		$terms_to_check = apply_filters( 'jet-engine/meta-boxes/conditions/post-has-terms/check-terms', $terms_to_check, $tax_to_check );

		$intersect = array_intersect( $terms, $terms_to_check );

		return ! empty( $intersect );
	}

	/**
	 * Returns sanitized array of terms of required taxonomy
	 *
	 * @return array
	 */
	public function get_terms_from_request( $tax, $request = array() ) {

		$result = array();
		$all_terms = ! empty( $request['terms'] ) ? $request['terms'] : array();

		if ( ! isset( $all_terms[ $tax ] ) ) {
			return $result;
		}

		$terms = $all_terms[ $tax ];
		$terms = array_filter( $terms );

		if ( ! is_taxonomy_hierarchical( $tax ) ) {
			$result = array_map( function( $term ) use ( $tax ) {

				$term_obj = get_term_by( 'name', $term, $tax );

				if ( $term_obj ) {
					return $term_obj->term_id;
				} elseif ( is_numeric( $term ) ) {
					return $term;
				} else {
					return false;
				}

			}, $terms );
		} else {
			$result = array_map( 'absint', $terms );
		}

		return $result;
	}

	/**
	 * Renders appropriate UI control for current condition
	 *
	 * @return array
	 */
	public function get_control() {
		ob_start();
		?>
		<cx-vui-component-wrapper
			label="<?php _e( 'Post Has Taxonomy Terms', 'jet-engine' ); ?>"
			description="<?php _e( 'Select specific taxonomy terms, to show meta box only if post has these terms', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'meta-condition', 'terms-conditions' ]"
			:style="conditionControlsInlineCSS( '<?php echo $this->get_key(); ?>' )"
			:conditions="[
				{
					input: this.generalSettings.object_type,
					compare: 'in',
					value: <?php echo htmlentities( json_encode( $this->allowed_sources() ) ) ?>,
				},
				{
					input: '<?php echo $this->get_key() ?>',
					compare: 'in',
					value: this.generalSettings.active_conditions,
				}
			]"
		>
			<cx-vui-select
				:options-list="[ ...[ { value: '', label: '<?php _e( 'Select taxonomy...', 'jet-engine' ) ?>' } ], ...taxonomies ]"
				v-model="generalSettings.<?php echo $this->get_key() ?>__tax"
				size="fullwidth"
			></cx-vui-select>
			<cx-vui-f-select
				:remote="true"
				:remote-callback="getIncludedTerms"
				size="fullwidth"
				:multiple="true"
				placeholder="<?php _e( 'Set terms...', 'jet-engine' ) ?>"
				v-model="generalSettings.<?php echo $this->get_key() ?>__terms"
				ref="<?php echo $this->get_key() ?>"
			></cx-vui-f-select>
			<?php echo $this->remove_button(); ?>
		</cx-vui-component-wrapper>
		<?php

		return ob_get_clean();

	}

	/**
	 * Returns array of allowed sources
	 *
	 * @return [type] [description]
	 */
	public function allowed_sources() {
		return array( 'post' );
	}

	/**
	 * Determine is codition checked on AJAX request or not
	 *
	 * @return boolean [description]
	 */
	public function is_ajax() {
		return true;
	}

	/**
	 * Return arguments list prepared for AJAX by given meta box arguments array
	 *
	 * @return array
	 */
	public function get_ajax_data_from_args( $args = array() ) {
		return array(
			'tax'   => ! empty( $args[ $this->get_key() . '__tax' ] ) ? $args[ $this->get_key() . '__tax' ] : '',
			'terms' => ! empty( $args[ $this->get_key() . '__terms' ] ) ? $args[ $this->get_key() . '__terms' ] : array(),
		);
	}

	/**
	 * Returns JS handler to pas data into AJAX request
	 *
	 * @return [type] [description]
	 */
	public function get_js_handler() {
		ob_start();
		?>
		window.JetEnginMBAjaxConditionsHandlers.<?php echo $this->get_key(); ?> = function( $ ) {

			$( document ).on( 'change', '.categorychecklist, .tagchecklist', () => {
				$( document ).trigger( 'jet-engine/meta-box/data-change', [] );
			} ).on( 'click', '.tagadd', () => {
				$( document ).trigger( 'jet-engine/meta-box/data-change', [] );
			} ).on( 'click', '.tag-cloud-link', () => {
				$( document ).trigger( 'jet-engine/meta-box/data-change', [] );
			} ).on( 'click', '.tagchecklist .ntdelbutton', () => {
				$( document ).trigger( 'jet-engine/meta-box/data-change', [] );
			} ).on( 'change', '.product_attributes .attribute_values', () => {
				$( document ).trigger( 'jet-engine/meta-box/data-change', [] );
			} );

		}
		<?php
		return ob_get_clean();
	}

}
