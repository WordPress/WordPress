/**
 * Internal dependencies
 */
import { generateSummary } from '../utils';

describe( 'Summary Component', () => {
	describe( 'Test the generateSummary utility', () => {
		const testContent =
			'<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><p>Ut enim ad minim veniam, quis <strong>nostrud</strong> exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p><p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';

		it( 'Default', async () => {
			const result = generateSummary( testContent );

			expect( result.trim() ).toEqual(
				'<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore&hellip;</p>'
			);
		} );
		it( 'No max words - return full description', async () => {
			const result = generateSummary( testContent, 100000 );

			expect( result.trim() ).toEqual(
				'<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>\n<p>Ut enim ad minim veniam, quis <strong>nostrud</strong> exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>\n<p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>'
			);
		} );
		it( 'Limit to 3 words', async () => {
			const result = generateSummary( testContent, 3 );

			expect( result.trim() ).toEqual(
				'<p>Lorem ipsum dolor&hellip;</p>'
			);
		} );
		it( 'Limit to 1 word', async () => {
			const result = generateSummary( testContent, 1 );

			expect( result.trim() ).toEqual( '<p>Lorem&hellip;</p>' );
		} );
		it( 'Limit to 15 characters, including spaces.', async () => {
			const result = generateSummary(
				testContent,
				15,
				'characters_including_spaces'
			);

			expect( result.trim() ).toEqual( '<p>Lorem ipsum dol&hellip;</p>' );
		} );
		it( 'Limit to 15 characters, excluding spaces.', async () => {
			const result = generateSummary(
				testContent,
				15,
				'characters_excluding_spaces'
			);

			expect( result.trim() ).toEqual(
				'<p>Lorem ipsum dolor&hellip;</p>'
			);
		} );
	} );
	describe( 'Test the generateSummary utility with HTML tags in strings', () => {
		const testContent =
			'<p>Lorem <strong class="classname">ipsum</strong> dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.</p>';

		it( 'Limit string to 10 characters', async () => {
			const result = generateSummary(
				testContent,
				10,
				'characters_excluding_spaces'
			);

			expect( result.trim() ).toEqual( '<p>Lorem ipsum&hellip;</p>' );
		} );
		it( 'Limit string to 5 words', async () => {
			const result = generateSummary( testContent, 5, 'words' );

			expect( result.trim() ).toEqual(
				'<p>Lorem ipsum dolor sit amet&hellip;</p>'
			);
		} );
		it( 'First paragraph only - tags are not stripped.', async () => {
			const result = generateSummary( testContent, 9999, 'words' );

			expect( result.trim() ).toEqual(
				'<p>Lorem <strong class="classname">ipsum</strong> dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.</p>'
			);
		} );
	} );
	describe( 'Test the generateSummary utility with special chars', () => {
		const testContent =
			'<p>我不知道这是否行得通。</p><p>我是用中文写的说明，因此我们可以测试如何修剪产品摘要中的单词。</p>';

		it( 'Default', async () => {
			const result = generateSummary(
				testContent,
				15,
				'characters_excluding_spaces'
			);

			expect( result.trim() ).toEqual( '<p>我不知道这是否行得通。</p>' );
		} );
		it( 'Limit to 3 words', async () => {
			const result = generateSummary(
				testContent,
				3,
				'characters_excluding_spaces'
			);

			expect( result.trim() ).toEqual( '<p>我不知&hellip;</p>' );
		} );
	} );
} );
