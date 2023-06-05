/**
 * External dependencies
 */
import classNames from 'classnames';
import { CheckboxControl } from '@wordpress/components';
import { useCallback } from '@wordpress/element';
import { arrayDifferenceBy, arrayUnionBy } from '@woocommerce/utils';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import type {
	renderItemArgs,
	SearchListItem as SearchListItemProps,
} from './types';
import { getHighlightedName, getBreadcrumbsForDisplay } from './utils';

const Count = ( { label }: { label: string | React.ReactNode | number } ) => {
	return (
		<span className="woocommerce-search-list__item-count">{ label }</span>
	);
};

const ItemLabel = ( props: { item: SearchListItemProps; search: string } ) => {
	const { item, search } = props;
	const hasBreadcrumbs = item.breadcrumbs && item.breadcrumbs.length;

	return (
		<span className="woocommerce-search-list__item-label">
			{ hasBreadcrumbs ? (
				<span className="woocommerce-search-list__item-prefix">
					{ getBreadcrumbsForDisplay( item.breadcrumbs ) }
				</span>
			) : null }
			<span className="woocommerce-search-list__item-name">
				{ getHighlightedName( decodeEntities( item.name ), search ) }
			</span>
		</span>
	);
};

export const SearchListItem = ( {
	countLabel,
	className,
	depth = 0,
	controlId = '',
	item,
	isSelected,
	isSingle,
	onSelect,
	search = '',
	selected,
	useExpandedPanelId,
	...props
}: renderItemArgs ): JSX.Element => {
	const [ expandedPanelId, setExpandedPanelId ] = useExpandedPanelId;
	const showCount =
		countLabel !== undefined &&
		countLabel !== null &&
		item.count !== undefined &&
		item.count !== null;
	const hasBreadcrumbs = !! item.breadcrumbs?.length;
	const hasChildren = !! item.children?.length;
	const isExpanded = expandedPanelId === item.id;
	const classes = classNames(
		[ 'woocommerce-search-list__item', `depth-${ depth }`, className ],
		{
			'has-breadcrumbs': hasBreadcrumbs,
			'has-children': hasChildren,
			'has-count': showCount,
			'is-expanded': isExpanded,
			'is-radio-button': isSingle,
		}
	);

	const name = props.name || `search-list-item-${ controlId }`;
	const id = `${ name }-${ item.id }`;

	const togglePanel = useCallback( () => {
		setExpandedPanelId( isExpanded ? -1 : Number( item.id ) );
	}, [ isExpanded, item.id, setExpandedPanelId ] );

	return hasChildren ? (
		<div
			className={ classes }
			onClick={ togglePanel }
			onKeyDown={ ( e ) =>
				e.key === 'Enter' || e.key === ' ' ? togglePanel() : null
			}
			role="treeitem"
			tabIndex={ 0 }
		>
			{ isSingle ? (
				<>
					<input
						type="radio"
						id={ id }
						name={ name }
						value={ item.value }
						onChange={ onSelect( item ) }
						onClick={ ( e ) => e.stopPropagation() }
						checked={ isSelected }
						className="woocommerce-search-list__item-input"
						{ ...props }
					/>

					<ItemLabel item={ item } search={ search } />

					{ showCount ? (
						<Count label={ countLabel || item.count } />
					) : null }
				</>
			) : (
				<>
					<CheckboxControl
						className="woocommerce-search-list__item-input"
						checked={ isSelected }
						{ ...( ! isSelected &&
						// We know that `item.children` is not `undefined` because
						// we are here only if `hasChildren` is `true`.
						( item.children as SearchListItemProps[] ).some(
							( child ) =>
								selected.find(
									( selectedItem ) =>
										selectedItem.id === child.id
								)
						)
							? { indeterminate: true }
							: {} ) }
						label={ getHighlightedName( item.name, search ) }
						onChange={ () => {
							if ( isSelected ) {
								onSelect(
									arrayDifferenceBy(
										selected,
										item.children as SearchListItemProps[],
										'id'
									)
								)();
							} else {
								onSelect(
									arrayUnionBy(
										selected,
										item.children as SearchListItemProps[],
										'id'
									)
								)();
							}
						} }
						onClick={ ( e ) => e.stopPropagation() }
					/>

					{ showCount ? (
						<Count label={ countLabel || item.count } />
					) : null }
				</>
			) }
		</div>
	) : (
		<label htmlFor={ id } className={ classes }>
			{ isSingle ? (
				<input
					type="radio"
					id={ id }
					name={ name }
					value={ item.value }
					onChange={ onSelect( item ) }
					checked={ isSelected }
					className="woocommerce-search-list__item-input"
					{ ...props }
				></input>
			) : (
				<input
					type="checkbox"
					id={ id }
					name={ name }
					value={ item.value }
					onChange={ onSelect( item ) }
					checked={ isSelected }
					className="woocommerce-search-list__item-input"
					{ ...props }
				></input>
			) }

			<ItemLabel item={ item } search={ search } />

			{ showCount ? <Count label={ countLabel || item.count } /> : null }
		</label>
	);
};

export default SearchListItem;
