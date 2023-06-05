/**
 * External dependencies
 */
import Title from '@woocommerce/base-components/title';

/**
 * Step Heading Component
 */
const FormStepHeading = ( {
	children,
	stepHeadingContent,
}: {
	children: JSX.Element;
	stepHeadingContent?: JSX.Element;
} ): JSX.Element => (
	<div className="wc-block-components-checkout-step__heading">
		<Title
			aria-hidden="true"
			className="wc-block-components-checkout-step__title"
			headingLevel="2"
		>
			{ children }
		</Title>
		{ !! stepHeadingContent && (
			<span className="wc-block-components-checkout-step__heading-content">
				{ stepHeadingContent }
			</span>
		) }
	</div>
);

export default FormStepHeading;
