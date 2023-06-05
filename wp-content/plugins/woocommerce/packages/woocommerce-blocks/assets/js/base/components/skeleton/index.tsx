/**
 * Internal dependencies
 */
import './style.scss';

export interface SkeletonProps {
	numberOfLines?: number;
}

export const Skeleton = ( {
	numberOfLines = 1,
}: SkeletonProps ): JSX.Element => {
	const skeletonLines = Array( numberOfLines ).fill(
		<span
			className="wc-block-components-skeleton-text-line"
			aria-hidden="true"
		/>
	);
	return (
		<div className="wc-block-components-skeleton">{ skeletonLines }</div>
	);
};
