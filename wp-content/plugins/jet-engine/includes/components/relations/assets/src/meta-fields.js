import FieldsList from 'fields-list';

const {
	Button,
	TextControl,
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

const {
	assign
} = window.lodash;

class MetaFields extends Component {

	constructor( props ) {

		super( props );

		let value = this.props.value || {};

		this.state = {
			metaData: assign( {}, value ),
			isBusy: false,
			done: false,
		}

		this.savedTimeout = null;

	}

	saveMeta() {
		window.wp.ajax.send(
			'jet_engine_relations_save_relation_meta',
			{
				type: 'POST',
				data: {
					_nonce: window.JetEngineRelationsCommon._nonce,
					relID: this.props.relID,
					relatedObjectID: this.props.relatedObjectID,
					relatedObjectType: this.props.controlObjectType,
					relatedObjectName: this.props.controlObjectName,
					currentObjectID: this.props.currentObjectID,
					isParentProcessed: this.props.isParentProcessed,
					meta: this.state.metaData,
				},
				success: ( response ) => {

					this.setState( {
						isBusy: false,
						done: true,
					} );

					this.savedTimeout = setTimeout( () => {
						this.setState( {
							done: false,
						} );
					}, 2000 );


					this.props.onUpdate();

				},
				error: ( response, errorCode, errorText ) => {

					if ( response ) {
						alert( response );
					} else {
						alert( errorText );
					}

					this.setState( {
						isBusy: false,
						done: false,
					} );

				}
			}
		);
	}

	render() {
		
		return ( <Fragment>
			<FieldsList
				fields={ this.props.metaFields }
				values={ this.state.metaData }
				onChange={ ( newData ) => {
					this.setState( {
						metaData: assign( {}, newData )
					} );
				} }
			/>
			<div className="jet-engine-rels-popup__footer">
				<Button
					isPrimary
					isBusy={ this.state.isBusy }
					onClick={ () => {

						this.setState( {
							isBusy: true,
						} );

						if ( this.savedTimeout ) {
							clearTimeout( this.savedTimeout );
							this.savedTimeout = null;
						}

						this.saveMeta();
					} }
				>{ 'Save Meta Data' }</Button>
				{ this.state.isBusy && <span style={ { marginLeft: '10px' } }>Saving...</span> }
				{ ! this.state.isBusy && this.state.done && <span style={ { marginLeft: '10px', color: 'green' } }>Saved!</span> }
			</div>
		</Fragment> );
	}

}

export default MetaFields;
