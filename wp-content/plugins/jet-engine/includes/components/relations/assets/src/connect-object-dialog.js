const {
	Button,
	ComboboxControl
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

class ConnectObjectDialog extends Component {

	constructor( props ) {

		super( props );

		this.state = {
			items: [],
			isBusy: false,
		};

		this.fetchItems();

	}

	hasMeta() {
		return ( this.props.metaFields && this.props.metaFields.length );
	}

	fetchItems() {

		const relIDs = [];

		if ( this.props.relatedItems && this.props.relatedItems.length ) {
			for (var i = 0; i < this.props.relatedItems.length; i++) {
				relIDs.push( this.props.relatedItems[ i ].related_id );
			}
		}

		window.wp.ajax.send(
			'jet_engine_relations_get_type_items',
			{
				type: 'GET',
				data: {
					_nonce: window.JetEngineRelationsCommon._nonce,
					relID: this.props.relID,
					existing: relIDs,
					objectType: this.props.controlObjectType,
					object: this.props.controlObjectName,
				},
				success: ( response ) => {
					this.setState( { items: [ ...response ] } );
				},
				error: ( data, errorCode, errorText ) => {

					if ( data.message ) {
						alert( data.message );
					} else {
						alert( errorText );
					}

				}
			}
		);
	}

	render() {
		return ( <Fragment>
			{ 0 < this.state.items.length && <Fragment>
				<ComboboxControl
					value={ this.state.relatedID }
					onChange={ ( value ) => {
						this.setState( { relatedID: value } )
					} }
					label={ this.props.labels.select }
					options={ this.state.items }
				/>
				<Button
					isPrimary
					isBusy={ this.state.isBusy }
					disabled={ ! this.state.relatedID }
					onClick={ () => {
						this.setState( { isBusy: true } );
						this.props.onChange( this.state.relatedID );
					} }
				>{ this.props.labels.connectButton }</Button>
			</Fragment> }
		</Fragment> );
	}

}

export default ConnectObjectDialog;
