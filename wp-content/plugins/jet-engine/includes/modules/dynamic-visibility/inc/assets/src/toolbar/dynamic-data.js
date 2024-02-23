import Database from "../icons/database";

const {
	Button,
	Dropdown,
	PanelBody,
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

const {
	DataSourceControls,
	DataContextControls
} = window.JetEngineBlocksComponents;

class DynamicData extends Component {

	constructor( props ) {
		
		super( props );

		let attributes = {};

		if ( props.value ) {

			try {
				attributes = JSON.parse( props.value );
			} catch ( e ) {
				attributes = {};
			}

			if ( ! attributes || ! attributes.data_source ) {
				attributes = {};
			}

		}

		this.state = {
			attributes: attributes,
		}

	}

	render() {
		return <Fragment>
			<div className="jet-engine-visibility-dynamic-trigger">
				<Dropdown
					className="jet-engine-dynamic-source"
					contentClassName="jet-engine-dynamic-source--inner-content"
					position="bottom center"
					key={ 'dynamic_control_' + this.props.control }
					popoverProps={ {
						__unstableSlotName: "jedv_popover_slot"
					} }
					renderToggle={ ( { isOpen, onToggle } ) => {
						return <Button
							isSmall={ true }
							variant="tertiary"
							className={ isOpen ? 'is-selected' : '' }
							icon={ Database }
							onClick={ onToggle }
						></Button>;
					} }
					renderContent={ ( { isOpen, onToggle } ) => (
						<Fragment>
							<PanelBody
								title={ 'Data Source' }
								initialOpen={ true }
							>
								<DataSourceControls
									getValue={ ( key, attr, object ) => {

										object = object || {};

										if ( ! key || ! attr ) {
											return null;
										}

										return object[ key ];
									} }
									setValue={ ( newValue, key, attr, object, setAttributes, supports ) => {
										this.setState( { attributes: _.assign( {}, this.state.attributes, { [ key ]: newValue } ) } );
									} }
									attr="dynamic_value"
									attributes={ this.state.attributes }
									setAttributes={ ( newData ) => {
										return null;
									} }
									supports={ [] }
								/>
							</PanelBody>
							<PanelBody
								title={ 'Data Context' }
								initialOpen={ false }
							>
								<DataContextControls
									getValue={ ( key, attr, object ) => {

										object = object || {};

										if ( ! key || ! attr ) {
											return null;
										}

										return object[ key ];
									} }
									setValue={ ( newValue, key, attr, object, setAttributes, supports ) => {
										this.setState( { attributes: _.assign( {}, this.state.attributes, { [ key ]: newValue } ) } );
									} }
									attr="dynamic_value"
									attributes={ this.state.attributes }
									setAttributes={ ( newData ) => {
										return null;
									} }
									supports={ [] }
								/>
							</PanelBody>
							<Button
								isSmall={ true }
								variant="tertiary"
								style={ {
									width: '100%',
									justifyContent: 'center'
								} }
								className="jet-engine-dynamic-source--apply"
								onClick={ () => {
									this.props.onChange( JSON.stringify( this.state.attributes ) );
									onToggle();
								} }
							>{ 'Apply' }</Button>
						</Fragment>
					) }

				/>
			</div>
		</Fragment>
	}
}

export default DynamicData;
