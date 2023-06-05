export interface BlockAttributes {
	// The product ID.
	productId: number;
	// CSS Class name for the component.
	className?: string | undefined;
	// Whether or not to display a link to the product page.
	showProductLink: boolean;
	// Whether or not to display the on sale badge.
	showSaleBadge: boolean;
	// How should the sale badge be aligned if displayed.
	saleBadgeAlign: 'left' | 'center' | 'right';
	// Size of image to use.
	imageSizing: 'full-size' | 'cropped';
	// Whether or not be a children of Query Loop Block.
	isDescendentOfQueryLoop: boolean;
}
