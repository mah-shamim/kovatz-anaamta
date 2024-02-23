import DatabaseIcon from 'icons/database';
import MetaFields from 'meta-fields';

const {
	Modal,
	Button
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

const {
	assign
} = window.lodash;

class EditMeta extends Component {

	constructor( props ) {
		super( props );

		this.state = {
			showModal: false,
			dataLoaded: false,
			metaData: {},
		};

	}

	hasMeta() {
		return ( this.props.metaFields && 0 < this.props.metaFields.length );
	}

	fetchData() {

		window.wp.ajax.send(
			'jet_engine_relations_get_related_item_meta',
			{
				type: 'GET',
				data: {
					_nonce: window.JetEngineRelationsCommon._nonce,
					relID: this.props.relID,
					relatedObjectID: this.props.relatedObjectID,
					relatedObjectType: this.props.controlObjectType,
					relatedObjectName: this.props.controlObjectName,
					isParentProcessed: this.props.isParentProcessed,
					currentObjectID: this.props.currentObjectID,
				},
				success: ( response ) => {

					if ( response ) {
						this.setState( { metaData: assign( {}, response ) } );
					}

					this.setState( { dataLoaded: true } );
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

	render() {

		const style = { width: '760px', maxWidth: '80vw' };
		const modalClasses = [ 'jet-engine-rels-modal' ];

		if ( this.state.dataLoaded ) {
			modalClasses.push( 'has-footer' );
		}

		return ( <Fragment>
			<Button
				isSecondary
				isSmall
				icon={ DatabaseIcon }
				onClick={ () => {

					this.fetchData();

					this.setState( {
						showModal: true,
					} );

				} }
			>{ 'Edit Meta' }</Button>
			{ this.state.showModal && <Modal
				title={ 'Edit Meta' }
				style={ style }
				className={ modalClasses.join( ' ' ) }
				onRequestClose={ ( event ) => {

					if ( ! event.target.classList.contains( 'is-nested-modal-trigger' ) ) {
						this.setState( {
							showModal: false,
							metaData: {},
							dataLoaded: false,
						} );
					}

				} }
			>
				{ this.state.dataLoaded && <MetaFields
					{ ...this.props }
					value={ this.state.metaData }
					onUpdate={ () => {
						this.setState();
					} }
				/> }
				{ ! this.state.dataLoaded && <div>{ 'Loading existing meta data...' }</div> }
			</Modal> }
		</Fragment> );
	}

}

export default EditMeta;
