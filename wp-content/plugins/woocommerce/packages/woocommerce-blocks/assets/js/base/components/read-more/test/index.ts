/**
 * Internal dependencies
 */
import { truncateHtml } from '../utils';
const shortContent =
	'<p>Lorem ipsum dolor sit amet, <strong>consectetur.</strong>.</p>';

const longContent =
	'<p>Lorem ipsum dolor sit amet, <strong>consectetur adipiscing elit. Nullam a condimentum diam.</strong> Donec finibus enim eros, et lobortis magna varius quis. Nulla lacinia tellus ac neque aliquet, in porttitor metus interdum. Maecenas vestibulum nisi et auctor vestibulum. Maecenas vehicula, lacus et pellentesque tempor, orci nulla mattis purus, id porttitor augue magna et metus. Aenean hendrerit aliquet massa ac convallis. Mauris vestibulum neque in condimentum porttitor. Donec viverra, orci a accumsan vehicula, dui massa lobortis lorem, et cursus est purus pulvinar elit. Vestibulum vitae tincidunt ex, ut vulputate nisi.</p>' +
	'<p>Morbi tristique iaculis felis, sed porta urna tincidunt vitae. Etiam nisl sem, eleifend non varius quis, placerat a arcu. Donec consectetur nunc at orci fringilla pulvinar. Nam hendrerit tellus in est aliquet varius id in diam. Donec eu ullamcorper ante. Ut ultricies, felis vel sodales aliquet, nibh massa vestibulum ipsum, sed dignissim mi nunc eget lacus. Curabitur mattis placerat magna a aliquam. Nullam diam elit, cursus nec erat ullamcorper, tempor eleifend mauris. Nunc placerat nunc ut enim ornare tempus. Fusce porta molestie ante eget faucibus. Fusce eu lectus sit amet diam auctor lacinia et in diam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris eu lacus lobortis, faucibus est vel, pulvinar odio. Duis feugiat tortor quis dui euismod varius.</p>';

describe( 'ReadMore Component', () => {
	describe( 'Test the truncateHtml function', () => {
		it( 'Truncate long HTML content to length of 10', () => {
			const truncatedContent = truncateHtml( longContent, 10 );

			expect( truncatedContent ).toEqual( '<p>Lorem ipsum...</p>' );
		} );
		it( 'Truncate long HTML content, but avoid cutting off HTML tags.', () => {
			const truncatedContent = truncateHtml( longContent, 40 );

			expect( truncatedContent ).toEqual(
				'<p>Lorem ipsum dolor sit amet, <strong>consectetur...</strong></p>'
			);
		} );
		it( 'No need to truncate short HTML content.', () => {
			const truncatedContent = truncateHtml( shortContent, 100 );

			expect( truncatedContent ).toEqual(
				'<p>Lorem ipsum dolor sit amet, <strong>consectetur.</strong>.</p>'
			);
		} );
	} );
} );
