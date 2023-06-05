jQuery(function( $ ) {

	function showTooltip( x, y, contents ) {
		$( '<div class="chart-tooltip">' + contents + '</div>' ).css( {
			top: y - 16,
			left: x + 20
		}).appendTo( 'body' ).fadeIn( 200 );
	}

	var prev_data_index = null;
	var prev_series_index = null;

	$( '.chart-placeholder' ).on( 'plothover', function ( event, pos, item ) {
		if ( item ) {
			if ( prev_data_index !== item.dataIndex || prev_series_index !== item.seriesIndex ) {
				prev_data_index   = item.dataIndex;
				prev_series_index = item.seriesIndex;

				$( '.chart-tooltip' ).remove();

				if ( item.series.points.show || item.series.enable_tooltip ) {

					var y = item.series.data[item.dataIndex][1],
						tooltip_content = '';

					if ( item.series.prepend_label ) {
						tooltip_content = tooltip_content + item.series.label + ': ';
					}

					if ( item.series.prepend_tooltip ) {
						tooltip_content = tooltip_content + item.series.prepend_tooltip;
					}

					tooltip_content = tooltip_content + y;

					if ( item.series.append_tooltip ) {
						tooltip_content = tooltip_content + item.series.append_tooltip;
					}

					if ( item.series.pie.show ) {
						showTooltip( pos.pageX, pos.pageY, tooltip_content );
					} else {
						showTooltip( item.pageX, item.pageY, tooltip_content );
					}
				}
			}
		} else {
			$( '.chart-tooltip' ).remove();
			prev_data_index = null;
		}
	});

	$( '.wc_sparkline.bars' ).each( function() {
		var chart_data = $( this ).data( 'sparkline' );

		var options = {
			grid: {
				show: false
			}
		};

		// main series
		var series = [{
			data: chart_data,
			color: $( this ).data( 'color' ),
			bars: {
				fillColor: $( this ).data( 'color' ),
				fill: true,
				show: true,
				lineWidth: 1,
				barWidth: $( this ).data( 'barwidth' ),
				align: 'center'
			},
			shadowSize: 0
		}];

		// draw the sparkline
		$.plot( $( this ), series, options );
	});

	$( '.wc_sparkline.lines' ).each( function() {
		var chart_data = $( this ).data( 'sparkline' );

		var options = {
			grid: {
				show: false
			}
		};

		// main series
		var series = [{
			data: chart_data,
			color: $( this ).data( 'color' ),
			lines: {
				fill: false,
				show: true,
				lineWidth: 1,
				align: 'center'
			},
			shadowSize: 0
		}];

		// draw the sparkline
		$.plot( $( this ), series, options );
	});

	var dates = $( '.range_datepicker' ).datepicker({
		changeMonth: true,
		changeYear: true,
		defaultDate: '',
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 1,
		minDate: '-20Y',
		maxDate: '+1D',
		showButtonPanel: true,
		showOn: 'focus',
		buttonImageOnly: true,
		onSelect: function() {
			var option = $( this ).is( '.from' ) ? 'minDate' : 'maxDate',
				date   = $( this ).datepicker( 'getDate' );

			dates.not( this ).datepicker( 'option', option, date );
		}
	});

	var a = document.createElement( 'a' );

	if ( typeof a.download === 'undefined' ) {
		$( '.export_csv' ).hide();
	}

	// Export
	$( '.export_csv' ).on( 'click', function() {
		var exclude_series = $( this ).data( 'exclude_series' ) || '';
		exclude_series    = exclude_series.toString();
		exclude_series    = exclude_series.split( ',' );
		var xaxes_label   = $( this ).data( 'xaxes' );
		var groupby       = $( this ) .data( 'groupby' );
		var index_type    = $( this ).data( 'index_type' );
		var export_format = $( this ).data( 'export' );
		var csv_data      = '';
		var s, series_data, d;

		if ( 'table' === export_format ) {

			$( this ).offsetParent().find( 'thead tr,tbody tr' ).each( function() {
				$( this ).find( 'th, td' ).each( function() {
					var value = $( this ).text();
					value = value.replace( '[?]', '' ).replace( '#', '' );
					csv_data += '"' + value + '"' + ',';
				});
				csv_data = csv_data.substring( 0, csv_data.length - 1 );
				csv_data += '\n';
			});

			$( this ).offsetParent().find( 'tfoot tr' ).each( function() {
				$( this ).find( 'th, td' ).each( function() {
					var value = $( this ).text();
					value = value.replace( '[?]', '' ).replace( '#', '' );
					csv_data += '"' + value + '"' + ',';
					if ( $( this ).attr( 'colspan' ) > 0 ) {
						for ( i = 1; i < $(this).attr('colspan'); i++ ) {
							csv_data += '"",';
						}
					}
				});
				csv_data = csv_data.substring( 0, csv_data.length - 1 );
				csv_data += '\n';
			});

		} else {

			if ( ! window.main_chart ) {
				return false;
			}

			var the_series = window.main_chart.getData();
			var series     = [];
			csv_data      += '"' + xaxes_label + '",';

			$.each( the_series, function( index, value ) {
				if ( ! exclude_series || $.inArray( index.toString(), exclude_series ) === -1 ) {
					series.push( value );
				}
			});

			// CSV Headers
			for ( s = 0; s < series.length; ++s ) {
				csv_data += '"' + series[s].label + '",';
			}

			csv_data = csv_data.substring( 0, csv_data.length - 1 );
			csv_data += '\n';

			// Get x axis values
			var xaxis = {};

			for ( s = 0; s < series.length; ++s ) {
				series_data = series[s].data;
				for ( d = 0; d < series_data.length; ++d ) {
					xaxis[series_data[d][0]] = [];
					// Zero values to start
					for ( var i = 0; i < series.length; ++i ) {
						xaxis[series_data[d][0]].push(0);
					}
				}
			}

			// Add chart data
			for ( s = 0; s < series.length; ++s ) {
				series_data = series[s].data;
				for ( d = 0; d < series_data.length; ++d ) {
					xaxis[series_data[d][0]][s] = series_data[d][1];
				}
			}

			// Loop data and output to csv string
			$.each( xaxis, function( index, value ) {
				var date = new Date( parseInt( index, 10 ) );

				if ( 'none' === index_type ) {
					csv_data += '"' + index + '",';
				} else {
					if ( groupby === 'day' ) {
						csv_data += '"' +
							date.getUTCFullYear() +
							'-' +
							parseInt( date.getUTCMonth() + 1, 10 ) +
							'-' +
							date.getUTCDate() +
							'",';
					} else {
						csv_data += '"' + date.getUTCFullYear() + '-' + parseInt( date.getUTCMonth() + 1, 10 ) + '",';
					}
				}

				for ( var d = 0; d < value.length; ++d ) {
					var val = value[d];

					if ( Math.round( val ) !== val ) {
						val = parseFloat( val );
						val = val.toFixed( 2 );
					}

					csv_data += '"' + val + '",';
				}
				csv_data = csv_data.substring( 0, csv_data.length - 1 );
				csv_data += '\n';
			} );
		}

		csv_data = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent( csv_data );
		// Set data as href and return
		$( this ).attr( 'href', csv_data );
		return true;
	});
});
