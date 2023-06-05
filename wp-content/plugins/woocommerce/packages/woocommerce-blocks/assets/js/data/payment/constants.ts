export const STORE_KEY = 'wc/store/payment';

export enum STATUS {
	IDLE = 'idle',
	EXPRESS_STARTED = 'express_started',
	PROCESSING = 'processing',
	READY = 'ready',
	ERROR = 'has_error',
}
