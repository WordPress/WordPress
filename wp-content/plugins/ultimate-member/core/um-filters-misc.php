<?php

	/***
	***	@formats numbers nicely
	***/
	add_filter('um_pretty_number_formatting', 'um_pretty_number_formatting');
	function um_pretty_number_formatting( $count ) {
		$count = (int)$count;
		return number_format( $count );
	}