export type StockStatus = 'instock' | 'outofstock' | 'onbackorder';

export type StockStatusOptions = {
	[ key in StockStatus ]: string;
};
