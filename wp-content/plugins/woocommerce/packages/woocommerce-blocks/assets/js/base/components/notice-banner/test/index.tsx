/**
 * External dependencies
 */
import { render, fireEvent, findByText } from '@testing-library/react';

/**
 * Internal dependencies
 */
import NoticeBanner from '../index';

describe( 'NoticeBanner', () => {
	test( 'renders without errors when all required props are provided', async () => {
		const { container } = render(
			<NoticeBanner status="error">This is an error message</NoticeBanner>
		);
		expect(
			await findByText( container, 'This is an error message' )
		).toBeInTheDocument();
	} );

	test( 'displays the notice message correctly', () => {
		const message = 'This is a test message';
		const { getByText } = render(
			<NoticeBanner status="success" spokenMessage="Speech">
				{ message }
			</NoticeBanner>
		);
		const messageElement = getByText( message );
		expect( messageElement ).toBeInTheDocument();
	} );

	test( 'displays the correct status for the notice', () => {
		const { container } = render(
			<NoticeBanner status="warning">
				This is a warning message
			</NoticeBanner>
		);
		expect( container.querySelector( '.is-warning' ) ).toBeInTheDocument();
	} );

	test( 'displays the summary correctly when provided', () => {
		const summaryText = '4 new messages';
		const { getByText } = render(
			<NoticeBanner status="default" summary={ summaryText }>
				This is a test message
			</NoticeBanner>
		);
		const summaryElement = getByText( summaryText );
		expect( summaryElement ).toBeInTheDocument();
	} );

	test( 'can be dismissed when isDismissible prop is true', () => {
		const onRemoveMock = jest.fn();
		const { getByRole } = render(
			<NoticeBanner
				status="success"
				isDismissible
				onRemove={ onRemoveMock }
			>
				This is a success message
			</NoticeBanner>
		);
		const closeButton = getByRole( 'button' );
		fireEvent.click( closeButton );
		expect( onRemoveMock ).toHaveBeenCalled();
	} );

	test( 'calls onRemove function when the notice is dismissed', () => {
		const onRemoveMock = jest.fn();
		const { getByRole } = render(
			<NoticeBanner status="info" isDismissible onRemove={ onRemoveMock }>
				This is an informative message
			</NoticeBanner>
		);
		const closeButton = getByRole( 'button' );
		fireEvent.click( closeButton );
		expect( onRemoveMock ).toHaveBeenCalled();
	} );

	test( 'applies the className prop to the notice', () => {
		const customClassName = 'my-custom-class';
		const { container } = render(
			<NoticeBanner status="success" className={ customClassName }>
				This is a success message
			</NoticeBanner>
		);
		const noticeElement = container.firstChild;
		expect( noticeElement ).toHaveClass( customClassName );
	} );

	test( 'does not throw any errors when all props are provided correctly', () => {
		const spyError = jest.spyOn( console, 'error' );
		render(
			<NoticeBanner status="default">This is a test message</NoticeBanner>
		);
		expect( spyError ).not.toHaveBeenCalled(); // Should not print any error/warning messages
		spyError.mockRestore(); // Restore the original mock
	} );
} );
