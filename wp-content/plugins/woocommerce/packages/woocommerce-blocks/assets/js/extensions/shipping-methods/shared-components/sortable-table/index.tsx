/**
 * External dependencies
 */
import styled from '@emotion/styled';
import { Icon, dragHandle } from '@wordpress/icons';
import { useMemo } from '@wordpress/element';
import {
	closestCenter,
	DndContext,
	KeyboardSensor,
	MouseSensor,
	TouchSensor,
	useSensor,
	useSensors,
	DragEndEvent,
	UniqueIdentifier,
} from '@dnd-kit/core';
import { restrictToVerticalAxis } from '@dnd-kit/modifiers';
import {
	SortableContext,
	verticalListSortingStrategy,
	useSortable,
	arrayMove,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { objectHasProp } from '@woocommerce/types';

export interface SortableData extends Record< string, unknown > {
	id: UniqueIdentifier;
}

type ColumnProps = {
	name: string;
	label: string;
	width?: string;
	align?: string;
	renderCallback?: ( row: SortableData ) => JSX.Element;
};

const TableRow = ( {
	children,
	id,
}: {
	children: JSX.Element[];
	id: UniqueIdentifier;
} ): JSX.Element => {
	const { attributes, listeners, transform, transition, setNodeRef } =
		useSortable( {
			id,
		} );
	const style = {
		transform: CSS.Transform.toString( transform ),
		transition,
	};
	return (
		<tr ref={ setNodeRef } style={ style }>
			<>
				<td style={ { width: '1%' } }>
					<Icon
						icon={ dragHandle }
						size={ 14 }
						className={ 'sortable-table__handle' }
						{ ...attributes }
						{ ...listeners }
					/>
				</td>
				{ children }
			</>
		</tr>
	);
};

const StyledTable = styled.table`
	background: #fff;
	border: 0;
	border-radius: 3px;
	box-shadow: 0 0 0 1px rgb( 0 0 0 / 10% );
	border-spacing: 0;
	width: 100%;
	clear: both;
	margin: 0;
	font-size: 14px;

	.align-left {
		text-align: left;
		.components-flex {
			justify-content: flex-start;
			gap: 0;
		}
	}
	.align-right {
		text-align: right;
		.components-flex {
			justify-content: flex-end;
			gap: 0;
		}
	}
	.align-center {
		text-align: center;
		> * {
			margin: 0 auto;
		}
		.components-flex {
			display: block;
		}
	}

	.sortable-table__handle {
		cursor: move;
	}

	th {
		position: relative;
		color: #2c3338;
		text-align: left;
		vertical-align: middle;
		vertical-align: top;
		word-wrap: break-word;
	}

	tbody {
		td {
			vertical-align: top;
			margin-bottom: 9px;
		}
	}

	tfoot {
		td {
			text-align: left;
			vertical-align: middle;
		}
	}

	thead,
	tfoot,
	tbody {
		td,
		th {
			border-top: 1px solid rgb( 0 0 0 / 10% );
			border-bottom: 1px solid rgb( 0 0 0 / 10% );
			padding: 16px 0 16px 24px;
			line-height: 1.5;

			&:last-child {
				padding-right: 24px;
			}

			> svg,
			> .components-base-control {
				margin: 3px 0;
			}
		}
	}

	thead th {
		border-top: 0;
	}

	tfoot td {
		border-bottom: 0;
	}
`;

export const SortableTable = ( {
	columns,
	data,
	setData,
	className,
	footerContent: FooterContent,
	placeholder,
}: {
	columns: ColumnProps[];
	data: SortableData[];
	setData: ( data: SortableData[] ) => void;
	className?: string;
	placeholder?: string | ( () => JSX.Element );
	footerContent?: () => JSX.Element;
} ): JSX.Element => {
	const items = useMemo( () => data.map( ( { id } ) => id ), [ data ] );

	const sensors = useSensors(
		useSensor( MouseSensor, {} ),
		useSensor( TouchSensor, {} ),
		useSensor( KeyboardSensor, {} )
	);

	function handleDragEnd( event: DragEndEvent ) {
		const { active, over } = event;

		if ( active !== null && over !== null && active?.id !== over?.id ) {
			const newData = arrayMove(
				data,
				items.indexOf( active.id ),
				items.indexOf( over.id )
			);
			setData( newData );
		}
	}

	const getColumnProps = ( column: ColumnProps, parentClassName: string ) => {
		const align = column?.align || 'left';
		const width = column?.width || 'auto';

		return {
			className: `${ parentClassName }-${ column.name } align-${ align }`,
			style: { width },
		};
	};

	return (
		<DndContext
			sensors={ sensors }
			onDragEnd={ handleDragEnd }
			collisionDetection={ closestCenter }
			modifiers={ [ restrictToVerticalAxis ] }
		>
			<StyledTable className={ `${ className } sortable-table` }>
				<thead>
					<tr>
						{ columns.map( ( column, index ) => (
							<th
								key={ column.name }
								{ ...getColumnProps(
									column,
									`sortable-table__column`
								) }
								colSpan={ index === 0 ? 2 : 1 }
							>
								{ column.label }
							</th>
						) ) }
					</tr>
				</thead>
				{ FooterContent && (
					<tfoot>
						<tr>
							<td colSpan={ columns.length + 1 }>
								<FooterContent />
							</td>
						</tr>
					</tfoot>
				) }
				<tbody>
					<SortableContext
						items={ items }
						strategy={ verticalListSortingStrategy }
					>
						{ !! data.length ? (
							data.map(
								( row ) =>
									row && (
										<TableRow
											key={ row.id }
											id={ row.id }
											className={ className }
										>
											{ columns.map( ( column ) => (
												<td
													key={ `${ row.id }-${ column.name }` }
													{ ...getColumnProps(
														column,
														`sortable-table__column`
													) }
												>
													{ column.renderCallback ? (
														column.renderCallback(
															row
														)
													) : (
														<>
															{ objectHasProp(
																row,
																column.name
															) &&
																row[
																	column.name
																] }
														</>
													) }
												</td>
											) ) }
										</TableRow>
									)
							)
						) : (
							<tr>
								<td colSpan={ columns.length + 1 }>
									{ placeholder }
								</td>
							</tr>
						) }
					</SortableContext>
				</tbody>
			</StyledTable>
		</DndContext>
	);
};

export default SortableTable;
