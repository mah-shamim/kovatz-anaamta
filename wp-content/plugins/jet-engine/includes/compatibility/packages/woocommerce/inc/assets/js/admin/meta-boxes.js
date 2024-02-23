const JetEngineWCMetaBoxesTypes = [ 'woocommerce_product_data', 'woocommerce_product_variation' ];

window.JetPlugins.hooks.addFilter(
	'jetEngine.metaFields.showConditionsEditor',
	'jetEngineWCMetaBoxes',
	( result ) => {
		if ( 'woocommerce_product_variation' === window.JetEngineMB.generalSettings.object_type ) {
			return false;
		}
		return result;
	}
);

window.JetPlugins.hooks.addFilter(
	'jetEngine.metaFields.fieldConditions',
	'jetEngineWCMetaBoxes',
	( conditions, key ) => {
		if ( 'width' === key || 'allow_custom' === key || 'save_custom' === key ) {
			conditions.push( {
				'input': window.JetEngineMB.generalSettings.object_type,
				'compare': 'not_in',
				'value': JetEngineWCMetaBoxesTypes,
			} );
		}

		if ( 'is_required' === key ) {
			conditions.push( {
				'input': window.JetEngineMB.generalSettings.object_type,
				'compare': 'not_equal',
				'value': 'woocommerce_product_variation',
			} );
		}

		return conditions;
	}
);

window.JetPlugins.hooks.addFilter(
	'jetEngine.metaFields.allowedObjectTypes',
	'jetEngineWCMetaBoxes',
	( objectTypes ) => {
		if ( JetEngineWCMetaBoxesTypes.includes( window.JetEngineMB.generalSettings.object_type ) ) {
			return [ objectTypes[ 0 ] ];
		} else {
			return objectTypes;
		}
	}
);

window.JetPlugins.hooks.addFilter(
	'jetEngine.metaFields.allowedFieldTypes',
	'jetEngineWCMetaBoxes',
	( fieldTypes ) => {
		const isWCMetaBox = JetEngineWCMetaBoxesTypes.includes( window.JetEngineMB.generalSettings.object_type );
		const disallowedTypes = [ 'html', 'map', 'repeater', 'wysiwyg' ];

		return fieldTypes.filter( ( fieldType ) => {
			if ( 'woocommerce_product_variation' === window.JetEngineMB.generalSettings.object_type && 'iconpicker' === fieldType.value ) {
				return false;
			}

			return ! (isWCMetaBox && disallowedTypes.includes( fieldType.value ));
		} );
	}
);