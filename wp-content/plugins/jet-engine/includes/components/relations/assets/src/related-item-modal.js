import ConnectObjectDialog from 'connect-object-dialog';
import CreateObjectDialog from 'create-object-dialog';
import MetaFields from 'meta-fields';

const {
	Modal,
	Button
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

class RelatedItemModal extends Component {

	constructor( props ) {
		super( props );

		this.state = {
			relatedObjectID: false,
			relatedList: [],
			isBusy: false,
		}

	}

	processRelation( relatedObjectID ) {
		window.wp.ajax.send(
			'jet_engine_relations_update_relation_items',
			{
				type: 'POST',
				data: {
					_nonce: window.JetEngineRelationsCommon._nonce,
					relID: this.props.relID,
					relatedObjectID: relatedObjectID,
					relatedObjectType: this.props.controlObjectType,
					relatedObjectName: this.props.controlObjectName,
					isParentProcessed: this.props.isParentProcessed,
					currentObjectID: this.props.currentObjectID,
				},
				success: ( response ) => {

					if ( ! this.hasMetaFields() ) {
						this.props.onComplete( response.related_list );
					} else {
						this.setState( {
							relatedObjectID: relatedObjectID,
							relatedList: response.related_list,
						} );
					}

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

	hasMetaFields() {
		return this.props.metaFields && 0 < this.props.metaFields.length;
	}

	showMetaFields() {
		return this.state.relatedObjectID && this.hasMetaFields();
	}

	modalTitle() {

		if ( this.showMetaFields() ) {
			return 'Edit Meta Fields';
		} else {
			return this.props.title;
		}

	}

	render() {

		const style = { width: '760px', maxWidth: '80vw' };

		if ( this.state.isBusy ) {
			style.opacity = '0.9';
		}

		const modalClasses = [ 'jet-engine-rels-modal' ];

		if ( this.showMetaFields() ) {
			modalClasses.push( 'has-footer' );
		}

		return ( <Modal
			title={ this.modalTitle() }
			style={ style }
			className={ modalClasses.join( ' ' ) }
			onRequestClose={ ( event ) => {
				if ( ! event.target.classList.contains( 'is-nested-modal-trigger' ) ) {
					this.props.onClose( this.state.relatedList );
				}
			} }
		>
			{ 'create' === this.props.type && ! this.state.relatedObjectID && <CreateObjectDialog
				{ ...this.props }
				onChange={ ( relatedObjectID ) => {
					this.setState( { isBusy: true } );
					this.processRelation( relatedObjectID );
				} }
			/> }
			{ 'connect' === this.props.type && ! this.state.relatedObjectID && <ConnectObjectDialog
				{ ...this.props }
				onChange={ ( relatedObjectID ) => {
					this.setState( { isBusy: true } );
					this.processRelation( relatedObjectID );
				} }
			/> }
			{ this.showMetaFields() && <MetaFields
				{ ...this.props }
				relatedObjectID={ this.state.relatedObjectID }
				onUpdate={ () => {
					this.props.onComplete( this.state.relatedList );
				} }
			/> }
		</Modal> );
	}

}

export default RelatedItemModal;
