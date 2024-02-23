import FieldsList from 'fields-list';

const {
	Button,
	ComboboxControl
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

const {
	assign
} = window.lodash;

class CreateObjectDialog extends Component {

	constructor( props ) {

		super( props );

		this.state = {
			item: {},
			isBusy: false,
		};

	}

	createItem() {

		window.wp.ajax.send(
			'jet_engine_relations_create_item_of_type',
			{
				type: 'GET',
				data: {
					_nonce: window.JetEngineRelationsCommon._nonce,
					relID: this.props.relID,
					relatedObjectType: this.props.controlObjectType,
					relatedObjectName: this.props.controlObjectName,
					isParentProcessed: this.props.isParentProcessed,
					item: this.state.item,
				},
				success: ( response ) => {
					this.props.onChange( response.itemID );
					this.setState( { isBusy: false } );
				},
				error: ( response, errorCode, errorText ) => {

					this.setState( { isBusy: false } );

					if ( response ) {
						alert( response );
					} else {
						alert( errorText );
					}

				}
			}
		);
	}

	render() {
		return ( <Fragment>
			<FieldsList
				fields={ this.props.createFields }
				onChange={ ( newData ) => {
					this.setState( {
						item: assign( {}, newData )
					} )
				} }
			/>
			<Button
				isPrimary
				isBusy={ this.state.isBusy }
				className="jet-engine-rels__create-item"
				onClick={ () => {
					this.setState( { isBusy: true } );
					this.createItem();
				} }
			>{ this.props.labels.createButton }</Button>
		</Fragment> );
	}

}

export default CreateObjectDialog;
