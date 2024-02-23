const {
	      BaseComputedField = Object,
      } = JetFBComponents;

const {
	      sprintf,
	      __,
      } = wp.i18n;

const getCCTType = action => {
	const { insert_custom_content_type = {} } = action?.settings;

	return insert_custom_content_type.type;
};

function DynamicInsertedCCTID() {
	BaseComputedField.call( this );

	this.getSupportedActions = function () {
		return [ 'insert_custom_content_type' ];
	};

	this.isSupported = function ( action ) {
		return (
			BaseComputedField.prototype.isSupported.call( this, action ) &&
			getCCTType( action )
		);
	};

	this.getName = function () {
		const lastPart = this.hasInList ? `_${ this.action.id }` : '';

		return `inserted_cct_${ getCCTType( this.action ) }` + lastPart;
	};

	this.getHelp = function () {
		return sprintf(
			__(
				'A computed field from the <b>Insert/Update Custom Content Type Item (%s)</b> action.',
				'jet-form-builder',
			),
			this.action.id,
		);
	};
}

DynamicInsertedCCTID.prototype = Object.create( BaseComputedField.prototype );

export default DynamicInsertedCCTID;