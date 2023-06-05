/**
 * External dependencies
 */
import { Modal } from '@wordpress/components';
import styled from '@emotion/styled';

const StyledModal = styled( Modal )`
	max-width: 600px;
	border-radius: 4px;
	@media ( min-width: 600px ) {
		min-width: 560px;
	}

	.components-modal__header {
		padding: 12px 24px;
		border-bottom: 1px solid #e0e0e0;
		position: relative;
		height: auto;
		width: auto;
		margin: 0 -24px 16px;

		@media ( max-width: 599px ) {
			button {
				display: none;
			}
		}
	}

	.components-modal__content {
		margin: 0;
		padding: 0 24px;

		@media ( max-width: 599px ) {
			display: flex;
			flex-direction: column;

			hr:last-of-type {
				margin-top: auto;
			}
		}

		.components-base-control {
			label {
				margin-top: 8px;
				text-transform: none !important;
			}
		}
	}
`;

const StyledFooter = styled.div`
	display: flex;
	justify-content: flex-end;
	border-top: 1px solid #e0e0e0;
	margin: 24px -24px 0;
	padding: 24px;

	> * {
		&:not( :first-of-type ) {
			margin-left: 8px;
		}
	}

	.button-link-delete {
		margin-right: auto;
		color: #d63638;
	}
`;

const SettingsModal = ( {
	children,
	actions,
	title,
	onRequestClose,
	...props
}: {
	children: React.ReactNode;
	actions: React.ReactNode;
	title: string;
	onRequestClose: () => void;
} ): JSX.Element => (
	<StyledModal title={ title } onRequestClose={ onRequestClose } { ...props }>
		{ children }
		<StyledFooter>{ actions }</StyledFooter>
	</StyledModal>
);

export default SettingsModal;
