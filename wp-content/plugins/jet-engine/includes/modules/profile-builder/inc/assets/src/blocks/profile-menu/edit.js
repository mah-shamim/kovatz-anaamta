const { __ } = wp.i18n;

const {
	ToggleControl,
	PanelBody,
	SelectControl,
	Disabled
} = wp.components;

const {
	serverSideRender: ServerSideRender
} = wp;

const {
	Fragment
} = wp.element;

const {
	assign
} = lodash;

const {
	InspectorControls,
} = wp.blockEditor;

const Edit = function( props ) {

	const {
		className,
		attributes,
		setAttributes,
	} = props;

	const layoutOptions = [
		{
			value: 'horizontal',
			label: __( 'Horizontal' ),
		},
		{
			value: 'vertical',
			label: __( 'Vertical' ),
		}
	];

	return <Fragment>
		{ props.isSelected && <InspectorControls
			key={ className + '-inspector' }
		>
			<PanelBody
				title={ __( 'General', 'jet-engine' ) }
			>
				<SelectControl
					label={ __( 'Context' ) }
					value={ attributes.menu_context }
					options={ [
						{
							value: 'account_page',
							label: __( 'Account' ),
						},
						{
							value: 'user_page',
							label: __( 'Single User Page' ),
						}
					] }
					onChange={ newValue => {
						setAttributes({
							menu_context: newValue,
						});
					} }
				/>
				{ 'account_page' === attributes.menu_context && window.JetEngineProfileBlocksConfig.account_roles && 0 < window.JetEngineProfileBlocksConfig.account_roles.length && 
					<SelectControl
						label={ __( 'Show menu for the role' ) }
						value={ attributes.account_roles }
						options={ window.JetEngineProfileBlocksConfig.account_roles }
						onChange={ newValue => {
							setAttributes({
								account_roles: newValue,
							});
						} }
					/>
				}
				{ 'user_page' === attributes.menu_context && window.JetEngineProfileBlocksConfig.user_roles && 0 < window.JetEngineProfileBlocksConfig.user_roles.length && 
					<SelectControl
						label={ __( 'Show menu for the role' ) }
						value={ attributes.user_roles }
						options={ window.JetEngineProfileBlocksConfig.user_roles }
						onChange={ newValue => {
							setAttributes({
								user_roles: newValue,
							});
						} }
					/>
				}
				<ToggleControl
					label={ __( 'Add subpage slug to the first page URL' ) }
					help={ __( 'By default, the subpage slug is not added to the URL of the menu\'s first page. If you enable this option subpage slug will be added to all menu page URLs, including the first one' ) }
					checked={ attributes.add_main_slug }
					onChange={ () => {
						props.setAttributes({ add_main_slug: ! attributes.add_main_slug });
					} }
				/>
				<SelectControl
					label={ __( 'Layout' ) }
					value={ attributes.menu_layout }
					options={ layoutOptions }
					onChange={ newValue => {
						setAttributes({
							menu_layout: newValue,
						});
					} }
				/>
				<SelectControl
					label={ __( 'Layout Tablet' ) }
					value={ attributes.menu_layout_tablet }
					options={ layoutOptions }
					onChange={ newValue => {
						setAttributes({
							menu_layout_tablet: newValue,
						});
					} }
				/>
				<SelectControl
					label={ __( 'Layout Mobile' ) }
					value={ attributes.menu_layout_mobile }
					options={ layoutOptions }
					onChange={ newValue => {
						setAttributes({
							menu_layout_mobile: newValue,
						});
					} }
				/>
			</PanelBody>
		</InspectorControls> }
		<Disabled>
			<ServerSideRender
				block="jet-engine/profile-menu"
				attributes={ attributes }
				urlQueryArgs={ {} }
			/>
		</Disabled>
	</Fragment>;
}

export default Edit;
