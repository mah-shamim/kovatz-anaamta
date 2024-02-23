import MediaControl from 'media-control';
import CheckboxGroupControl from 'checkbox-group-control';

const {
	Button,
	TextControl,
	TextareaControl,
	RadioControl,
	SelectControl
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

const {
	assign
} = window.lodash;

class FieldsList extends Component {

	constructor( props ) {

		super( props );

		if ( this.props.values ) {
			this.state = assign( {}, this.props.values );
		} else {
			this.state = {};
		}

	}

	onChange( fieldName, value ) {

		this.setState( ( state ) => {
			const newState = assign( {}, state, { [ fieldName ]: value } );
			this.props.onChange( newState );
			return newState;
		} );

	}

	fieldTemplate( field ) {

		const commonProps = {
			key: 'field_' + field.type + field.name,
			label: field.title,
			help: field.description ? <span dangerouslySetInnerHTML={{ __html: field.description }}/> : '',
			value: this.state[ field.name ],
			onChange: ( newVal ) => {
				this.onChange( field.name, newVal );
			}
		}

		const unfilteredProps = assign( {}, field );

		if ( unfilteredProps.class ) {
			unfilteredProps.className = unfilteredProps.class;
			delete( unfilteredProps.class )
		}

		let type = field.type;

		if ( 'text' === type && field.input_type ) {
			type = field.input_type;
		}

		let groupLayout;

		if ( -1 !== ['checkbox', 'radio'].indexOf( field.type ) ) {
			groupLayout = field.layout || 'vertical';
		}

		switch ( type ) {

			case 'select':
				return <SelectControl
					{ ...commonProps }
					options={ field.options }
					multiple={ field.multiple }
				/>;

			case 'radio':

				if ( parseInt( commonProps.value, 10 ) == commonProps.value ) {
					commonProps.selected = parseInt( commonProps.value, 10 );
				} else {
					commonProps.selected = commonProps.value;
				}

				delete( commonProps.value );

				return <RadioControl
					className={ 'je-radio-group-' + groupLayout }
					{ ...commonProps }
					options={ field.options }
				/>;

			case 'checkbox':
				return <CheckboxGroupControl
					{ ...commonProps }
					options={ field.options }
					layout={ groupLayout }
				/>;

			case 'media':

				if ( field.multi_upload ) {
					return <p><i>{ 'Gallery field type is not supported' }</i></p>;
				} else {
					return <MediaControl
						{ ...commonProps }
						{ ...field }
					/>;
				}

			case 'date':
				return <TextControl
					{ ...commonProps }
					type="date"
				/>;

			case 'time':
				return <TextControl
					{ ...commonProps }
					type="time"
				/>;

			case 'textarea':
			case 'wysiwyg':
				return <TextareaControl
					{ ...commonProps }
				/>;

			case 'datetime-local':
				return <TextControl
					{ ...commonProps }
					type="datetime-local"
				/>;

			case 'switcher':
			case 'iconpicker':
			case 'repeater':
			case 'colorpicker':
			case 'posts':
			case 'html':
				return <p><i>{ type + ' field type is not supported' }</i></p>;

			default:
				return <TextControl
					{ ...commonProps }
				/>;
		}

	}

	render() {

		return ( <Fragment>
			{ this.props.fields.map( ( field ) => {
				return this.fieldTemplate( field );
			} ) }
		</Fragment> );
	}

}

export default FieldsList;
