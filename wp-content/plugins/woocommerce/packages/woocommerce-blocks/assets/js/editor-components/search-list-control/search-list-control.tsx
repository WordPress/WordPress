/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	Button,
	FormTokenField,
	Spinner,
	TextControl,
	withSpokenMessages,
} from '@wordpress/components';
import {
	useState,
	useMemo,
	useEffect,
	useCallback,
	Fragment,
} from '@wordpress/element';
import { Icon, info } from '@wordpress/icons';
import classnames from 'classnames';
import { useInstanceId } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { getFilteredList, defaultMessages } from './utils';
import SearchListItem from './item';
import Tag from '../tag';
import type {
	SearchListItem as SearchListItemProps,
	SearchListControlProps,
	SearchListMessages,
	renderItemArgs,
	ListItemsProps,
	SearchListItemsContainerProps,
} from './types';

const defaultRenderListItem = ( args: renderItemArgs ): JSX.Element => {
	return <SearchListItem { ...args } />;
};

const ListItems = ( props: ListItemsProps ): JSX.Element | null => {
	const {
		list,
		selected,
		renderItem,
		depth = 0,
		onSelect,
		instanceId,
		isSingle,
		search,
		useExpandedPanelId,
	} = props;

	const [ expandedPanelId ] = useExpandedPanelId;

	if ( ! list ) {
		return null;
	}
	return (
		<>
			{ list.map( ( item ) => {
				const isSelected =
					item.children?.length && ! isSingle
						? item.children.every( ( { id } ) =>
								selected.find(
									( selectedItem ) => selectedItem.id === id
								)
						  )
						: !! selected.find( ( { id } ) => id === item.id );
				const isExpanded =
					item.children?.length && expandedPanelId === item.id;

				return (
					<Fragment key={ item.id }>
						<li>
							{ renderItem( {
								item,
								isSelected,
								onSelect,
								isSingle,
								selected,
								search,
								depth,
								useExpandedPanelId,
								controlId: instanceId,
							} ) }
						</li>
						{ isExpanded ? (
							<ListItems
								{ ...props }
								list={ item.children as SearchListItemProps[] }
								depth={ depth + 1 }
							/>
						) : null }
					</Fragment>
				);
			} ) }
		</>
	);
};

const SelectedListItems = ( {
	isLoading,
	isSingle,
	selected,
	messages,
	onChange,
	onRemove,
}: SearchListControlProps & {
	messages: SearchListMessages;
	onRemove: ( itemId: string | number ) => () => void;
} ) => {
	if ( isLoading || isSingle || ! selected ) {
		return null;
	}
	const selectedCount = selected.length;
	return (
		<div className="woocommerce-search-list__selected">
			<div className="woocommerce-search-list__selected-header">
				<strong>{ messages.selected( selectedCount ) }</strong>
				{ selectedCount > 0 ? (
					<Button
						isLink
						isDestructive
						onClick={ () => onChange( [] ) }
						aria-label={ messages.clear }
					>
						{ __( 'Clear all', 'woo-gutenberg-products-block' ) }
					</Button>
				) : null }
			</div>
			{ selectedCount > 0 ? (
				<ul>
					{ selected.map( ( item, i ) => (
						<li key={ i }>
							<Tag
								label={ item.name }
								id={ item.id }
								remove={ onRemove }
							/>
						</li>
					) ) }
				</ul>
			) : null }
		</div>
	);
};

const ListItemsContainer = ( {
	filteredList,
	search,
	onSelect,
	instanceId,
	useExpandedPanelId,
	...props
}: SearchListItemsContainerProps ) => {
	const { messages, renderItem, selected, isSingle } = props;
	const renderItemCallback = renderItem || defaultRenderListItem;

	if ( filteredList.length === 0 ) {
		return (
			<div className="woocommerce-search-list__list is-not-found">
				<span className="woocommerce-search-list__not-found-icon">
					<Icon icon={ info } />
				</span>
				<span className="woocommerce-search-list__not-found-text">
					{ search
						? // eslint-disable-next-line @wordpress/valid-sprintf
						  sprintf( messages.noResults, search )
						: messages.noItems }
				</span>
			</div>
		);
	}

	return (
		<ul className="woocommerce-search-list__list">
			<ListItems
				useExpandedPanelId={ useExpandedPanelId }
				list={ filteredList }
				selected={ selected }
				renderItem={ renderItemCallback }
				onSelect={ onSelect }
				instanceId={ instanceId }
				isSingle={ isSingle }
				search={ search }
			/>
		</ul>
	);
};

/**
 * Component to display a searchable, selectable list of items.
 */
export const SearchListControl = ( props: SearchListControlProps ) => {
	const {
		className = '',
		isCompact,
		isHierarchical,
		isLoading,
		isSingle,
		list,
		messages: customMessages = defaultMessages,
		onChange,
		onSearch,
		selected,
		type = 'text',
		debouncedSpeak,
	} = props;

	const [ search, setSearch ] = useState( '' );
	const useExpandedPanelId = useState< number >( -1 );
	const instanceId = useInstanceId( SearchListControl );
	const messages = useMemo(
		() => ( { ...defaultMessages, ...customMessages } ),
		[ customMessages ]
	);
	const filteredList = useMemo( () => {
		return getFilteredList( list, search, isHierarchical );
	}, [ list, search, isHierarchical ] );

	useEffect( () => {
		if ( debouncedSpeak ) {
			debouncedSpeak( messages.updated );
		}
	}, [ debouncedSpeak, messages ] );

	useEffect( () => {
		if ( typeof onSearch === 'function' ) {
			onSearch( search );
		}
	}, [ search, onSearch ] );

	const onRemove = useCallback(
		( itemId: string | number ) => () => {
			if ( isSingle ) {
				onChange( [] );
			}
			const i = selected.findIndex(
				( { id: selectedId } ) => selectedId === itemId
			);
			onChange( [
				...selected.slice( 0, i ),
				...selected.slice( i + 1 ),
			] );
		},
		[ isSingle, selected, onChange ]
	);

	const onSelect = useCallback(
		( item: SearchListItemProps | SearchListItemProps[] ) => () => {
			if ( Array.isArray( item ) ) {
				onChange( item );
				return;
			}

			if ( selected.findIndex( ( { id } ) => id === item.id ) !== -1 ) {
				onRemove( item.id )();
				return;
			}
			if ( isSingle ) {
				onChange( [ item ] );
			} else {
				onChange( [ ...selected, item ] );
			}
		},
		[ isSingle, onRemove, onChange, selected ]
	);

	const onRemoveToken = useCallback(
		( tokens: Array< SearchListItemProps & { value: string } > ) => {
			const [ removedItem ] = selected.filter(
				( item ) => ! tokens.find( ( token ) => item.id === token.id )
			);

			onRemove( removedItem.id )();
		},
		[ onRemove, selected ]
	);

	return (
		<div
			className={ classnames( 'woocommerce-search-list', className, {
				'is-compact': isCompact,
				'is-loading': isLoading,
				'is-token': type === 'token',
			} ) }
		>
			{ type === 'text' && (
				<SelectedListItems
					{ ...props }
					onRemove={ onRemove }
					messages={ messages }
				/>
			) }
			<div className="woocommerce-search-list__search">
				{ type === 'text' ? (
					<TextControl
						label={ messages.search }
						type="search"
						value={ search }
						onChange={ ( value ) => setSearch( value ) }
					/>
				) : (
					<FormTokenField
						disabled={ isLoading }
						label={ messages.search }
						onChange={ onRemoveToken }
						onInputChange={ ( value ) => setSearch( value ) }
						suggestions={ [] }
						// eslint-disable-next-line @typescript-eslint/ban-ts-comment
						// @ts-ignore - Ignoring because `__experimentalValidateInput` is not yet in the type definitions.
						__experimentalValidateInput={ () => false }
						value={
							isLoading
								? [
										__(
											'Loading…',
											'woo-gutenberg-products-block'
										),
								  ]
								: selected.map( ( token ) => ( {
										...token,
										value: token.name,
								  } ) )
						}
						__experimentalShowHowTo={ false }
					/>
				) }
			</div>
			{ isLoading ? (
				<div className="woocommerce-search-list__list">
					<Spinner />
				</div>
			) : (
				<ListItemsContainer
					{ ...props }
					search={ search }
					filteredList={ filteredList }
					messages={ messages }
					onSelect={ onSelect }
					instanceId={ instanceId }
					useExpandedPanelId={ useExpandedPanelId }
				/>
			) }
		</div>
	);
};

export default withSpokenMessages( SearchListControl );
