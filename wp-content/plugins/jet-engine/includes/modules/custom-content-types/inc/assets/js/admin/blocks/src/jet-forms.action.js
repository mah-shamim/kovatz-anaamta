import DynamicInsertedCCTID from './DynamicInsertedCCTID';

const {
	      TextControl,
	      SelectControl,
      } = wp.components;

const {
	      useState,
	      useEffect,
      } = wp.element;

const {
	      addAction,
	      getFormFieldsBlocks,
	      Tools: { withPlaceholder },
	      addComputedField       = () => {}, // since JFB 3.0
	      convertListToFieldsMap = () => [],
      } = JetFBActions;
const {
	      ActionFieldsMap,
	      WrapperRequiredControl,
      } = JetFBComponents;

const {
	      useFields = () => false,
      } = window?.JetFBHooks ?? {};

const { addFilter } = wp.hooks;

addComputedField( DynamicInsertedCCTID );

addFilter( 'jet.fb.preset.editor.custom.condition', 'jet-form-builder',
	function ( isVisible, customCondition, state ) {
		if ( 'cct_query_var' === customCondition ) {

			return (
				'custom_content_type' === state.from && 'query_var' ===
				state.post_from
			);
		}
		return isVisible;
	} );

addAction(
	'insert_custom_content_type',
	function CCTAction( {
		settings,
		label,
		help,
		source,
		onChangeSetting,
		getMapField,
		setMapField,
	} ) {

		const [ cctFields, setCctFields ]       = useState( [] );
		const [ cctFieldsMap, setCctFieldsMap ] = useState( [] );

		const [ isLoading, setLoading ] = useState( false );

		let fields = useFields();

		const [ formFieldsList ] = useState( () => {
			return false === fields
			       ? convertListToFieldsMap( getFormFieldsBlocks() )
			       : convertListToFieldsMap( fields );
		}, [] );

		const fetchTypeFields = function ( type ) {
			if ( !type ) {
				return;
			}
			setLoading( true );

			wp.apiFetch( {
				method: 'get',
				path: source.fetch_path + '?type=' + type,
			} ).then( response => {

				if ( response.success && response.fields ) {
					const typeFields = [];

					for ( var i = 0; i < response.fields.length; i++ ) {

						if ( '_ID' === response.fields[ i ].value ) {
							response.fields[ i ].label += ' (will update the item)';
						}
						typeFields.push( { ...response.fields[ i ] } );
					}

					setCctFields( typeFields );

				}
				else {
					alert( response.notices[ i ].join( '; ' ) + ';' );
				}

				setLoading( false );
			} ).catch( ( e ) => {
				setLoading( false );

				alert( e );
				console.log( e );
			} );
		};

		useEffect( () => {
			fetchTypeFields( settings.type );
		}, [] );

		useEffect( () => {
			if ( !settings.type ) {
				setCctFields( [] );
			}
		}, [ settings.type ] );

		useEffect( () => {
			const cctMap = {};
			cctFields.forEach( field => {
				if ( '_ID' !== field.value ) {
					cctMap[ field.value ] = { label: field.label };
				}
			} );

			setCctFieldsMap( Object.entries( cctMap ) );
		}, [ cctFields ] );

		return <>
			<SelectControl
				label={ label( 'type' ) }
				labelPosition="side"
				value={ settings.type }
				onChange={ newValue => {
					onChangeSetting( newValue, 'type' );
					fetchTypeFields( newValue );
				} }
				options={ withPlaceholder( source.types ) }
			/>
			<SelectControl
				label={ label( 'status' ) }
				labelPosition="side"
				value={ settings.status }
				onChange={ newValue => {
					onChangeSetting( newValue, 'status' );
				} }
				options={ withPlaceholder( source.statuses ) }
			/>
			<div style={ { opacity: isLoading ? '0.5' : '1' } }
			     className="jet-control-full">
				<ActionFieldsMap
					label={ label( 'fields_map' ) }
					fields={ formFieldsList }
					plainHelp={ help( 'fields_map' ) }
				>
					{ ( { fieldId, fieldData, index } ) =>
						<WrapperRequiredControl
							field={ [ fieldId, fieldData ] }
						>
							<SelectControl
								key={ fieldId + index }
								value={ getMapField( { name: fieldId } ) }
								onChange={ value => setMapField(
									{ nameField: fieldId, value } ) }
								options={ withPlaceholder( cctFields ) }
							/>
						</WrapperRequiredControl> }
				</ActionFieldsMap>
				{ 0 < cctFieldsMap.length && <ActionFieldsMap
					label={ label( 'default_fields' ) }
					fields={ cctFieldsMap }
					plainHelp={ help( 'default_fields' ) }
				>
					{ ( { fieldId, fieldData, index } ) =>
						<WrapperRequiredControl
							field={ [ fieldId, fieldData ] }
						>
							<TextControl
								key={ fieldId + index }
								value={ getMapField( {
									source: 'default_fields',
									name: fieldId,
								} ) }
								onChange={ value => setMapField( {
									source: 'default_fields',
									nameField: fieldId,
									value,
								} ) }
							/>
						</WrapperRequiredControl> }
				</ActionFieldsMap> }
			</div>
		</>;
	},
);