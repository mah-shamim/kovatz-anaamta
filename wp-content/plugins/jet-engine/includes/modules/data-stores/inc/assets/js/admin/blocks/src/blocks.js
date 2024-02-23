const { __ } = wp.i18n;

const {
	registerBlockType
} = wp.blocks;

const {
	InspectorControls,
	MediaUpload
} = wp.blockEditor;

const {
	PanelColor,
	IconButton,
	TextControl,
	TextareaControl,
	SelectControl,
	ToggleControl,
	PanelBody,
	RangeControl,
	RadioControl,
	CheckboxControl,
	Disabled,
	G,
	Path,
	Circle,
	Rect,
	SVG,
	ColorPalette
} = wp.components;

const {
	serverSideRender: ServerSideRender
} = wp;

const Icon = <SVG width="24" height="24" viewBox="0 0 64 34" fill="none" xmlns="http://www.w3.org/2000/svg"><Path fillRule="evenodd" clipRule="evenodd" d="M12.4693 8.26144C11.7859 8.8 11 9.71032 11 11V23C11 24.2897 11.7859 25.2 12.4693 25.7386C13.1752 26.2949 14.0728 26.7084 15.0036 27.0187C16.8861 27.6462 19.3596 28 22 28C24.6404 28 27.1139 27.6462 28.9964 27.0187C29.9272 26.7084 30.8248 26.2949 31.5307 25.7386C32.2141 25.2 33 24.2897 33 23V11C33 9.71032 32.2141 8.8 31.5307 8.26144C30.8248 7.70512 29.9272 7.29158 28.9964 6.98131C27.1139 6.35381 24.6404 6 22 6C19.3596 6 16.8861 6.35381 15.0036 6.98131C14.0728 7.29158 13.1752 7.70512 12.4693 8.26144ZM28.9964 15.0187C27.1139 15.6462 24.6404 16 22 16C19.3596 16 16.8861 15.6462 15.0036 15.0187C14.2928 14.7818 13.6015 14.4846 13 14.1097V17C13 17.7684 13.8666 18.4692 15.2917 19C16.9396 19.6138 19.3345 20 22 20C24.6655 20 27.0604 19.6138 28.7083 19C28.789 18.97 28.8678 18.9394 28.9448 18.9083C30.2287 18.3897 31 17.7249 31 17V14.1097C30.3985 14.4846 29.7072 14.7818 28.9964 15.0187ZM22 8C26.9706 8 31 9.34315 31 11C31 11.7684 30.1334 12.4692 28.7083 13C27.0604 13.6137 24.6655 14 22 14C19.3345 14 16.9396 13.6137 15.2917 13C15.211 12.97 15.1322 12.9394 15.0552 12.9083C13.7713 12.3897 13 11.7249 13 11C13 9.34315 17.0294 8 22 8ZM31 23V20.1097C30.3985 20.4846 29.7072 20.7818 28.9964 21.0187C27.1139 21.6462 24.6404 22 22 22C19.3596 22 16.8861 21.6462 15.0036 21.0187C14.2928 20.7818 13.6015 20.4846 13 20.1097V23C13 24.6569 17.0294 26 22 26C26.9706 26 31 24.6569 31 23Z" fill="currentColor"/><Path d="M50.7074 21.2929C50.3168 20.9024 49.6837 20.9024 49.2931 21.2929C48.9026 21.6834 48.9026 22.3166 49.2931 22.7071L51.5859 24.9999L49.2931 27.2926C48.9026 27.6832 48.9026 28.3163 49.2931 28.7069C49.6837 29.0974 50.3168 29.0974 50.7074 28.7069L53.0001 26.4141L55.2927 28.7067C55.6833 29.0972 56.3164 29.0972 56.707 28.7067C57.0975 28.3162 57.0975 27.683 56.707 27.2925L54.4143 24.9999L56.707 22.7073C57.0975 22.3167 57.0975 21.6836 56.707 21.293C56.3164 20.9025 55.6833 20.9025 55.2927 21.293L53.0001 23.5857L50.7074 21.2929Z" fill="currentColor"/><Path d="M53 4C53.5523 4 54 4.44772 54 5V8H57C57.5523 8 58 8.44772 58 9C58 9.55228 57.5523 10 57 10H54V13C54 13.5523 53.5523 14 53 14C52.4477 14 52 13.5523 52 13V10H49C48.4477 10 48 9.55228 48 9C48 8.44772 48.4477 8 49 8H52V5C52 4.44772 52.4477 4 53 4Z" fill="currentColor"/><Path fillRule="evenodd" clipRule="evenodd" d="M4 0H60C62.2091 0 64 1.79086 64 4V30C64 32.2091 62.2091 34 60 34H4C1.79086 34 0 32.2091 0 30V4C0 1.79086 1.79086 0 4 0ZM4 2C2.89543 2 2 2.89543 2 4V30C2 31.1046 2.89543 32 4 32H42L42 2H4ZM62 30C62 31.1046 61.1046 32 60 32H44V18H62L62 30ZM62 4L62 16L44 16L44 2H60C61.1046 2 62 2.89543 62 4Z" fill="currentColor"/></SVG>;

registerBlockType( 'jet-engine/data-store-button', {
	title: __( 'Data Store Button' ),
	icon: Icon,
	category: 'jet-engine',
	attributes: window.JetEngineListingData.atts.dataStoreButton,
	className: 'jet-data-store-link-wrapper',
	edit: function( props ) {

		const attributes    = props.attributes;
		const storesOptions = window.JetEngineListingData.dataStores;

		function inArray( needle, highstack ) {
			return 0 <= highstack.indexOf( needle );
		}

		return [
			props.isSelected && (
				<InspectorControls
					key={ 'inspector' }
				>
					<PanelBody title={ __( 'General' ) }>

						<SelectControl
							label={__( 'Source' )}
							value={attributes.store}
							options={storesOptions}
							onChange={newValue => {
								props.setAttributes( { store: newValue } );
							}}
						/>
						<TextControl
							type="text"
							label={__( "Label" )}
							value={attributes.label}
							onChange={newValue =>
								props.setAttributes( {
									label: newValue
								} )
							}
						/>
						<div className="jet-media-control components-base-control">
							<div className="components-base-control__label">{__( 'Icon' )}</div>
							{attributes.icon_url && <img src={attributes.icon_url} width="100%" height="auto"/>}
							<MediaUpload
								onSelect={media => {
									props.setAttributes( {
										icon:     media.id,
										icon_url: media.url,
									} );
								}}
								type="image"
								value={attributes.icon}
								render={( { open } ) => (
									<IconButton
										isSecondary
										icon="edit"
										onClick={open}
									>{__( 'Select Icon' )}</IconButton>
								)}
							/>
							{ attributes.icon_url &&
								<IconButton
									onClick={() => {
										props.setAttributes( {
											icon:     0,
											icon_url: '',
										} )
									}}
									isLink
									isDestructive
								>
									{__( 'Remove Icon' )}
								</IconButton>
							}
						</div>
						<SelectControl
							label={__( 'Action after an item added to store' )}
							value={attributes.action_after_added}
							options={[
								{
									value: 'remove_from_store',
									label: __( 'Remove from store' ),
								},
								{
									value: 'switch_status',
									label: __( 'Switch button status' ),
								},
								{
									value: 'hide',
									label: __( 'Hide button' ),
								},
							]}
							onChange={newValue => {
								props.setAttributes( { action_after_added: newValue } );
							}}
						/>
						{inArray( attributes.action_after_added, ['switch_status', 'remove_from_store'] ) &&
							<TextControl
								type="text"
								label={__( "Label after added to store" )}
								value={attributes.added_to_store_label}
								onChange={newValue =>
									props.setAttributes( {
										added_to_store_label: newValue
									} )
								}
							/>
						}
						{inArray( attributes.action_after_added, ['switch_status', 'remove_from_store'] ) &&
							<div className="jet-media-control components-base-control">
								<div className="components-base-control__label">{__( 'Icon after added to store' )}</div>
								{attributes.added_to_store_icon_url && <img src={attributes.added_to_store_icon_url} width="100%" height="auto"/>}
								<MediaUpload
									onSelect={media => {
										props.setAttributes( {
											added_to_store_icon:     media.id,
											added_to_store_icon_url: media.url,
										} );
									}}
									type="image"
									value={attributes.added_to_store_icon}
									render={( { open } ) => (
										<IconButton
											isSecondary
											icon="edit"
											onClick={open}
										>{__( 'Select Icon' )}</IconButton>
									)}
								/>
								{attributes.added_to_store_icon_url &&
									<IconButton
										onClick={() => {
											props.setAttributes( {
												added_to_store_icon:     0,
												added_to_store_icon_url: '',
											} )
										}}
										isLink
										isDestructive
									>
										{__( 'Remove Icon' )}
									</IconButton>
								}
							</div>
						}
						{'switch_status' === attributes.action_after_added &&
							<div>
								<TextControl
									type="text"
									label={__( "URL after added to store" )}
									value={attributes.added_to_store_url}
									onChange={newValue =>
										props.setAttributes( {
											added_to_store_url: newValue
										} )
									}
								/>
								<ToggleControl
									label={__( 'Open in new window' )}
									checked={attributes.open_in_new}
									onChange={() => {
										props.setAttributes( { open_in_new: !attributes.open_in_new } );
									}}
								/>
								<SelectControl
									label={__( 'Add "rel" attr' )}
									value={attributes.rel_attr}
									options={[
										{
											value: '',
											label: __( 'No' ),
										},
										{
											value: 'alternate',
											label: __( 'Alternate' ),
										},
										{
											value: 'author',
											label: __( 'Author' ),
										},
										{
											value: 'bookmark',
											label: __( 'Bookmark' ),
										},
										{
											value: 'external',
											label: __( 'External' ),
										},
										{
											value: 'help',
											label: __( 'Help' ),
										},
										{
											value: 'license',
											label: __( 'License' ),
										},
										{
											value: 'next',
											label: __( 'Next' ),
										},
										{
											value: 'nofollow',
											label: __( 'Nofollow' ),
										},
										{
											value: 'noreferrer',
											label: __( 'Noreferrer' ),
										},
										{
											value: 'noopener',
											label: __( 'Noopener' ),
										},
										{
											value: 'prev',
											label: __( 'Prev' ),
										},
										{
											value: 'search',
											label: __( 'Search' ),
										},
										{
											value: 'tag',
											label: __( 'Tag' ),
										},
									]}
									onChange={newValue => {
										props.setAttributes( { rel_attr: newValue } );
									}}
								/>
							</div>
						}

					</PanelBody>
				</InspectorControls>
			),
			<Disabled key={ 'block_render' }>
				<ServerSideRender
					block="jet-engine/data-store-button"
					attributes={ attributes }
				/>
			</Disabled>
		];
	},
	save: props => {
		return null;
	}
} );
