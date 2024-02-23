import VisibilityBlockControls from './toolbar';

const {
	addFilter,
	applyFilters
} = wp.hooks;

const {
	Fragment
} = wp.element;


function addToolbarControls( BlockEdit ) {
	return ( props ) => {
		if ( props.isSelected ) {
			return (
				<Fragment>
					<BlockEdit { ...props } />
					<VisibilityBlockControls { ...props } />
				</Fragment>
			);
		}

		return <BlockEdit { ...props } />;
	};
}

addFilter(
	'editor.BlockEdit',
	'jet-engine/add-visibility-controls',
	addToolbarControls,
	900
);

function registerAttributes( settings, name ) {

	return _.assign( {}, settings, {
		attributes: _.assign( {}, settings.attributes, {
			jetDynamicVisibility: {
				type: 'object',
				default: {}
			},
		} ),
		supports: _.assign( {}, settings.supports, {
			jetDynamicVisibility: true
		} ),
	} );
}

addFilter(
	'blocks.registerBlockType',
	'jet-engine/register-visibility-attributes',
	registerAttributes
);
