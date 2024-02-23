import EditMeta from 'related-items-table-edit-meta';
import RowActions from 'related-items-table-row-actions';

const {
	Button
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

class RelatedItemsTable extends Component {

	constructor( props ) {
		super( props );
	}

	hasMeta() {
		return ( this.props.metaFields && 0 < this.props.metaFields.length );
	}

	render() {

		const relatedItemsList = this.props.items.map( ( item ) => {

			let row = item.columns.map( ( col, index ) => {
				return ( <td key={ 'col_' + index } dangerouslySetInnerHTML={ { __html: col } }></td> );
			} );

			return ( <tr key={ 'row_' + item._ID }>
				{ row }
				{ this.hasMeta() && <td className="rel-meta">
					<EditMeta
						{ ...this.props }
						relatedObjectID={ item.related_id }
					/>
				</td> }
				<td>
					<RowActions
						actions={ item.actions }
						relID={ this.props.relID }
						relatedObjectID={ item.related_id }
						relatedObjectType={ this.props.controlObjectType }
						relatedObjectName={ this.props.controlObjectName }
						currentObjectID={ this.props.currentObjectID }
						isParentProcessed={ this.props.isParentProcessed }
						onUpdate={ ( items ) => {
							this.props.onUpdate( items );
						} }
					/>
				</td>
			</tr> );
		} );

		const columnsHeadings = this.props.columns.map( ( item ) => {
			return ( <th key={ 'rel-heading-' + item.key } className={ 'rel-' + item.key }>{ item.label }</th> );
		} );

		return ( <div className="jet-engine-rels__table-wrap">
			<table className="wp-list-table widefat fixed striped table-view-list jet-engine-rels__table">
				<thead>
					<tr>{ columnsHeadings }</tr>
				</thead>
				<tbody>
					{ 0 < this.props.items.length && relatedItemsList }
					{ ! this.props.items.length && <tr>
						<td colSpan={ this.props.columns.length }>{ '--' }</td>
					</tr> }
				</tbody>
				<tfoot>
					<tr>{ columnsHeadings }</tr>
				</tfoot>
			</table>
		</div> );
	}

}

export default RelatedItemsTable;
