import './action';

const { addFilter } = wp.hooks;

addFilter( 'jet.fb.preset.editor.custom.condition', 'jet-form-builder', function( isVisible, customCondition, state ) {
	if ( 'relation_query_var' === customCondition ) {

		return (
			'connect_relation_items' === state.from
			&& [ 'query_var', 'object_var' ].includes( state.rel_object_from )
		);
	}
	return isVisible;
} )