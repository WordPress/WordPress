export interface BlockAttributes {
	productId: number;
	align: 'left' | 'center' | 'right';
	isDescendentOfQueryLoop?: boolean | undefined;
}
