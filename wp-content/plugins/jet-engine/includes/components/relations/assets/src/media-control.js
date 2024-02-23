const {
	Button
} = wp.components;

const {
	MediaUpload,
	MediaUploadCheck
} = wp.blockEditor;

const {
	Component,
	Fragment
} = wp.element;

const {
	assign,
	castArray
} = window.lodash;

class MediaUploadFallback extends Component {

	constructor( props ) {

		super( props );

		this.frame = wp.media( {
			multiple: false,
		} );

		this.openModal = this.openModal.bind( this );
		this.onOpen    = this.onOpen.bind( this );
		this.onSelect  = this.onSelect.bind( this );
		this.onUpdate  = this.onUpdate.bind( this );
		this.onClose   = this.onClose.bind( this );

		this.frame.on( 'select', this.onSelect );
		this.frame.on( 'update', this.onUpdate );
		this.frame.on( 'open', this.onOpen );
		this.frame.on( 'close', this.onClose );

	}

	onSelect() {
		const attachment = this.frame.state().get( 'selection' ).toJSON();
		this.props.onSelect( attachment[0] );
	}

	onUpdate( selections ) {

		const state = this.frame.state();
		const selectedImages = selections || state.get( 'selection' );

		if ( ! selectedImages || ! selectedImages.models.length ) {
			return;
		}

		this.props.onSelect( selectedImages.models[ 0 ].toJSON() );

	}

	onOpen() {

		this.updateCollection();

		const hasMedia = !! this.props.value;

		if ( ! hasMedia ) {
			return;
		}

		const selection = this.frame.state().get( 'selection' );

		castArray( this.props.value ).forEach( ( id ) => {
			selection.add( wp.media.attachment( id ) );
		} );

	}

	onClose() {
		if ( this.props.onClose ) {
			this.props.onClose();
		}
	}

	updateCollection() {
		const frameContent = this.frame.content.get();
		
		if ( frameContent && frameContent.collection ) {
			const collection = frameContent.collection;

			// clean all attachments we have in memory.
			collection
				.toArray()
				.forEach( ( model ) => model.trigger( 'destroy', model ) );

			// reset has more flag, if library had small amount of items all items may have been loaded before.
			collection.mirroring._hasMore = true;

			// request items
			collection.more();
		}
	}

	openModal() {
		this.frame.open();
	}

	render() {
		return this.props.render( { open: this.openModal } );
	}

	componentWillUnmount() {
		this.frame.remove();
	}

}

class MediaControl extends Component {

	constructor( props ) {

		super( props );

		this.state = {
			value: this.props.value
		};

	}

	componentDidMount() {

		let fetchImg = false;

		switch ( this.props.value_format ) {
			case 'both':

				if ( this.props.value && this.props.value.url ) {
					this.setState( {
						previewURL: this.props.value.url,
					} );
				}

				break;

			case 'url':

				if ( this.props.value ) {
					this.setState( {
						previewURL: this.props.value,
					} );
				}

				break;

			default:

				if ( this.props.value ) {
					fetchImg = this.props.value;
				}

				break;
		}

		if ( fetchImg ) {
			this.fetchImg( fetchImg );
		}

	}

	mediaUploadRender( open ) {
		return <div style={ {
			display: 'flex',
			paddingTop: '5px',
			paddingBottom: '15px',
		} }>
			<Button
				onClick={ open }
				className={ 'is-nested-modal-trigger' }
				isSecondary
				isSmall
			>{ 'Select or upload image' }</Button>
			{ this.state.value && <Button
				onClick={ () => {
					this.setState( ( state ) => {

						const newState = assign( {}, state, {
							value: null,
							previewURL: null
						} );

						this.props.onChange( newState.value );

						return newState;
					} );
				} }
				isDestructive
				isSmall
				style={ { marginLeft: '5px' } }
			>{ 'Reset' }</Button> }
		</div>;
	}

	mediaUploadOnSelect( media ) {

		this.setState( ( state ) => {

			var newState = {};

			switch ( this.props.value_format ) {
				case 'both':

					newState = assign( {}, state, { value: {
						id: media.id,
						url: media.url,
					} } );
					break;

				case 'url':

					newState = assign( {}, state, { value: media.url } );
					break;

				default:

					newState = assign( {}, state, { value: media.id } );
					break;
			}

			this.props.onChange( newState.value );

			return newState;

		} );

		this.setState( { previewURL: media.url } );
	}

	fetchImg( imgID ) {

		wp.apiFetch( {
			method: 'get',
			path: '/wp/v2/media/' + imgID,
		} ).then( ( response ) => {
			this.setState( {
				previewURL: response.media_details.sizes.thumbnail.source_url,
			} );
		} );

	}

	render() {
		return <Fragment>
			<div style={ { paddingBottom: '10px' } }><b>{ this.props.label }</b></div>
			{ this.state.previewURL && <img src={ this.state.previewURL } width="150px" height="auto"/> }
			<MediaUploadCheck
				fallback={ <MediaUploadFallback
						onSelect={ ( media ) => {
						this.mediaUploadOnSelect( media );
					} }
					value={ this.state.value }
					render={ ( { open } ) => {
						return this.mediaUploadRender( open );
					} }
				/> }
			>
				<MediaUpload
					onSelect={ ( media ) => {
						this.mediaUploadOnSelect( media );
					} }
					value={ this.state.value }
					render={ ( { open } ) => {
						return this.mediaUploadRender( open );
					} }
				/>
			</MediaUploadCheck>
		</Fragment>;
	}

}

export default MediaControl;
