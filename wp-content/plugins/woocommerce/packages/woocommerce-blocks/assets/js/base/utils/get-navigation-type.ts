/**
 * Returns the navigation type for the page load.
 */
export const getNavigationType = () => {
	if (
		window.performance &&
		window.performance.getEntriesByType( 'navigation' ).length
	) {
		return (
			window.performance.getEntriesByType(
				'navigation'
			)[ 0 ] as PerformanceNavigationTiming
		 ).type;
	}
	return '';
};

export default getNavigationType;
