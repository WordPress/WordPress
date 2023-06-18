/**
 * External dependencies
 */
import { fireEvent, render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import { SearchListControl } from '../';

const noop = () => {};

const SELECTORS = {
	listItems: '.woocommerce-search-list__list > li',
	searchInput: '.components-text-control__input[type="search"]',
};

const list = [
	{ id: 1, name: 'Apricots' },
	{ id: 2, name: 'Clementine' },
	{ id: 3, name: 'Elderberry' },
	{ id: 4, name: 'Guava' },
	{ id: 5, name: 'Lychee' },
	{ id: 6, name: 'Mulberry' },
];

const hierarchicalList = [
	{ id: 1, name: 'Apricots', parent: 0 },
	{ id: 2, name: 'Clementine', parent: 1 },
	{ id: 3, name: 'Elderberry', parent: 1 },
	{ id: 4, name: 'Guava', parent: 3 },
	{ id: 5, name: 'Lychee', parent: 0 },
	{ id: 6, name: 'Mulberry', parent: 0 },
];

describe( 'SearchListControl', () => {
	test( 'should render a search box and list of options', () => {
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				selected={ [] }
				onChange={ noop }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box and list of options with a custom className', () => {
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				className="test-search"
				list={ list }
				selected={ [] }
				onChange={ noop }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box, a list of options, and 1 selected item', () => {
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				selected={ [ list[ 1 ] ] }
				onChange={ noop }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box, a list of options, and 2 selected item', () => {
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				selected={ [ list[ 1 ], list[ 3 ] ] }
				onChange={ noop }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box and no options', () => {
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ [] }
				selected={ [] }
				onChange={ noop }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box with a search term, and only matching options', () => {
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				search="berry"
				selected={ [] }
				onChange={ noop }
				debouncedSpeak={ noop }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box with a search term, and only matching options, regardless of case sensitivity', () => {
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				selected={ [] }
				onChange={ noop }
				debouncedSpeak={ noop }
			/>
		);

		fireEvent.change(
			component.container.querySelector( SELECTORS.searchInput ),
			{ target: { value: 'BeRrY' } }
		);

		expect( component ).toMatchSnapshot();

		const $listItems = component.container.querySelectorAll(
			SELECTORS.listItems
		);

		expect( $listItems ).toHaveLength( 2 );
	} );

	// @see https://github.com/woocommerce/woocommerce-blocks/issues/6524
	test( "should render search results in their original case regardless of user's input case", () => {
		const EXPECTED = [ 'Elderberry', 'Mulberry' ];

		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				selected={ [] }
				onChange={ noop }
				debouncedSpeak={ noop }
			/>
		);

		fireEvent.change(
			component.container.querySelector( SELECTORS.searchInput ),
			{ target: { value: 'BeRrY' } }
		);

		const listItems = Array.from(
			component.container.querySelectorAll( SELECTORS.listItems )
		).map( ( $el ) => $el.textContent );

		expect( listItems ).toEqual( expect.arrayContaining( EXPECTED ) );
	} );

	test( 'should render a search box with a search term, and no matching options', () => {
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				search="no matches"
				selected={ [] }
				onChange={ noop }
				debouncedSpeak={ noop }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box and list of options, with a custom search input message', () => {
		const messages = { search: 'Testing search label' };
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				selected={ [] }
				onChange={ noop }
				messages={ messages }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box and list of options, with a custom render callback for each item', () => {
		const renderItem = ( { item } ) => (
			<div key={ item.id }>{ item.name }!</div>
		); // eslint-disable-line
		const component = render(
			<SearchListControl
				instanceId={ 1 }
				list={ list }
				selected={ [] }
				onChange={ noop }
				renderItem={ renderItem }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );

	test( 'should render a search box and list of hierarchical options', () => {
		const component = render(
			<SearchListControl
				isCompact
				isHierarchical
				instanceId={ 1 }
				isSingle={ false }
				list={ hierarchicalList }
				onChange={ noop }
				selected={ [] }
				type={ 'text' }
			/>
		);
		expect( component ).toMatchSnapshot();
	} );
} );
