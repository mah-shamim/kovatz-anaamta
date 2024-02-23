import DynamicData from './dynamic-data';

const {
	Modal,
	ToggleControl,
	SelectControl,
	Popover
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

const {
	RepeaterControl,
	CustomControl
} = window.JetEngineBlocksComponents;

class VisibilityModal extends Component {

	constructor( props ) {
		
		super( props );

		this.state = {
			attributes: _.assign( {
				jedv_enabled: false,
				jedv_type: 'show',
				jedv_conditions: [],
				jedv_relation: 'AND',
			}, this.props.attributes )
		}

	}

	setAttributes( newAttributes ) {
		this.setState( { attributes: _.assign( {}, this.state.attributes, newAttributes ) } );
	}

	render() {

		const style = { width: '560px', maxWidth: '80vw', minHeight: '95vh' };

		if ( this.state.isBusy ) {
			style.opacity = '0.9';
		}

		const modalClasses = [ 'jet-engine-visibility-modal' ];
		const itemControls = window.JetEngineDynamicVisibilityData.controls;
		const defaults     = {};

		for (var i = 0; i < itemControls.length; i++) {
			defaults[ itemControls[ i ].name ] = itemControls[ i ].default || null;
		}

		return ( <Modal
			title={ 'Set up visibility conditions for current block' }
			style={ style }
			className={ modalClasses.join( ' ' ) }
			onRequestClose={ ( event ) => {
				if ( ! event.target.classList.contains( 'is-nested-modal-trigger' ) ) {
					this.props.onClose( this.state.attributes );
				}
			} }
		>
			<ToggleControl
				label={ 'Enable' }
				checked={ this.state.attributes.jedv_enabled }
				onChange={ () => {
					this.setAttributes( { jedv_enabled: ! this.state.attributes.jedv_enabled } )
				} }
			/>
			<br/>
			{ this.state.attributes.jedv_enabled && <SelectControl
				label={ 'Visibility condition type' }
				value={ this.state.attributes.jedv_type }
				options={ [
					{
						value: 'show',
						label: 'Show element if condition met',
					},
					{
						value: 'hide',
						label: 'Hide element if condition met',
					}
				] }
				onChange={ ( newValue ) => {
					this.setAttributes( { jedv_type: newValue } )
				} }
			/> }
			{ this.state.attributes.jedv_enabled && <RepeaterControl
				data={ this.state.attributes.jedv_conditions }
				default={ defaults }
				onChange={ newData => {
					this.setAttributes( { jedv_conditions: newData } );
				} }
			>
				{
					( item, index ) =>
						<div>
						{ itemControls.map( ( control ) => {

							const setValue = ( newValue ) => {
								
								const conditions  = [ ...this.state.attributes.jedv_conditions ];
								const currentItem = conditions[ index ];

								if ( ! currentItem ) {
									return;
								}

								conditions[ index ] = _.assign( {}, currentItem, {
									[ control.name ]: newValue
								} );

								this.setAttributes( { jedv_conditions: [ ...conditions ] } );

							}

							return <CustomControl
								control={ control }
								value={ item[ control.name ] }
								condition={ control.condition }
								getValue={ ( key, attr, object ) => {

									object = object || {};

									if ( ! key || ! attr ) {
										return '';
									}

									if ( ! object[ key ] ) {
										return '';
									}

									return object[ key ];
								} }
								attr={ control.name }
								attributes={ item }
								onChange={ newValue => {
									setValue( newValue );
								} }
							>
								{ control.dynamic && <DynamicData
									control={ control.name }
									value={ item[ control.name ] }
									onChange={ ( newValue ) => {
										setValue( newValue );
									} }
								/> }
							</CustomControl>
						} ) }
						</div>
				}
			</RepeaterControl> }
			{ this.state.attributes.jedv_enabled && this.state.attributes.jedv_conditions.length > 1 && <SelectControl
				label={ 'Relation' }
				value={ this.state.attributes.jedv_relation }
				options={ [
					{
						value: 'AND',
						label: 'AND',
					},
					{
						value: 'OR',
						label: 'OR',
					}
				] }
				onChange={ ( newValue ) => {
					this.setAttributes( { jedv_relation: newValue } )
				} }
			/> }
			<Popover.Slot name="jedv_popover_slot"/>
		</Modal> );
	}

}

export default VisibilityModal;
