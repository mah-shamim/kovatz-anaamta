const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;

const {
	SVG,
	Path
} = wp.primitives;

const {
	InspectorControls
} = wp.editor;

const {
	PanelBody,
	SelectControl,
	TextControl,
	TextareaControl,
	Disabled
} = wp.components;

const {
	serverSideRender: ServerSideRender
} = wp;

const Icon = <SVG width="24" height="24" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><Path fillRule="evenodd" clipRule="evenodd" d="M22 23C22 26.3137 19.3137 29 16 29C12.6863 29 10 26.3137 10 23C10 19.6863 12.6863 17 16 17C19.3137 17 22 19.6863 22 23ZM20 23C20 25.2091 18.2091 27 16 27C13.7909 27 12 25.2091 12 23C12 20.7909 13.7909 19 16 19C18.2091 19 20 20.7909 20 23Z" fill="currentColor"/><Path fillRule="evenodd" clipRule="evenodd" d="M16 43C18.4 43 28 30.105 28 23.3077C28 16.5103 22.6274 11 16 11C9.37258 11 4 16.5103 4 23.3077C4 30.105 13.6 43 16 43ZM24.6272 28.4574C25.5248 26.3849 26 24.6019 26 23.3077C26 17.5669 21.4754 13 16 13C10.5246 13 6 17.5669 6 23.3077C6 24.6019 6.47522 26.3849 7.37279 28.4574C8.25298 30.4899 9.46576 32.6347 10.767 34.5979C12.0698 36.5633 13.426 38.2936 14.569 39.5084C15.1447 40.1201 15.6265 40.5585 15.9885 40.8275L16 40.8361L16.0115 40.8275C16.3735 40.5585 16.8553 40.1201 17.431 39.5084C18.574 38.2936 19.9302 36.5633 21.233 34.5979C22.5342 32.6347 23.747 30.4899 24.6272 28.4574ZM16.3339 41.0498L16.3426 41.0532C16.3451 41.0543 16.3464 41.0549 16.3464 41.055C16.3465 41.0552 16.3424 41.0537 16.3339 41.0498Z" fill="currentColor"/><Path fillRule="evenodd" clipRule="evenodd" d="M48 29C51.3137 29 54 26.3137 54 23C54 19.6863 51.3137 17 48 17C44.6863 17 42 19.6863 42 23C42 26.3137 44.6863 29 48 29ZM48 27C50.2091 27 52 25.2091 52 23C52 20.7909 50.2091 19 48 19C45.7909 19 44 20.7909 44 23C44 25.2091 45.7909 27 48 27Z" fill="currentColor"/><Path fillRule="evenodd" clipRule="evenodd" d="M48 43C50.4 43 60 30.105 60 23.3077C60 16.5103 54.6274 11 48 11C41.3726 11 36 16.5103 36 23.3077C36 30.105 45.6 43 48 43ZM56.6272 28.4574C57.5248 26.3849 58 24.6019 58 23.3077C58 17.5669 53.4754 13 48 13C42.5246 13 38 17.5669 38 23.3077C38 24.6019 38.4752 26.3849 39.3728 28.4574C40.253 30.4899 41.4658 32.6347 42.767 34.5979C44.0698 36.5633 45.426 38.2936 46.569 39.5084C47.1447 40.1201 47.6265 40.5585 47.9885 40.8275L48 40.8361L48.0115 40.8275C48.3735 40.5585 48.8553 40.1201 49.431 39.5084C50.574 38.2936 51.9302 36.5633 53.233 34.5979C54.5342 32.6347 55.747 30.4899 56.6272 28.4574Z" fill="currentColor"/>\<Path fillRule="evenodd" clipRule="evenodd" d="M19.8759 50.9924C19.4346 52.7215 17.8666 54 16 54C13.7909 54 12 52.2091 12 50C12 47.7909 13.7909 46 16 46C17.8666 46 19.4346 47.2785 19.8759 49.0076C19.9166 49.0026 19.958 49 20 49H44C44.042 49 44.0834 49.0026 44.1241 49.0076C44.5654 47.2785 46.1334 46 48 46C50.2091 46 52 47.7909 52 50C52 52.2091 50.2091 54 48 54C46.1334 54 44.5654 52.7215 44.1241 50.9924C44.0834 50.9974 44.042 51 44 51H20C19.958 51 19.9166 50.9974 19.8759 50.9924ZM16 52C17.1046 52 18 51.1046 18 50C18 48.8954 17.1046 48 16 48C14.8954 48 14 48.8954 14 50C14 51.1046 14.8954 52 16 52ZM50 50C50 51.1046 49.1046 52 48 52C46.8954 52 46 51.1046 46 50C46 48.8954 46.8954 48 48 48C49.1046 48 50 48.8954 50 50Z" fill="currentColor"/></SVG>

registerBlockType( 'jet-smart-filters/location-distance', {
	title: __( 'Location Distance' ),
	icon: Icon,
	category: 'jet-smart-filters',
	supports: {
		html: false
	},
	attributes: {
		filter_id: {
			type: 'number',
			default: 0,
		},
		content_provider: {
			type: 'string',
			default: 'not-selected',
		},
		apply_type: {
			type: 'string',
			default: 'ajax',
		},
		apply_on: {
			type: 'string',
			default: 'value',
		},
		placeholder: {
			type: 'string',
			default: 'Enter your location...',
		},
		geolocation_placeholder: {
			type: 'string',
			default: 'Your current location',
		},
		query_id: {
			type: 'string',
			default: '',
		},
		distance_units: {
			type: 'string',
			default: 'km',
		},
		distance_list: {
			type: 'string',
			default: '5,10,25,50,100',
		},
	},
	className: 'jet-smart-filters-location-distance',
	edit: class extends wp.element.Component {

		getOtptionsFromObject( object ) {

			console.log( object );

			const result = [];

			for ( const [ value, label ] of Object.entries( object ) ) {
				result.push( {
					value: value,
					label: label,
				} );
			}

			return result;

		}

		render() {
			
			const props = this.props;

			console.log( props.attributes );

			return [
				props.isSelected && (
					<InspectorControls
						key={'inspector'}
					>
						<PanelBody title={__( 'General' )}>
							<div>
								<h4 style={{margin:'5px 0 0'}}>Please note!</h4>
								<p style={{ color: '#757575', fontSize: '12px' }}>
									This filter is compatible only with queries from JetEngine Query Builder. ALso you need to set up <a href="https://crocoblock.com/knowledge-base/jetsmartfilters/location-distance-filter-overview/" target="_blank">Geo Query</a> in your query settings to meke filter to work correctly.
								</p>
							</div>
							<SelectControl
								label={ __( 'Select filter' ) }
								value={ props.attributes.filter_id }
								options={ this.getOtptionsFromObject( window.JetSmartFilterBlocksData.filters['location-distance'] ) }
								onChange={ newValue => {
									props.setAttributes({ filter_id: Number(newValue) });
								} }
							/>
							<SelectControl
								label={ __( 'This filter for' ) }
								value={ props.attributes.content_provider }
								options={ this.getOtptionsFromObject( window.JetSmartFilterBlocksData.providers ) }
								onChange={ newValue => {
									props.setAttributes({ content_provider: newValue });
								} }
							/>
							<SelectControl
								label={ __( 'Apply type' ) }
								value={ props.attributes.apply_type }
								options={ [{
									label: __('AJAX'),
									value: 'ajax'
								},
								{
									label: __('Page reload'),
									value: 'reload'
								},
								{
									label: __('Mixed'),
									value: 'mixed'
								}] }
								onChange={ newValue => {
									props.setAttributes({ apply_type: newValue });
								} }
							/>
							<SelectControl
								label={ __('Apply on') }
								value={ props.attributes.apply_on }
								options={ [{
									label: __( 'Value change' ),
									value: 'value'
								},
								{
									label: __( 'Click on apply button' ),
									value: 'submit'
								}] }
								onChange={ newValue => {
									props.setAttributes({ apply_on: newValue });
								}}
							/>
							<TextControl
								type="text"
								label={ __( 'Placeholder' ) }
								help={ __( 'Placeholder text for the location input' ) }
								value={ props.attributes.placeholder }
								onChange={ newValue => {
									props.setAttributes( { placeholder: newValue } );
								} }
							/>
							<TextControl
								type="text"
								label={ __( 'Text for user geolocation control' ) }
								help={ __( 'This text used for User Geolocation icon tooltip and as location input value, when User Geolocation is used' ) }
								value={ props.attributes.geolocation_placeholder }
								onChange={ newValue => {
									props.setAttributes( { geolocation_placeholder: newValue } );
								} }
							/>
							<TextControl
								type="text"
								label={ __( 'Query ID' ) }
								help={ __( 'Set unique query ID if you use multiple blocks of same provider on the page. Same ID you need to set for filtered block.' ) }
								value={ props.attributes.query_id }
								onChange={ newValue => {
									props.setAttributes( { query_id: newValue } );
								} }
							/>
						</PanelBody>
						<PanelBody title={__( 'Distance Control' )}>
							<SelectControl
								label={ __( 'Distance Units' ) }
								value={ props.attributes.distance_units }
								options={ [{
									label: __('Kilometers'),
									value: 'km'
								},
								{
									label: __('Miles'),
									value: 'mi'
								}] }
								onChange={ newValue => {
									props.setAttributes({ distance_units: newValue });
								} }
							/>
							<TextareaControl
								label={ __( 'Distance List' ) }
								help={ __( 'Comma-separated list of distance options numbers' ) }
								value={ props.attributes.distance_list }
								onChange={ newValue => {
									props.setAttributes( { distance_list: newValue } );
								} }
							/>
						</PanelBody>
					</InspectorControls>
				),
				<div class="jet-smart-filters-block-holder">
					<Disabled key={ 'block_render' }>
						<ServerSideRender
							block="jet-smart-filters/location-distance"
							attributes={ props.attributes }
						/>
					</Disabled>
				</div>
			];

		}

	},
	save: props => {
		return null;
	},
} );