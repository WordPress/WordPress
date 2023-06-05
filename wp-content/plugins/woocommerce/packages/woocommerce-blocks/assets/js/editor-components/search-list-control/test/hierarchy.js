/**
 * Internal dependencies
 */
import { buildTermsTree } from '../utils';

const list = [
	{ id: 1, name: 'Apricots', parent: 0 },
	{ id: 2, name: 'Clementine', parent: 0 },
	{ id: 3, name: 'Elderberry', parent: 2 },
	{ id: 4, name: 'Guava', parent: 2 },
	{ id: 5, name: 'Lychee', parent: 3 },
	{ id: 6, name: 'Mulberry', parent: 0 },
	{ id: 7, name: 'Tamarind', parent: 5 },
];

describe( 'buildTermsTree', () => {
	test( 'should return an empty array on empty input', () => {
		const tree = buildTermsTree( [] );
		expect( tree ).toEqual( [] );
	} );

	test( 'should return a flat array when there are no parent relationships', () => {
		const tree = buildTermsTree( [
			{ id: 1, name: 'Apricots', parent: 0 },
			{ id: 2, name: 'Clementine', parent: 0 },
		] );
		expect( tree ).toEqual( [
			{
				id: 1,
				name: 'Apricots',
				parent: 0,
				breadcrumbs: [],
				children: [],
			},
			{
				id: 2,
				name: 'Clementine',
				parent: 0,
				breadcrumbs: [],
				children: [],
			},
		] );
	} );

	test( 'should return a tree of items', () => {
		const tree = buildTermsTree( list );
		expect( tree ).toEqual( [
			{
				id: 1,
				name: 'Apricots',
				parent: 0,
				breadcrumbs: [],
				children: [],
			},
			{
				id: 2,
				name: 'Clementine',
				parent: 0,
				breadcrumbs: [],
				children: [
					{
						id: 3,
						name: 'Elderberry',
						parent: 2,
						breadcrumbs: [ 'Clementine' ],
						children: [
							{
								id: 5,
								name: 'Lychee',
								parent: 3,
								breadcrumbs: [ 'Clementine', 'Elderberry' ],
								children: [
									{
										id: 7,
										name: 'Tamarind',
										parent: 5,
										breadcrumbs: [
											'Clementine',
											'Elderberry',
											'Lychee',
										],
										children: [],
									},
								],
							},
						],
					},
					{
						id: 4,
						name: 'Guava',
						parent: 2,
						breadcrumbs: [ 'Clementine' ],
						children: [],
					},
				],
			},
			{
				id: 6,
				name: 'Mulberry',
				parent: 0,
				breadcrumbs: [],
				children: [],
			},
		] );
	} );

	test( 'should return a tree of items, with orphan categories appended to the end', () => {
		const filteredList = [
			{ id: 1, name: 'Apricots', parent: 0 },
			{ id: 2, name: 'Clementine', parent: 0 },
			{ id: 4, name: 'Guava', parent: 2 },
			{ id: 5, name: 'Lychee', parent: 3 },
			{ id: 6, name: 'Mulberry', parent: 0 },
		];
		const tree = buildTermsTree( filteredList, list );
		expect( tree ).toEqual( [
			{
				id: 1,
				name: 'Apricots',
				parent: 0,
				breadcrumbs: [],
				children: [],
			},
			{
				id: 2,
				name: 'Clementine',
				parent: 0,
				breadcrumbs: [],
				children: [
					{
						id: 4,
						name: 'Guava',
						parent: 2,
						breadcrumbs: [ 'Clementine' ],
						children: [],
					},
				],
			},
			{
				id: 6,
				name: 'Mulberry',
				parent: 0,
				breadcrumbs: [],
				children: [],
			},
			{
				id: 5,
				name: 'Lychee',
				parent: 3,
				breadcrumbs: [ 'Clementine', 'Elderberry' ],
				children: [],
			},
		] );
	} );

	test( 'should return a tree of items, with orphan categories appended to the end, with children of thier own', () => {
		const filteredList = [
			{ id: 1, name: 'Apricots', parent: 0 },
			{ id: 3, name: 'Elderberry', parent: 2 },
			{ id: 4, name: 'Guava', parent: 2 },
			{ id: 5, name: 'Lychee', parent: 3 },
			{ id: 6, name: 'Mulberry', parent: 0 },
		];
		const tree = buildTermsTree( filteredList, list );
		expect( tree ).toEqual( [
			{
				id: 1,
				name: 'Apricots',
				parent: 0,
				breadcrumbs: [],
				children: [],
			},
			{
				id: 6,
				name: 'Mulberry',
				parent: 0,
				breadcrumbs: [],
				children: [],
			},
			{
				id: 3,
				name: 'Elderberry',
				parent: 2,
				breadcrumbs: [ 'Clementine' ],
				children: [
					{
						id: 5,
						name: 'Lychee',
						parent: 3,
						breadcrumbs: [ 'Clementine', 'Elderberry' ],
						children: [],
					},
				],
			},
			{
				id: 4,
				name: 'Guava',
				parent: 2,
				breadcrumbs: [ 'Clementine' ],
				children: [],
			},
		] );
	} );
} );
