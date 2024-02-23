const {
	TextareaControl,
	PanelBody
} = wp.components;

const RestPanel = class extends wp.element.Component {

	constructor(props) {
		super( props );
		this.handleChange = this.handleChange.bind( this );
	}

	handleChange( key, value ) {
		var atts = JSON.parse( JSON.stringify( this.props.attributes ) );
		atts[ key ] = value;
		this.props.onChange( atts );
	}

	render() {
		return <PanelBody 
					title={ 'Rest API Query' }
					initialOpen={ false }
				>
			<TextareaControl
				label={ 'Query Arguments' }
				help={ 'Enter each argument in a separate line. Arguments format - argument_name=argument_value' }
				value={ this.props.attributes.jet_rest_query }
				onChange={ newValue => {
					this.handleChange( 'jet_rest_query', newValue );
				} }
			/>
		</PanelBody>;
	}
}

if ( ! window.JetEngineListingData.customPanles.listingGrid ) {
	window.JetEngineListingData.customPanles.listingGrid = [];
}

window.JetEngineListingData.customPanles.listingGrid.push( RestPanel );
