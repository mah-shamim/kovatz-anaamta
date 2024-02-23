<?php
/**
 * Active filters class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Active_Filters' ) ) {
	/**
	 * Define Jet_Smart_Filters_Active_Filters class
	 */
	class Jet_Smart_Filters_Active_Filters {
		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'active-filters';
		}

		/**
		 * Render filters smaple to style them in editor
		 */
		public function render_filters_sample( $settings = array() ) {

			$active_filters = array(
				array(
					'label' => __( 'Categories', 'jet-smart-filters' ),
					'value' => __( 'Active Category', 'jet-smart-filters' ),
				),
				array(
					'label' => __( 'Tags', 'jet-smart-filters' ),
					'value' => __( 'Active Tag', 'jet-smart-filters' ),
				),
			);

			$title = isset( $settings['filters_label'] ) ? $settings['filters_label'] : '';

			echo '<div class="jet-active-filters__list">';
			echo '<div class="jet-active-filters__title">' . $title . '</div>';
			foreach ( $active_filters as $_filter ) {
				echo '<div class="jet-active-filter">';
				echo jet_smart_filters()->utils->template_replace_with_value( 'for-js/active-filter/label.php', $_filter['label'] );
				echo jet_smart_filters()->utils->template_replace_with_value( 'for-js/active-filter/value.php', $_filter['value'] );
				echo jet_smart_filters()->utils->get_template_html( 'for-js/active-filter/remove.php' );
				echo '</div>';
			}
			echo '<div>';
		}

		/**
		 * Render tags smaple to style them in editor
		 */
		public function render_tags_sample( $settings = array() ) {

			$active_tags = array(
				array(
					'label' => __( 'Categories', 'jet-smart-filters' ),
					'value' => __( 'Active Category', 'jet-smart-filters' ),
				),
				array(
					'label' => __( 'Tags', 'jet-smart-filters' ),
					'value' => __( 'Active Tag', 'jet-smart-filters' ),
				),
			);

			$title       = isset( $settings['tags_label'] ) ? $settings['tags_label'] : '';
			$clear_item  = isset( $settings['clear_item'] ) ? filter_var( $settings['clear_item'], FILTER_VALIDATE_BOOLEAN ) : false;
			$clear_label = ! empty( $settings['clear_item_label'] ) ? $settings['clear_item_label'] : false;

			echo '<div class="jet-active-tags__list">';
			echo '<div class="jet-active-tags__title">' . $title . '</div>';
			if ( $clear_item && $clear_label ) {
				echo '<div class="jet-active-tag jet-active-tag--clear">';
				echo jet_smart_filters()->utils->template_replace_with_value( 'for-js/active-tag/value.php', $clear_label );
				echo jet_smart_filters()->utils->get_template_html( 'for-js/active-tag/remove.php' );
				echo '</div>';
			}
			foreach ( $active_tags as $_tag ) {
				echo '<div class="jet-active-tag">';
				echo jet_smart_filters()->utils->template_replace_with_value( 'for-js/active-tag/label.php', $_tag['label'] );
				echo jet_smart_filters()->utils->template_replace_with_value( 'for-js/active-tag/value.php', $_tag['value'] );
				echo jet_smart_filters()->utils->get_template_html( 'for-js/active-tag/remove.php' );
				echo '</div>';
			}
			echo '<div>';
		}
	}
}
