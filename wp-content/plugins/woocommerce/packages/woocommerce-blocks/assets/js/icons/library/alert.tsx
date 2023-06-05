/**
 * External dependencies
 */
import { IconProps } from '@wordpress/icons/build-types/icon';
import { SVG } from '@wordpress/primitives';

interface AlertProps {
	status?: 'warning' | 'error' | 'success' | 'info';
	props?: IconProps;
}

const statusToColorMap = {
	warning: '#F0B849',
	error: '#CC1818',
	success: '#46B450',
	info: '#0073AA',
};

const Alert = ( { status = 'warning', ...props }: AlertProps ) => (
	<SVG
		xmlns="http://www.w3.org/2000/svg"
		fill="none"
		viewBox="0 0 24 24"
		{ ...props }
	>
		<path
			d="M12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z"
			stroke={ statusToColorMap[ status ] }
			strokeWidth="1.5"
		/>
		<path d="M13 7H11V13H13V7Z" fill={ statusToColorMap[ status ] } />
		<path d="M13 15H11V17H13V15Z" fill={ statusToColorMap[ status ] } />
	</SVG>
);

export default Alert;
