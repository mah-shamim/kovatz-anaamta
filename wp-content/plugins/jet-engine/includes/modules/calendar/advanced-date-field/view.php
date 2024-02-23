<?php
/**
 * Register Advanced date meta field type
 */

class Jet_Engine_Advanced_Date_Field_View {

	public $field_type;

	public $assets_added = false;

	/**
	 * Constructor for the class
	 */
	public function __construct( $field_type ) {

		$this->field_type = $field_type;

		add_filter(
			'jet-engine/meta-fields/config',
			array( $this, 'register_field_type' )
		);

		add_filter(
			'jet-engine/meta-fields/' . $this->field_type . '/args',
			array( $this, 'prepare_field_args' ),
			10, 3
		);

	}

	/**
	 * Register field type for JetEngine options
	 * 
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	public function register_field_type( $config ) {

		$config['field_types'][] = array(
			'value'         => $this->field_type,
			'label'         => __( 'Advanced Date', 'jet-engine' ),
			'skip_repeater' => true,
		);

		// Added the map field for specific condition operators.
		foreach ( $config['condition_operators'] as &$condition_operator ) {

			if ( empty( $condition_operator['value'] ) ) {
				continue;
			}

			if ( in_array( $condition_operator['value'], array( 'equal', 'not_equal' ) ) && isset( $condition_operator['not_fields'] ) ) {
				$condition_operator['not_fields'][] = $this->field_type;
			}

			if ( in_array( $condition_operator['value'], array( 'contains', '!contains' ) ) && isset( $condition_operator['fields'] ) ) {
				$condition_operator['fields'][] = $this->field_type;
			}

		}

		unset( $condition_operator );

		return $config;
	}

	/**
	 * Prepare field arguments before registering field in the actual meta box
	 * 
	 * @param  [type] $args     [description]
	 * @param  [type] $field    [description]
	 * @param  [type] $instance [description]
	 * @return [type]           [description]
	 */
	public function prepare_field_args( $args, $field, $instance ) {

		$args['type']         = 'text';
		$args['input_type']   = 'hidden';
		$args['custom_type']  = $this->field_type;
		$args['autocomplete'] = 'off';
		$args['class']        = 'jet-engine-' . $this->field_type . '-field';

		if ( ! empty( $field['width'] ) && in_array( $field['width'], [ '50%', '33.33333%', '25%' ] ) ) {
			$args['class'] .= ' jet-engine-' . $this->field_type . '-field--tiny';
		}

		add_action( 'admin_enqueue_scripts', function ( $hook ) use ( $instance ) {

			if ( ! $instance->is_allowed_on_current_admin_hook( $hook ) ) {
				return;
			}

			if ( $this->assets_added ) {
				return;
			}

			$this->enqueue_assets();

			$this->assets_added = true;
		} );

		return $args;
	}

	/**
	 * Enqueue field specific assets for editor
	 * 
	 * @param  boolean $hook [description]
	 * @return [type]        [description]
	 */
	public function enqueue_assets( $hook = false ) {

		wp_enqueue_style(
			'jet-engine-advanced-date-field',
			jet_engine()->plugin_url( 'includes/modules/calendar/assets/css/advanced-date-field.css' ),
			array(),
			jet_engine()->get_version()
		);

		$post_meta_box = new Jet_Engine_CPT_Meta();
		$post_meta_box->date_assets();

		wp_enqueue_script(
			'jet-engine-advanced-date-field',
			jet_engine()->plugin_url( 'includes/modules/calendar/assets/js/advanced-date-field.js' ),
			array( 'jquery', 'wp-util' ),
			jet_engine()->get_version(),
			true
		);

		add_action( 'admin_print_footer_scripts', array( $this, 'print_field_js_template' ) );

	}

	/**
	 * JS template of field for editor
	 * 
	 * @return [type] [description]
	 */
	public function print_field_js_template() {
		?>
		<script type="text/html" id="tmpl-jet-engine-advanced-date-field">
			<div class="jet-engine-advanced-date-field__date">
				<span class="jet-engine-advanced-date-field__label"><?php 
					_e( 'Start date', 'jet-engine' );
				?></span>
				<div class="jet-engine-advanced-date-field__date-warp <# if ( data.required ) { #>cx-control-required<# } #>" data-control-name="{{ data.fieldName }}[date]">
					<input 
						type="date" 
						class="jet-engine-advanced-date-field__date"
						name="{{ data.fieldName }}[date]"
						placeholder="Select date..."
						value="{{ data.date }}"
						<# if ( data.required ) { #>required<# } #>
					>
					<div class="cx-control__error"></div>
				</div>
			</div>
			<div class="jet-engine-advanced-date-field__date">
				<span class="jet-engine-advanced-date-field__label"><?php 
					_e( 'Has end date', 'jet-engine' );
				?></span>
				<div class="jet-engine-advanced-date-field__switch-wrap">
					<label class="jet-engine-advanced-date-field__switch">
						<input 
							type="checkbox" 
							class="jet-engine-advanced-date-field__switch-input"
							name="{{ data.fieldName }}[is_end_date]"
							value="1"
							<# if ( data.isEndDate ) { #>checked<# } #>
						>
						<span class="jet-engine-advanced-date-field__switch-slider"></span>
					</label>
				</div>
				<# if ( data.isEndDate ) { #>
					<div class="jet-engine-advanced-date-field__date-warp <# if ( data.required ) { #>cx-control-required<# } #>" data-control-name="{{ data.fieldName }}[end_date]">
						<input 
							type="date" 
							class="jet-engine-advanced-date-field__end-date"
							name="{{ data.fieldName }}[end_date]"
							placeholder="Select date..."
							value="{{ data.endDate }}"
							<# if ( data.required ) { #>required<# } #>
						>
						<div class="cx-control__error"></div>
					</div>
				<# } #>
			</div>
			<div class="jet-engine-advanced-date-field__is-recurring">
				<span class="jet-engine-advanced-date-field__label"><?php 
					_e( 'Is recurring', 'jet-engine' );
				?></span>
				<label class="jet-engine-advanced-date-field__switch">
					<input 
						type="checkbox" 
						class="jet-engine-advanced-date-field__switch-input"
						name="{{ data.fieldName }}[is_recurring]"
						value="1"
						<# if ( data.isRecurring ) { #>checked<# } #>
					>
					<span class="jet-engine-advanced-date-field__switch-slider"></span>
				</label>
			</div>
			<# if ( data.isRecurring ) { #>
			<div class="jet-engine-advanced-date-field__recurring-wrap">
				<div class="jet-engine-advanced-date-field__recurring-row">
					<div class="jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label"><?php
						_e( 'Repeat', 'jet-engine' );
					?></div>
					<div class="jet-engine-advanced-date-field__recurring-content">
						<select name="{{ data.fieldName }}[recurring]" class="cx-ui-select">
							<# _.each( data.recurrings, function( recurring ) { #> 
								<option 
									value="{{ recurring.value }}"
									<# if ( data.recurring == recurring.value ) { #>selected<# } #>
								>{{ recurring.label }}</option>
							<# }); #>
						</select>
						<div class="jet-engine-advanced-date-field__recurring-label"><?php
							_e( 'every', 'jet-engine' );
						?></div>
						<input
							type="number"
							name="{{ data.fieldName }}[recurring_period]"
							min="1" 
							value="{{ data.recurringPeriod }}"
							class="cx-ui-text"
						>
						<# _.each( data.recurrings, function( recurring ) { #> 
							<# if ( data.recurring == recurring.value ) { #>
								<div class="jet-engine-advanced-date-field__recurring-label">{{ recurring.sublabel }}</div>
							<# } #>
						<# }); #>
					</div>
				</div>
				<# if ( 'daily' != data.recurring ) { #>
				<div class="jet-engine-advanced-date-field__recurring-row">
					<div class="jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label label-weekdays">&nbsp;</div>
					<div class="jet-engine-advanced-date-field__recurring-content">
					<# if ( 'weekly' == data.recurring ) { #>
						<div class="jet-engine-advanced-date-field__weekdays">
							<# _.each( data.weekdaysConfig, function( day ) { #> 
								<label>
									<input 
										type="checkbox" 
										value="{{ day.value }}" 
										name="{{ data.fieldName }}[week_days][]"
										<# if ( data.weekDays.includes( '' + day.value ) ) { #>checked<# } #>
									>
									<span class="jet-engine-advanced-date-field__weekday-label">{{ day.label }}</span>
									<span class="jet-engine-advanced-date-field__weekday-marker"></span>
								</label>
							<# }); #>
						</div>
					<# } #>
					<# if ( 'monthly' == data.recurring || 'yearly' == data.recurring ) { #>
						<div class="jet-engine-advanced-date-field__monthly">
							<div class="jet-engine-advanced-date-field__monthly-row">
								<label>
									<input 
										type="radio"
										value="on_day"
										name="{{ data.fieldName }}[monthly_type]"
										<# if ( 'on_day' == data.monthlyType ) { #>checked<# } #>
									>
									<?php _e( 'on day', 'jet-engine' ); ?>
								</label>
								<# if ( 'yearly' == data.recurring ) { #>
									<select 
										name="{{ data.fieldName }}[month]"
										class="cx-ui-select"
									>
									<# _.each( data.months, function( month ) { #> 
										<option
											value="{{ month.value }}"
											<# if ( month.value == data.month ) { #>selected<# } #>
										>{{ month.label }}</option>
									<# } ); #>
									</select>
								<# } #>
								<select 
									name="{{ data.fieldName }}[month_day]"
									class="cx-ui-select"
								>
								<# for ( var i = 1; i <= 31; i++ ) { #> 
									<option
										value="{{ i }}"
										<# if ( i == data.monthDay ) { #>selected<# } #>
									>{{ i }}</option>
								<# }; #>
								</select>
							</div>
							<div class="jet-engine-advanced-date-field__monthly-row">
								<label>
									<input 
										type="radio"
										value="on_day_type"
										name="{{ data.fieldName }}[monthly_type]"
										<# if ( 'on_day_type' == data.monthlyType ) { #>checked<# } #>
									>
									<?php _e( 'on the', 'jet-engine' ); ?>
								</label>
								<select 
									name="{{ data.fieldName }}[month_day_type]"
									class="cx-ui-select"
								>
									<option
										value="first"
										<# if ( 'first' == data.monthDayType ) { #>selected<# } #>
									><?php _e( 'First', 'jet-engine' ); ?></option>
									<option
										value="second"
										<# if ( 'second' == data.monthDayType ) { #>selected<# } #>
									><?php _e( 'Second', 'jet-engine' ); ?></option>
									<option
										value="third"
										<# if ( 'third' == data.monthDayType ) { #>selected<# } #>
									><?php _e( 'Third', 'jet-engine' ); ?></option>
									<option
										value="fourth"
										<# if ( 'fourth' == data.monthDayType ) { #>selected<# } #>
									><?php _e( 'Fourth', 'jet-engine' ); ?></option>
									<option
										value="last"
										<# if ( 'last' == data.monthDayType ) { #>selected<# } #>
									><?php _e( 'Last', 'jet-engine' ); ?></option>
								</select>
								<select 
									name="{{ data.fieldName }}[month_day_type_value]"
									class="cx-ui-select"
								>
									<# _.each( data.weekdaysConfig, function( day ) { #> 
									<option
										value="{{ day.value }}"
										<# if ( day.value == data.monthDayTypeValue ) { #>selected<# } #>
									>{{ day.label }}</option>
									<# } ); #>
									<option
										value="day"
										<# if ( 'day' == data.monthDayTypeValue ) { #>selected<# } #>
									><?php _e( 'Day', 'jet-engine' ); ?></option>
								</select>
								<# if ( 'yearly' == data.recurring ) { #>
									<select 
										name="{{ data.fieldName }}[month]"
										class="cx-ui-select"
									>
									<# _.each( data.months, function( month ) { #> 
										<option
											value="{{ month.value }}"
											<# if ( month.value == data.month ) { #>selected<# } #>
										>{{ month.label }}</option>
									<# } ); #>
									</select>
								<# } #>
							</div>
						</div>
					<# } #>
					</div>
				</div>
				<# } #>
				<div class="jet-engine-advanced-date-field__recurring-row">
					<div class="jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label"><?php
						_e( 'End', 'jet-engine' );
					?></div>
					<div class="jet-engine-advanced-date-field__recurring-content">
						<select name="{{ data.fieldName }}[end]" class="cx-ui-select">
							<option 
								value="after"
								<# if ( 'after' == data.end ) { #>selected<# } #>
							><?php _e( 'After', 'jet-engine' ); ?></option>
							<option 
								value="on_date"
								<# if ( 'on_date' == data.end ) { #>selected<# } #>
							><?php _e( 'On date', 'jet-engine' ); ?></option>
						</select>
						<# if ( 'after' == data.end ) { #>
						<input
							type="number"
							name="{{ data.fieldName }}[end_after]"
							min="2" 
							value="{{ data.endAfter }}"
							class="cx-ui-text"
						>
						<div class="jet-engine-advanced-date-field__recurring-label"><?php 
							_e( 'iterations', 'jet-engine' ); 
						?></div>
						<# } #>
						<# if ( 'on_date' == data.end ) { #>
						<input 
							type="date"
							class="jet-engine-advanced-date-field__date" 
							name="{{ data.fieldName }}[end_after_date]"
							placeholder="Select date..."
							value="{{ data.endAfterDate }}"
							required
						>
						<# } #>
					</div>
				</div>
			</div>
			<# } #>
		</script>
		<?php
	}

}
