export interface ApiResponse< T > {
	body: Record< string, unknown >;
	headers: Headers;
	status: number;
	ok: boolean;
	json: () => Promise< T >;
}

export function assertBatchResponseIsValid(
	response: unknown
): asserts response is {
	responses: ApiResponse< unknown >[];
	headers: Headers;
} {
	if (
		typeof response === 'object' &&
		response !== null &&
		response.hasOwnProperty( 'responses' )
	) {
		return;
	}
	throw new Error( 'Response not valid' );
}

export function assertResponseIsValid< T >(
	response: unknown
): asserts response is ApiResponse< T > {
	if (
		typeof response === 'object' &&
		response !== null &&
		'body' in response &&
		'headers' in response
	) {
		return;
	}
	throw new Error( 'Response not valid' );
}
