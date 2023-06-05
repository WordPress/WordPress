const FilterPlaceholders = ( {
	displayStyle,
	isLoading,
}: {
	isLoading: boolean;
	displayStyle: string;
} ) => {
	if ( ! isLoading ) {
		return null;
	}

	return (
		<>
			{ [ ...Array( displayStyle === 'list' ? 2 : 3 ) ].map( ( x, i ) => (
				<li
					className={
						displayStyle === 'list'
							? 'show-loading-state-list'
							: 'show-loading-state-chips'
					}
					key={ i }
				>
					<span className="show-loading-state__inner" />
				</li>
			) ) }
		</>
	);
};

export default FilterPlaceholders;
