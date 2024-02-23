
import RelatedItemModal from 'related-item-modal';
import RelatedItemsTable from 'related-items-table';

const {
	Button,
	ButtonGroup
} = wp.components;

const {
	render,
	Component,
	Fragment
} = wp.element;

class App extends Component {

	constructor( props ) {

		super( props );

		this.state = {
			connectNew: false,
			createNew: false,
			relatedID: null,
			relatedItems: [],
			allowedOptions: [],
		};

		this.fetchItems();

	}

	fetchItems() {

		window.wp.ajax.send(
			'jet_engine_relations_get_related_items',
			{
				type: 'GET',
				data: {
					_nonce: window.JetEngineRelationsCommon._nonce,
					relID: this.props.relID,
					objectType: this.props.controlObjectType,
					object: this.props.controlObjectName,
					currentObjectID: this.props.currentObjectID,
					isParentProcessed: this.props.isParentProcessed,
				},
				success: ( response ) => {
					this.setState( { relatedItems: [ ...response ] } );
				},
				error: ( response, errorCode, errorText ) => {

					if ( response ) {
						alert( response );
					} else {
						alert( errorText );
					}

				}
			}
		);
	}

	buttonLabel( type ) {

		type = type || 'connect';

		switch( type ) {
			case 'connect':
				return this.props.labels.connectButton;
			case 'create':
				return this.props.labels.createButton;
		}
	}

	modalTitle() {

		if ( this.state.connectNew ) {
			return this.buttonLabel( 'connect' );
		}

		if ( this.state.createNew ) {
			return this.buttonLabel( 'create' );
		}

		return null;
	}

	closeModal( relatedItems ) {

		this.setState( {
			createNew: false,
			connectNew: false,
		} )

		if ( relatedItems && relatedItems.length ) {
			this.setState( { relatedItems: [ ...relatedItems ] } );
		}
	}

	canCreate() {
		return this.props.createFields && 0 < this.props.createFields.length;
	}

	render() {

		const buttonStyle = { margin: '0 10px 0 0' };

		return ( <div className="jet-engine-rels">
			{ ( this.state.connectNew || this.state.createNew ) && <RelatedItemModal
				{ ...this.props }
				title={ this.modalTitle() }
				relatedItems={ this.state.relatedItems }
				type={ ( this.state.connectNew ? 'connect' : 'create' ) }
				onClose={ ( relatedItems ) => {
					relatedItems = relatedItems || false;
					this.closeModal( relatedItems );
				} }
				onComplete={ ( relatedItems ) => {
					if ( relatedItems && relatedItems.length ) {
						this.setState( { relatedItems: [ ...relatedItems ] } );
					} else {
						this.setState( { relatedItems: [] } );
					}
					this.closeModal();
				} }
			/> }
			<ButtonGroup
				style={ {
					display: 'flex',
					gap: '10px'
				} }
			>
				{ this.canCreate() && <Button
					isSecondary
					onClick={ () => {
						this.setState( {
							createNew: true,
							connectNew: false,
						} )
					} }
				>{ this.buttonLabel( 'create' ) }</Button> }
				<Button
					isSecondary
					onClick={ () => {
						this.setState( {
							createNew: false,
							connectNew: true,
						} )
					} }
				>{ this.buttonLabel( 'connect' ) }</Button>
			</ButtonGroup>
			<RelatedItemsTable
				items={ this.state.relatedItems }
				columns={ this.props.tableColumns }
				metaFields={ this.props.metaFields }
				relID={ this.props.relID }
				currentObjectID={ this.props.currentObjectID }
				controlObjectType={ this.props.controlObjectType }
				controlObjectName={ this.props.controlObjectName }
				isParentProcessed={ this.props.isParentProcessed }
				onUpdate={ ( relatedItems ) => {
					this.setState( { relatedItems: [ ...relatedItems ] } );
				} }
			/>
		</div> );
	}

}

for ( var i = 0; i < window.JetEngineRelationsControls.length; i++ ) {

	let control = window.JetEngineRelationsControls[ i ];
	const controlEl = document.getElementById( control.relEl );

	if ( controlEl ) {
		render(
			<App
				relID={ control.relID }
				metaFields={ control.metaFields }
				labels={ control.labels }
				tableColumns={ control.tableColumns }
				currentObjectID={ window.JetEngineCurrentObjectID }
				controlObjectType={ control.objectType }
				controlObjectName={ control.object }
				isParentProcessed={ control.isParentProcessed }
				createFields={ control.createFields }
			/>,
			controlEl
		);
	}

}
