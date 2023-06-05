/**
 * External dependencies
 */
import { SVG } from '@wordpress/primitives';

const checkPayment = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
		<g fill="none" fillRule="evenodd">
			<path d="M0 0h24v24H0z" />
			<path
				fill="#000"
				fillRule="nonzero"
				d="M17.3 8v1c1 .2 1.4.9 1.4 1.7h-1c0-.6-.3-1-1-1-.8 0-1.3.4-1.3.9 0 .4.3.6 1.4 1 1 .2 2 .6 2 1.9 0 .9-.6 1.4-1.5 1.5v1H16v-1c-.9-.1-1.6-.7-1.7-1.7h1c0 .6.4 1 1.3 1 1 0 1.2-.5 1.2-.8 0-.4-.2-.8-1.3-1.1-1.3-.3-2.1-.8-2.1-1.8 0-.9.7-1.5 1.6-1.6V8h1.3zM12 10v1H6v-1h6zm2-2v1H6V8h8zM2 4v16h20V4H2zm2 14V6h16v12H4z"
			/>
			<path
				stroke="#000"
				strokeLinecap="round"
				d="M6 16c2.6 0 3.9-3 1.7-3-2 0-1 3 1.5 3 1 0 1-.8 2.8-.8"
			/>
		</g>
	</SVG>
);

export default checkPayment;
