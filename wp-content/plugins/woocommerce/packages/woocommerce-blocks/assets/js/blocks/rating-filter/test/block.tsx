/**
 * External dependencies
 */
import React from '@wordpress/element';
import { render, screen, waitFor, within } from '@testing-library/react';
import * as hooks from '@woocommerce/base-context/hooks';
import userEvent from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import RatingFilterBlock from '../block';
import { Attributes } from '../types';

jest.mock( '@woocommerce/base-context/hooks', () => ( {
	__esModule: true,
	...jest.requireActual( '@woocommerce/base-context/hooks' ),
} ) );

const setWindowUrl = ( { url }: { url: string } ) => {
	global.window = Object.create( window );
	Object.defineProperty( window, 'location', {
		value: {
			href: url,
		},
		writable: true,
	} );
};

const acceptErrorWithDuplicatedKeys = () => {
	// React throws an error about the duplicated `key` in the render tree.
	// This is due to `FormTokenField` is forcefully used with incorrect children type,
	// hence the error is acknowledged and consciously accepted.
	expect( console ).toHaveErrored();
};

const stubCollectionData = () => ( {
	price_range: null,
	attribute_counts: null,
	rating_counts: [
		{ rating: 2, count: 5 },
		{ rating: 4, count: 24 },
		{ rating: 5, count: 1 },
	],
	stock_status_counts: null,
} );

type DisplayStyle = 'list' | 'dropdown';
type SelectType = 'single' | 'multiple';
interface SetupParams {
	filterRating: string;
	displayStyle: DisplayStyle;
	selectType: SelectType;
}

const selectors = {
	list: '.wc-block-rating-filter.style-list',
	suggestionsContainer: '.components-form-token-field__suggestions-list',
	chipsContainer: '.components-form-token-field__token',
};

const setup = ( params: SetupParams ) => {
	const url = `http://woo.local/${
		params.filterRating ? '?rating_filter=' + params.filterRating : ''
	}`;
	setWindowUrl( { url } );

	const attributes: Attributes = {
		displayStyle: params.displayStyle || 'list',
		selectType: params.selectType || 'single',
		showCounts: true,
		showFilterButton: true,
		isPreview: false,
	};

	jest.spyOn( hooks, 'useCollectionData' ).mockReturnValue( {
		results: stubCollectionData(),
		isLoading: false,
	} );

	const { container, ...utils } = render(
		<RatingFilterBlock attributes={ attributes } />
	);

	const getList = () => container.querySelector( selectors.list );
	const getDropdown = () => screen.queryByRole( 'combobox' );

	const getChipsContainers = () =>
		container.querySelectorAll( selectors.chipsContainer );
	const getSuggestionsContainer = () =>
		container.querySelector( selectors.suggestionsContainer );

	const getChips = ( value: string ) => {
		const chipsContainers = getChipsContainers();
		const chips = Array.from( chipsContainers ).find( ( chipsContainer ) =>
			chipsContainer
				? within( chipsContainer ).queryByLabelText(
						`Rated ${ value } out of 5`
				  )
				: false
		);

		return chips || null;
	};
	const getSuggestion = ( value: string ) => {
		const suggestionsContainer = getSuggestionsContainer();
		if ( suggestionsContainer ) {
			return within( suggestionsContainer ).queryByLabelText(
				`Rated ${ value } out of 5`
			);
		}
		return null;
	};
	const getCheckbox = ( value: string ) => {
		const checkboxesContainer = getList();
		const checkboxes = checkboxesContainer
			? checkboxesContainer.querySelectorAll( 'input' )
			: [];

		const checkbox = Array.from( checkboxes ).find(
			( input ) => input.id === value
		);

		return checkbox;
	};

	const getRemoveButtonFromChips = ( chips: HTMLElement | null ) =>
		chips
			? within( chips ).getByLabelText( 'Remove rating filter.' )
			: null;

	const getRating2Chips = () => getChips( '2' );
	const getRating4Chips = () => getChips( '4' );
	const getRating5Chips = () => getChips( '5' );

	const getRating2Suggestion = () => getSuggestion( '2' );
	const getRating4Suggestion = () => getSuggestion( '4' );
	const getRating5Suggestion = () => getSuggestion( '5' );

	const getRating2Checkbox = () => getCheckbox( '2' );
	const getRating4Checkbox = () => getCheckbox( '4' );
	const getRating5Checkbox = () => getCheckbox( '5' );

	return {
		...utils,
		container,
		getDropdown,
		getList,
		getRating2Chips,
		getRating4Chips,
		getRating5Chips,
		getRating2Suggestion,
		getRating4Suggestion,
		getRating5Suggestion,
		getRating2Checkbox,
		getRating4Checkbox,
		getRating5Checkbox,
		getRemoveButtonFromChips,
	};
};

interface SetupParams {
	filterRating: string;
	displayStyle: DisplayStyle;
	selectType: SelectType;
}

const setupSingleChoiceList = ( filterRating = '5' ) =>
	setup( {
		filterRating,
		displayStyle: 'list',
		selectType: 'single',
	} );

const setupMultipleChoiceList = ( filterRating = '5' ) =>
	setup( {
		filterRating,
		displayStyle: 'list',
		selectType: 'multiple',
	} );

const setupSingleChoiceDropdown = ( filterRating = '5' ) =>
	setup( {
		filterRating,
		displayStyle: 'dropdown',
		selectType: 'single',
	} );

const setupMultipleChoiceDropdown = ( filterRating = '5' ) =>
	setup( {
		filterRating,
		displayStyle: 'dropdown',
		selectType: 'multiple',
	} );

describe( 'Filter by Rating block', () => {
	describe( 'Single choice Dropdown', () => {
		test( 'renders dropdown', () => {
			const { getDropdown, getList } = setupSingleChoiceDropdown();
			expect( getDropdown() ).toBeInTheDocument();
			expect( getList() ).toBeNull();
		} );

		test( 'renders chips based on URL params', () => {
			const ratingParam = '2';
			const { getRating2Chips, getRating4Chips, getRating5Chips } =
				setupSingleChoiceDropdown( ratingParam );

			expect( getRating2Chips() ).toBeInTheDocument();
			expect( getRating4Chips() ).toBeNull();
			expect( getRating5Chips() ).toBeNull();
		} );

		test( 'replaces chosen option when another one is clicked', () => {
			const ratingParam = '2';
			const {
				getDropdown,
				getRating2Chips,
				getRating4Chips,
				getRating4Suggestion,
			} = setupSingleChoiceDropdown( ratingParam );

			expect( getRating2Chips() ).toBeInTheDocument();
			expect( getRating4Chips() ).toBeNull();

			const dropdown = getDropdown();

			if ( dropdown ) {
				userEvent.click( dropdown );
				acceptErrorWithDuplicatedKeys();
			}

			const rating4Suggestion = getRating4Suggestion();

			if ( rating4Suggestion ) {
				userEvent.click( rating4Suggestion );
			}

			expect( getRating2Chips() ).toBeNull();
			expect( getRating4Chips() ).toBeInTheDocument();
		} );

		test( 'removes the option when the X button is clicked', () => {
			const ratingParam = '4';
			const {
				getRating2Chips,
				getRating4Chips,
				getRating5Chips,
				getRemoveButtonFromChips,
			} = setupMultipleChoiceDropdown( ratingParam );

			expect( getRating2Chips() ).toBeNull();
			expect( getRating4Chips() ).toBeInTheDocument();
			expect( getRating5Chips() ).toBeNull();

			const removeRating4Button = getRemoveButtonFromChips(
				getRating4Chips()
			);

			if ( removeRating4Button ) {
				userEvent.click( removeRating4Button );
				acceptErrorWithDuplicatedKeys();
			}

			expect( getRating2Chips() ).toBeNull();
			expect( getRating4Chips() ).toBeNull();
			expect( getRating5Chips() ).toBeNull();
		} );
	} );

	describe( 'Multiple choice Dropdown', () => {
		test( 'renders dropdown', () => {
			const { getDropdown, getList } = setupMultipleChoiceDropdown();
			expect( getDropdown() ).toBeDefined();
			expect( getList() ).toBeNull();
		} );

		test( 'renders chips based on URL params', () => {
			const ratingParam = '2,4';
			const { getRating2Chips, getRating4Chips, getRating5Chips } =
				setupMultipleChoiceDropdown( ratingParam );

			expect( getRating2Chips() ).toBeInTheDocument();
			expect( getRating4Chips() ).toBeInTheDocument();
			expect( getRating5Chips() ).toBeNull();
		} );

		test( 'adds chosen option to another one that is clicked', () => {
			const ratingParam = '2';
			const {
				getDropdown,
				getRating2Chips,
				getRating4Chips,
				getRating5Chips,
				getRating4Suggestion,
				getRating5Suggestion,
			} = setupMultipleChoiceDropdown( ratingParam );

			expect( getRating2Chips() ).toBeInTheDocument();
			expect( getRating4Chips() ).toBeNull();
			expect( getRating5Chips() ).toBeNull();

			const dropdown = getDropdown();

			if ( dropdown ) {
				userEvent.click( dropdown );
				acceptErrorWithDuplicatedKeys();
			}

			const rating4Suggestion = getRating4Suggestion();

			if ( rating4Suggestion ) {
				userEvent.click( rating4Suggestion );
			}

			expect( getRating2Chips() ).toBeInTheDocument();
			expect( getRating4Chips() ).toBeInTheDocument();
			expect( getRating5Chips() ).toBeNull();

			const rating5Suggestion = getRating5Suggestion();

			if ( rating5Suggestion ) {
				userEvent.click( rating5Suggestion );
			}

			expect( getRating2Chips() ).toBeInTheDocument();
			expect( getRating4Chips() ).toBeInTheDocument();
			expect( getRating5Chips() ).toBeInTheDocument();
		} );

		test( 'removes the option when the X button is clicked', () => {
			const ratingParam = '2,4,5';
			const {
				getRating2Chips,
				getRating4Chips,
				getRating5Chips,
				getRemoveButtonFromChips,
			} = setupMultipleChoiceDropdown( ratingParam );

			expect( getRating2Chips() ).toBeInTheDocument();
			expect( getRating4Chips() ).toBeInTheDocument();
			expect( getRating5Chips() ).toBeInTheDocument();

			const removeRating4Button = getRemoveButtonFromChips(
				getRating4Chips()
			);

			if ( removeRating4Button ) {
				userEvent.click( removeRating4Button );
			}

			expect( getRating2Chips() ).toBeInTheDocument();
			expect( getRating4Chips() ).toBeNull();
			expect( getRating5Chips() ).toBeInTheDocument();
		} );
	} );

	describe( 'Single choice List', () => {
		test( 'renders list', () => {
			const { getDropdown, getList } = setupSingleChoiceList();
			expect( getDropdown() ).toBeNull();
			expect( getList() ).toBeInTheDocument();
		} );

		test( 'renders checked options based on URL params', () => {
			const ratingParam = '4';
			const {
				getRating2Checkbox,
				getRating4Checkbox,
				getRating5Checkbox,
			} = setupSingleChoiceList( ratingParam );

			expect( getRating2Checkbox()?.checked ).toBeFalsy();
			expect( getRating4Checkbox()?.checked ).toBeTruthy();
			expect( getRating5Checkbox()?.checked ).toBeFalsy();
		} );

		test( 'replaces chosen option when another one is clicked', async () => {
			const ratingParam = '2';
			const {
				getRating2Checkbox,
				getRating4Checkbox,
				getRating5Checkbox,
			} = setupSingleChoiceList( ratingParam );

			expect( getRating2Checkbox()?.checked ).toBeTruthy();
			expect( getRating4Checkbox()?.checked ).toBeFalsy();
			expect( getRating5Checkbox()?.checked ).toBeFalsy();

			const rating4checkbox = getRating4Checkbox();

			if ( rating4checkbox ) {
				userEvent.click( rating4checkbox );
			}

			expect( getRating2Checkbox()?.checked ).toBeFalsy();
			expect( getRating4Checkbox()?.checked ).toBeTruthy();
			expect( getRating5Checkbox()?.checked ).toBeFalsy();
		} );

		test( 'removes the option when it is clicked again', async () => {
			const ratingParam = '4';
			const {
				getRating2Checkbox,
				getRating4Checkbox,
				getRating5Checkbox,
			} = setupMultipleChoiceList( ratingParam );

			expect( getRating2Checkbox()?.checked ).toBeFalsy();
			expect( getRating4Checkbox()?.checked ).toBeTruthy();
			expect( getRating5Checkbox()?.checked ).toBeFalsy();

			const rating4checkbox = getRating4Checkbox();

			if ( rating4checkbox ) {
				userEvent.click( rating4checkbox );
			}

			await waitFor( () => {
				expect( getRating2Checkbox()?.checked ).toBeFalsy();
				expect( getRating4Checkbox()?.checked ).toBeFalsy();
				expect( getRating5Checkbox()?.checked ).toBeFalsy();
			} );
		} );
	} );

	describe( 'Multiple choice List', () => {
		test( 'renders list', () => {
			const { getDropdown, getList } = setupMultipleChoiceList();
			expect( getDropdown() ).toBeNull();
			expect( getList() ).toBeInTheDocument();
		} );

		test( 'renders chips based on URL params', () => {
			const ratingParam = '4,5';
			const {
				getRating2Checkbox,
				getRating4Checkbox,
				getRating5Checkbox,
			} = setupMultipleChoiceList( ratingParam );

			expect( getRating2Checkbox()?.checked ).toBeFalsy();
			expect( getRating4Checkbox()?.checked ).toBeTruthy();
			expect( getRating5Checkbox()?.checked ).toBeTruthy();
		} );

		test( 'adds chosen option to another one that is clicked', async () => {
			const ratingParam = '2,4';
			const {
				getRating2Checkbox,
				getRating4Checkbox,
				getRating5Checkbox,
			} = setupMultipleChoiceList( ratingParam );

			expect( getRating2Checkbox()?.checked ).toBeTruthy();
			expect( getRating4Checkbox()?.checked ).toBeTruthy();
			expect( getRating5Checkbox()?.checked ).toBeFalsy();

			const rating5checkbox = getRating5Checkbox();

			if ( rating5checkbox ) {
				userEvent.click( rating5checkbox );
			}

			await waitFor( () => {
				expect( getRating2Checkbox()?.checked ).toBeTruthy();
				expect( getRating4Checkbox()?.checked ).toBeTruthy();
				expect( getRating5Checkbox()?.checked ).toBeTruthy();
			} );
		} );

		test( 'removes the option when it is clicked again', async () => {
			const ratingParam = '2,4';
			const {
				getRating2Checkbox,
				getRating4Checkbox,
				getRating5Checkbox,
			} = setupMultipleChoiceList( ratingParam );

			expect( getRating2Checkbox()?.checked ).toBeTruthy();
			expect( getRating4Checkbox()?.checked ).toBeTruthy();
			expect( getRating5Checkbox()?.checked ).toBeFalsy();

			const rating2checkbox = getRating2Checkbox();

			if ( rating2checkbox ) {
				userEvent.click( rating2checkbox );
			}

			await waitFor( () => {
				expect( getRating2Checkbox()?.checked ).toBeFalsy();
				expect( getRating4Checkbox()?.checked ).toBeTruthy();
				expect( getRating5Checkbox()?.checked ).toBeFalsy();
			} );
		} );
	} );
} );
