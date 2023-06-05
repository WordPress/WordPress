/**
 * External dependencies
 */
import { Card, CardBody } from '@wordpress/components';
import styled from '@emotion/styled';
import type { ReactNode } from 'react';

const StyledCard = styled( Card )`
	border-radius: 3px;
`;

const StyledCardBody = styled( CardBody )`
	padding: 24px;

	// increasing the specificity of the styles to override the Gutenberg ones
	&.is-size-medium.is-size-medium {
		padding: 24px;
	}

	h4 {
		margin-top: 0;
		margin-bottom: 1em;
	}

	> * {
		margin-top: 0;
		margin-bottom: 1.5em;

		// fixing the spacing on the inputs and their help text, to ensure it is consistent
		&:last-child {
			margin-bottom: 0;

			> :last-child {
				margin-bottom: 0;
			}
		}
	}

	input,
	select {
		margin: 0;
	}

	// spacing adjustment on "Express checkouts > Show express checkouts on" list
	ul > li:last-child {
		margin-bottom: 0;

		.components-base-control__field {
			margin-bottom: 0;
		}
	}
`;

const SettingsCard = ( {
	children,
	...props
}: {
	children: ReactNode;
} ): JSX.Element => (
	<StyledCard>
		<StyledCardBody { ...props }>{ children }</StyledCardBody>
	</StyledCard>
);

export default SettingsCard;
