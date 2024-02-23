const {
		  TextControl,
		  SelectControl,
	  } = wp.components;

const {
		  useState,
		  useEffect,
		  Fragment,
	  } = wp.element;

const { withRequestFields, withLoadingSelect } = JetFBHooks;

const {
		  withSelect,
		  withDispatch,
	  } = wp.data;

const { compose } = wp.compose;

const {
		  addAction,
		  getFormFieldsBlocks,
		  Tools: { withPlaceholder },
	  } = JetFBActions;

function RelationAction( {
							 settings,
							 label,
							 help,
							 source,
							 onChangeSettingObj,
							 requestFields,
						 } ) {

	const [ formFieldsList, setFormFields ] = useState( [] );

	useEffect( () => {
		setFormFields( [ ...getFormFieldsBlocks( [], '--' ), ...requestFields ] );
	}, [] );

	return <Fragment>
		<SelectControl
			label={ label( 'relation' ) }
			labelPosition='side'
			value={ settings.relation }
			onChange={ relation => onChangeSettingObj( { relation } ) }
			options={ withPlaceholder( source.relations ) }
		/>
		<SelectControl
			label={ label( 'parent_id' ) }
			labelPosition='side'
			value={ settings.parent_id }
			onChange={ parent_id => onChangeSettingObj( { parent_id } ) }
			options={ formFieldsList }
		/>
		<SelectControl
			label={ label( 'child_id' ) }
			labelPosition='side'
			value={ settings.child_id }
			onChange={ child_id => onChangeSettingObj( { child_id } ) }
			options={ formFieldsList }
		/>
		<SelectControl
			label={ label( 'context' ) }
			labelPosition='side'
			value={ settings.context }
			onChange={ context => onChangeSettingObj( { context } ) }
			options={ withPlaceholder( source.context_options ) }
		/>
		<SelectControl
			label={ label( 'store_items_type' ) }
			labelPosition='side'
			value={ settings.store_items_type }
			onChange={ store_items_type => onChangeSettingObj( { store_items_type } ) }
			options={ withPlaceholder( source.store_items_type_options ) }
		/>
	</Fragment>;
}

addAction( 'connect_relation_items', compose(
	withSelect( withRequestFields )
)( RelationAction ) );