/**
 * Internal dependencies
 */
import type { RadioControlOptionLayout } from './types';

const OptionLayout = ( {
	label,
	secondaryLabel,
	description,
	secondaryDescription,
	id,
}: RadioControlOptionLayout ): JSX.Element => {
	return (
		<div className="wc-block-components-radio-control__option-layout">
			<div className="wc-block-components-radio-control__label-group">
				{ label && (
					<span
						id={ id && `${ id }__label` }
						className="wc-block-components-radio-control__label"
					>
						{ label }
					</span>
				) }
				{ secondaryLabel && (
					<span
						id={ id && `${ id }__secondary-label` }
						className="wc-block-components-radio-control__secondary-label"
					>
						{ secondaryLabel }
					</span>
				) }
			</div>
			{ ( description || secondaryDescription ) && (
				<div className="wc-block-components-radio-control__description-group">
					{ description && (
						<span
							id={ id && `${ id }__description` }
							className="wc-block-components-radio-control__description"
						>
							{ description }
						</span>
					) }
					{ secondaryDescription && (
						<span
							id={ id && `${ id }__secondary-description` }
							className="wc-block-components-radio-control__secondary-description"
						>
							{ secondaryDescription }
						</span>
					) }
				</div>
			) }
		</div>
	);
};

export default OptionLayout;
