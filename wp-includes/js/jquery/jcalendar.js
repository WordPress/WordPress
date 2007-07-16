/**
 * jCalendar 0.5
 *
 * Some code based on jQuery Date Picker (http://kelvinluck.com/assets/jquery/datePicker/)
 *
 * Copyright (c) 2007 Theodore Serbinski (http://tedserbinski.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
jQuery.jcalendar = function() {
	var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var days = ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'];
	var navLinks = {p:'Prev', n:'Next', t:'Today'};
	var _firstDayOfWeek;
	var _firstDate;
	var _lastDate;
	var _selectedDate;

	var _drawCalendar = function(dateIn, a, day, month, year) {
	  var today = new Date();
	  var d;

		if (dateIn == undefined) {
			// start from this month.
			d = new Date(today.getFullYear(), today.getMonth(), 1);
			year.val(today.getFullYear());
			month.val(today.getMonth()+1);
			day.val(today.getDate());
		}
		else {
			// start from the passed in date
			d = dateIn;
		  d.setDate(1);
		}

		// check that date is within allowed limits
		if ((d.getMonth() < _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear()) || d.getFullYear() < _firstDate.getFullYear()) {
			d = new Date(_firstDate.getFullYear(), _firstDate.getMonth(), 1);
		}
		else if ((d.getMonth() > _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear()) || d.getFullYear() > _lastDate.getFullYear()) {
			d = new Date(_lastDate.getFullYear(), _lastDate.getMonth(), 1);
		}

		var firstMonth = true;
		var firstDate = _firstDate.getDate();

		// create prev and next links
		if (!(d.getMonth() == _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear())) {
			// not in first display month so show a previous link
			firstMonth = false;
			var lastMonth = d.getMonth() == 0 ? new Date(d.getFullYear()-1, 11, 1) : new Date(d.getFullYear(), d.getMonth()-1, 1);
			var prevLink = jQuery('<a href="" class="link-prev">&lsaquo; '+ navLinks.p +'</a>').click(function() {
				jQuery.jcalendar.changeMonth(lastMonth, this, day, month, year);
				return false;
			});
		}

		var finalMonth = true;
		var lastDate = _lastDate.getDate();

		if (!(d.getMonth() == _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear())) {
			// in the last month - no next link
			finalMonth = false;
			var nextMonth = new Date(d.getFullYear(), d.getMonth()+1, 1);
			var nextLink = jQuery('<a href="" class="link-next">'+ navLinks.n +' &rsaquo;</a>').click(function() {
				jQuery.jcalendar.changeMonth(nextMonth, this, day, month, year);
				return false;
			});
		}

		var todayLink = jQuery('<a href="" class="link-today">'+ navLinks.t +'</a>').click(function() {
			day.val(today.getDate());
			jQuery.jcalendar.changeMonth(today, this, day, month, year);
			return false;
		});

    // update the year and month select boxes
  	year.val(d.getFullYear());
  	month.val(d.getMonth()+1);

		var headRow = jQuery("<tr></tr>");
		for (var i=_firstDayOfWeek; i<_firstDayOfWeek+7; i++) {
			var weekday = i%7;
			var wordday = days[weekday];
			headRow.append('<th scope="col" abbr="'+ wordday +'" title="'+ wordday +'" class="'+ (weekday == 0 || weekday == 6 ? 'weekend' : 'weekday') +'">'+ wordday +'</th>');
		}
		headRow = jQuery("<thead></thead>").append(headRow);

		var tBody = jQuery("<tbody></tbody>");
		var lastDay = (new Date(d.getFullYear(), d.getMonth()+1, 0)).getDate();
		var curDay = _firstDayOfWeek - d.getDay();
		if (curDay > 0) curDay -= 7;

		var todayDate = today.getDate();
		var thisMonth = d.getMonth() == today.getMonth() && d.getFullYear() == today.getFullYear();

    // render calendar
		do {
 		  var thisRow = jQuery("<tr></tr>");
  		for (var i=0; i<7; i++) {
  			var weekday = (_firstDayOfWeek + i) % 7;
  			var atts = {'class':(weekday == 0 || weekday == 6 ? 'weekend ' : 'weekday ')};

  			if (curDay < 0 || curDay >= lastDay) {
  				dayStr = ' ';
  			}
  			else if (firstMonth && curDay < firstDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else if (finalMonth && curDay > lastDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else {
  				d.setDate(curDay+1);

  				// attach a click handler to every day to select it if clicked
  				// we use the rel attribute to keep track of the day that is being clicked
  				dayStr = jQuery('<a href="" rel="'+ d +'">'+ (curDay+1) +'</a>').click(function(e) {
            if (_selectedDate) {
               _selectedDate.removeClass('selected');
            }
      			_selectedDate = jQuery(this);
      			_selectedDate.addClass('selected');
            day.val(new Date(_selectedDate.attr('rel')).getDate());
  					return false;
  				});

  				// highlight the current selected day
  				if (day.val() == d.getDate()) {
  				  _selectedDate = dayStr;
  				  _selectedDate.addClass('selected');
  				}
  			}

  			if (thisMonth && curDay+1 == todayDate) {
  				atts['class'] += 'today';
  			}
  			thisRow.append(jQuery("<td></td>").attr(atts).append(dayStr));
  			curDay++;
      }

			tBody.append(thisRow);
		} while (curDay < lastDay);

		jQuery('div.jcalendar').html('<table cellspacing="1"></table><div class="jcalendar-links"></div>');
		jQuery('div.jcalendar table').append(headRow, tBody);
		jQuery('div.jcalendar > div.jcalendar-links').append(prevLink, todayLink, nextLink);
	};

	return {
		show: function(a, day, month, year) {
 			_firstDate = a._startDate;
			_lastDate = a._endDate;
			_firstDayOfWeek = a._firstDayOfWeek;

			// pass in the selected form date if one was set
			var selected;
			if (year.val() > 0 && month.val() > 0 && day.val() > 0) {
			  selected = new Date(year.val(), month.val()-1, day.val());
			}
			else {
			  selected = null;
			}
			_drawCalendar(selected, a, day, month, year);
		},
		changeMonth: function(d, e, day, month, year) {
			_drawCalendar(d, e, day, month, year);
		},
		/**
		* Function: setLanguageStrings
		*
		* Allows you to localise the calendar by passing in relevant text for the english strings in the plugin.
		*
		* Arguments:
		* days		-	Array, e.g. ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
		* months	-	Array, e.g. ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		* navLinks	-	Object, e.g. {p:'Prev', n:'Next', c:'Close', b:'Choose date'}
		**/
		setLanguageStrings: function(aDays, aMonths, aNavLinks) {
			days = aDays;
			months = aMonths;
			navLinks = aNavLinks;
		},
		/**
		* Function: setDateWindow
		*
		* Used internally to set the start and end dates for a given date select
		*
		* Arguments:
		* i			-	The id of the INPUT element this date window is for
		* w			-	The date window - an object containing startDate and endDate properties
		*				e.g. {startDate:'24-11-1981', endDate:'25-12-2012}
		**/
		setDateWindow: function(i, w, year) {
			if (w == undefined) w = {};
			if (w.startDate == undefined) {
				// set the minimum browseable date equal to January of the min year in the select box
				// don't get the first option because that is an empty year

				// note we can't do this: year.find('option:eq(1)').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._startDate = new Date(jQuery(year).find('option:eq(1)').val(), 0, 1);
			}
			else {
  			dateParts = w.startDate.split('-');
  			i._startDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			if (w.endDate == undefined) {
			  // set the maximum browseable date equal to December of the max year in the select box

			  // note we can't do this: year.find('option:last').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._endDate = new Date(jQuery(year).find('option:last').val(), 11, 1);
			}
			else {
  			dateParts = w.endDate.split('-');
  			i._endDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			i._firstDayOfWeek = w.firstDayOfWeek == undefined ? 0 : w.firstDayOfWeek;
		}
	};
}();

jQuery.fn.jcalendar = function(a) {
	this.each(function() {
    var day = jQuery(this).find('select.jcalendar-select-day');
    var month = jQuery(this).find('select.jcalendar-select-month');
    var year = jQuery(this).find('select.jcalendar-select-year');
    jQuery('div.jcalendar-selects').after('<div class="jcalendar"></div>');
		jQuery.jcalendar.setDateWindow(this, a, year);
		jQuery.jcalendar.show(this, day, month, year);

		day.change(function() {
		  // only if a valid day is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), month.val()-1, this.value);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		month.change(function() {
		  // only if a valid month is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), this.value-1, 1);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		year.change(function() {
		  // only if a valid year is selected
		  if (this.value > 0) {
  		  d = new Date(this.value, month.val()-1, 1);
    	  jQuery.jcalendar.changeMonth(d, a, day, month, year);
    	}
		});

	});
	return this;
};
/**
 * jCalendar 0.5
 *
 * Some code based on jQuery Date Picker (http://kelvinluck.com/assets/jquery/datePicker/)
 *
 * Copyright (c) 2007 Theodore Serbinski (http://tedserbinski.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
jQuery.jcalendar = function() {
	var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var days = ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'];
	var navLinks = {p:'Prev', n:'Next', t:'Today'};
	var _firstDayOfWeek;
	var _firstDate;
	var _lastDate;
	var _selectedDate;

	var _drawCalendar = function(dateIn, a, day, month, year) {
	  var today = new Date();
	  var d;

		if (dateIn == undefined) {
			// start from this month.
			d = new Date(today.getFullYear(), today.getMonth(), 1);
			year.val(today.getFullYear());
			month.val(today.getMonth()+1);
			day.val(today.getDate());
		}
		else {
			// start from the passed in date
			d = dateIn;
		  d.setDate(1);
		}

		// check that date is within allowed limits
		if ((d.getMonth() < _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear()) || d.getFullYear() < _firstDate.getFullYear()) {
			d = new Date(_firstDate.getFullYear(), _firstDate.getMonth(), 1);
		}
		else if ((d.getMonth() > _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear()) || d.getFullYear() > _lastDate.getFullYear()) {
			d = new Date(_lastDate.getFullYear(), _lastDate.getMonth(), 1);
		}

		var firstMonth = true;
		var firstDate = _firstDate.getDate();

		// create prev and next links
		if (!(d.getMonth() == _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear())) {
			// not in first display month so show a previous link
			firstMonth = false;
			var lastMonth = d.getMonth() == 0 ? new Date(d.getFullYear()-1, 11, 1) : new Date(d.getFullYear(), d.getMonth()-1, 1);
			var prevLink = jQuery('<a href="" class="link-prev">&lsaquo; '+ navLinks.p +'</a>').click(function() {
				jQuery.jcalendar.changeMonth(lastMonth, this, day, month, year);
				return false;
			});
		}

		var finalMonth = true;
		var lastDate = _lastDate.getDate();

		if (!(d.getMonth() == _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear())) {
			// in the last month - no next link
			finalMonth = false;
			var nextMonth = new Date(d.getFullYear(), d.getMonth()+1, 1);
			var nextLink = jQuery('<a href="" class="link-next">'+ navLinks.n +' &rsaquo;</a>').click(function() {
				jQuery.jcalendar.changeMonth(nextMonth, this, day, month, year);
				return false;
			});
		}

		var todayLink = jQuery('<a href="" class="link-today">'+ navLinks.t +'</a>').click(function() {
			day.val(today.getDate());
			jQuery.jcalendar.changeMonth(today, this, day, month, year);
			return false;
		});

    // update the year and month select boxes
  	year.val(d.getFullYear());
  	month.val(d.getMonth()+1);

		var headRow = jQuery("<tr></tr>");
		for (var i=_firstDayOfWeek; i<_firstDayOfWeek+7; i++) {
			var weekday = i%7;
			var wordday = days[weekday];
			headRow.append('<th scope="col" abbr="'+ wordday +'" title="'+ wordday +'" class="'+ (weekday == 0 || weekday == 6 ? 'weekend' : 'weekday') +'">'+ wordday +'</th>');
		}
		headRow = jQuery("<thead></thead>").append(headRow);

		var tBody = jQuery("<tbody></tbody>");
		var lastDay = (new Date(d.getFullYear(), d.getMonth()+1, 0)).getDate();
		var curDay = _firstDayOfWeek - d.getDay();
		if (curDay > 0) curDay -= 7;

		var todayDate = today.getDate();
		var thisMonth = d.getMonth() == today.getMonth() && d.getFullYear() == today.getFullYear();

    // render calendar
		do {
 		  var thisRow = jQuery("<tr></tr>");
  		for (var i=0; i<7; i++) {
  			var weekday = (_firstDayOfWeek + i) % 7;
  			var atts = {'class':(weekday == 0 || weekday == 6 ? 'weekend ' : 'weekday ')};

  			if (curDay < 0 || curDay >= lastDay) {
  				dayStr = ' ';
  			}
  			else if (firstMonth && curDay < firstDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else if (finalMonth && curDay > lastDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else {
  				d.setDate(curDay+1);

  				// attach a click handler to every day to select it if clicked
  				// we use the rel attribute to keep track of the day that is being clicked
  				dayStr = jQuery('<a href="" rel="'+ d +'">'+ (curDay+1) +'</a>').click(function(e) {
            if (_selectedDate) {
               _selectedDate.removeClass('selected');
            }
      			_selectedDate = jQuery(this);
      			_selectedDate.addClass('selected');
            day.val(new Date(_selectedDate.attr('rel')).getDate());
  					return false;
  				});

  				// highlight the current selected day
  				if (day.val() == d.getDate()) {
  				  _selectedDate = dayStr;
  				  _selectedDate.addClass('selected');
  				}
  			}

  			if (thisMonth && curDay+1 == todayDate) {
  				atts['class'] += 'today';
  			}
  			thisRow.append(jQuery("<td></td>").attr(atts).append(dayStr));
  			curDay++;
      }

			tBody.append(thisRow);
		} while (curDay < lastDay);

		jQuery('div.jcalendar').html('<table cellspacing="1"></table><div class="jcalendar-links"></div>');
		jQuery('div.jcalendar table').append(headRow, tBody);
		jQuery('div.jcalendar > div.jcalendar-links').append(prevLink, todayLink, nextLink);
	};

	return {
		show: function(a, day, month, year) {
 			_firstDate = a._startDate;
			_lastDate = a._endDate;
			_firstDayOfWeek = a._firstDayOfWeek;

			// pass in the selected form date if one was set
			var selected;
			if (year.val() > 0 && month.val() > 0 && day.val() > 0) {
			  selected = new Date(year.val(), month.val()-1, day.val());
			}
			else {
			  selected = null;
			}
			_drawCalendar(selected, a, day, month, year);
		},
		changeMonth: function(d, e, day, month, year) {
			_drawCalendar(d, e, day, month, year);
		},
		/**
		* Function: setLanguageStrings
		*
		* Allows you to localise the calendar by passing in relevant text for the english strings in the plugin.
		*
		* Arguments:
		* days		-	Array, e.g. ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
		* months	-	Array, e.g. ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		* navLinks	-	Object, e.g. {p:'Prev', n:'Next', c:'Close', b:'Choose date'}
		**/
		setLanguageStrings: function(aDays, aMonths, aNavLinks) {
			days = aDays;
			months = aMonths;
			navLinks = aNavLinks;
		},
		/**
		* Function: setDateWindow
		*
		* Used internally to set the start and end dates for a given date select
		*
		* Arguments:
		* i			-	The id of the INPUT element this date window is for
		* w			-	The date window - an object containing startDate and endDate properties
		*				e.g. {startDate:'24-11-1981', endDate:'25-12-2012}
		**/
		setDateWindow: function(i, w, year) {
			if (w == undefined) w = {};
			if (w.startDate == undefined) {
				// set the minimum browseable date equal to January of the min year in the select box
				// don't get the first option because that is an empty year

				// note we can't do this: year.find('option:eq(1)').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._startDate = new Date(jQuery(year).find('option:eq(1)').val(), 0, 1);
			}
			else {
  			dateParts = w.startDate.split('-');
  			i._startDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			if (w.endDate == undefined) {
			  // set the maximum browseable date equal to December of the max year in the select box

			  // note we can't do this: year.find('option:last').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._endDate = new Date(jQuery(year).find('option:last').val(), 11, 1);
			}
			else {
  			dateParts = w.endDate.split('-');
  			i._endDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			i._firstDayOfWeek = w.firstDayOfWeek == undefined ? 0 : w.firstDayOfWeek;
		}
	};
}();

jQuery.fn.jcalendar = function(a) {
	this.each(function() {
    var day = jQuery(this).find('select.jcalendar-select-day');
    var month = jQuery(this).find('select.jcalendar-select-month');
    var year = jQuery(this).find('select.jcalendar-select-year');
    jQuery('div.jcalendar-selects').after('<div class="jcalendar"></div>');
		jQuery.jcalendar.setDateWindow(this, a, year);
		jQuery.jcalendar.show(this, day, month, year);

		day.change(function() {
		  // only if a valid day is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), month.val()-1, this.value);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		month.change(function() {
		  // only if a valid month is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), this.value-1, 1);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		year.change(function() {
		  // only if a valid year is selected
		  if (this.value > 0) {
  		  d = new Date(this.value, month.val()-1, 1);
    	  jQuery.jcalendar.changeMonth(d, a, day, month, year);
    	}
		});

	});
	return this;
};
/**
 * jCalendar 0.5
 *
 * Some code based on jQuery Date Picker (http://kelvinluck.com/assets/jquery/datePicker/)
 *
 * Copyright (c) 2007 Theodore Serbinski (http://tedserbinski.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
jQuery.jcalendar = function() {
	var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var days = ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'];
	var navLinks = {p:'Prev', n:'Next', t:'Today'};
	var _firstDayOfWeek;
	var _firstDate;
	var _lastDate;
	var _selectedDate;

	var _drawCalendar = function(dateIn, a, day, month, year) {
	  var today = new Date();
	  var d;

		if (dateIn == undefined) {
			// start from this month.
			d = new Date(today.getFullYear(), today.getMonth(), 1);
			year.val(today.getFullYear());
			month.val(today.getMonth()+1);
			day.val(today.getDate());
		}
		else {
			// start from the passed in date
			d = dateIn;
		  d.setDate(1);
		}

		// check that date is within allowed limits
		if ((d.getMonth() < _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear()) || d.getFullYear() < _firstDate.getFullYear()) {
			d = new Date(_firstDate.getFullYear(), _firstDate.getMonth(), 1);
		}
		else if ((d.getMonth() > _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear()) || d.getFullYear() > _lastDate.getFullYear()) {
			d = new Date(_lastDate.getFullYear(), _lastDate.getMonth(), 1);
		}

		var firstMonth = true;
		var firstDate = _firstDate.getDate();

		// create prev and next links
		if (!(d.getMonth() == _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear())) {
			// not in first display month so show a previous link
			firstMonth = false;
			var lastMonth = d.getMonth() == 0 ? new Date(d.getFullYear()-1, 11, 1) : new Date(d.getFullYear(), d.getMonth()-1, 1);
			var prevLink = jQuery('<a href="" class="link-prev">&lsaquo; '+ navLinks.p +'</a>').click(function() {
				jQuery.jcalendar.changeMonth(lastMonth, this, day, month, year);
				return false;
			});
		}

		var finalMonth = true;
		var lastDate = _lastDate.getDate();

		if (!(d.getMonth() == _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear())) {
			// in the last month - no next link
			finalMonth = false;
			var nextMonth = new Date(d.getFullYear(), d.getMonth()+1, 1);
			var nextLink = jQuery('<a href="" class="link-next">'+ navLinks.n +' &rsaquo;</a>').click(function() {
				jQuery.jcalendar.changeMonth(nextMonth, this, day, month, year);
				return false;
			});
		}

		var todayLink = jQuery('<a href="" class="link-today">'+ navLinks.t +'</a>').click(function() {
			day.val(today.getDate());
			jQuery.jcalendar.changeMonth(today, this, day, month, year);
			return false;
		});

    // update the year and month select boxes
  	year.val(d.getFullYear());
  	month.val(d.getMonth()+1);

		var headRow = jQuery("<tr></tr>");
		for (var i=_firstDayOfWeek; i<_firstDayOfWeek+7; i++) {
			var weekday = i%7;
			var wordday = days[weekday];
			headRow.append('<th scope="col" abbr="'+ wordday +'" title="'+ wordday +'" class="'+ (weekday == 0 || weekday == 6 ? 'weekend' : 'weekday') +'">'+ wordday +'</th>');
		}
		headRow = jQuery("<thead></thead>").append(headRow);

		var tBody = jQuery("<tbody></tbody>");
		var lastDay = (new Date(d.getFullYear(), d.getMonth()+1, 0)).getDate();
		var curDay = _firstDayOfWeek - d.getDay();
		if (curDay > 0) curDay -= 7;

		var todayDate = today.getDate();
		var thisMonth = d.getMonth() == today.getMonth() && d.getFullYear() == today.getFullYear();

    // render calendar
		do {
 		  var thisRow = jQuery("<tr></tr>");
  		for (var i=0; i<7; i++) {
  			var weekday = (_firstDayOfWeek + i) % 7;
  			var atts = {'class':(weekday == 0 || weekday == 6 ? 'weekend ' : 'weekday ')};

  			if (curDay < 0 || curDay >= lastDay) {
  				dayStr = ' ';
  			}
  			else if (firstMonth && curDay < firstDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else if (finalMonth && curDay > lastDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else {
  				d.setDate(curDay+1);

  				// attach a click handler to every day to select it if clicked
  				// we use the rel attribute to keep track of the day that is being clicked
  				dayStr = jQuery('<a href="" rel="'+ d +'">'+ (curDay+1) +'</a>').click(function(e) {
            if (_selectedDate) {
               _selectedDate.removeClass('selected');
            }
      			_selectedDate = jQuery(this);
      			_selectedDate.addClass('selected');
            day.val(new Date(_selectedDate.attr('rel')).getDate());
  					return false;
  				});

  				// highlight the current selected day
  				if (day.val() == d.getDate()) {
  				  _selectedDate = dayStr;
  				  _selectedDate.addClass('selected');
  				}
  			}

  			if (thisMonth && curDay+1 == todayDate) {
  				atts['class'] += 'today';
  			}
  			thisRow.append(jQuery("<td></td>").attr(atts).append(dayStr));
  			curDay++;
      }

			tBody.append(thisRow);
		} while (curDay < lastDay);

		jQuery('div.jcalendar').html('<table cellspacing="1"></table><div class="jcalendar-links"></div>');
		jQuery('div.jcalendar table').append(headRow, tBody);
		jQuery('div.jcalendar > div.jcalendar-links').append(prevLink, todayLink, nextLink);
	};

	return {
		show: function(a, day, month, year) {
 			_firstDate = a._startDate;
			_lastDate = a._endDate;
			_firstDayOfWeek = a._firstDayOfWeek;

			// pass in the selected form date if one was set
			var selected;
			if (year.val() > 0 && month.val() > 0 && day.val() > 0) {
			  selected = new Date(year.val(), month.val()-1, day.val());
			}
			else {
			  selected = null;
			}
			_drawCalendar(selected, a, day, month, year);
		},
		changeMonth: function(d, e, day, month, year) {
			_drawCalendar(d, e, day, month, year);
		},
		/**
		* Function: setLanguageStrings
		*
		* Allows you to localise the calendar by passing in relevant text for the english strings in the plugin.
		*
		* Arguments:
		* days		-	Array, e.g. ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
		* months	-	Array, e.g. ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		* navLinks	-	Object, e.g. {p:'Prev', n:'Next', c:'Close', b:'Choose date'}
		**/
		setLanguageStrings: function(aDays, aMonths, aNavLinks) {
			days = aDays;
			months = aMonths;
			navLinks = aNavLinks;
		},
		/**
		* Function: setDateWindow
		*
		* Used internally to set the start and end dates for a given date select
		*
		* Arguments:
		* i			-	The id of the INPUT element this date window is for
		* w			-	The date window - an object containing startDate and endDate properties
		*				e.g. {startDate:'24-11-1981', endDate:'25-12-2012}
		**/
		setDateWindow: function(i, w, year) {
			if (w == undefined) w = {};
			if (w.startDate == undefined) {
				// set the minimum browseable date equal to January of the min year in the select box
				// don't get the first option because that is an empty year

				// note we can't do this: year.find('option:eq(1)').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._startDate = new Date(jQuery(year).find('option:eq(1)').val(), 0, 1);
			}
			else {
  			dateParts = w.startDate.split('-');
  			i._startDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			if (w.endDate == undefined) {
			  // set the maximum browseable date equal to December of the max year in the select box

			  // note we can't do this: year.find('option:last').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._endDate = new Date(jQuery(year).find('option:last').val(), 11, 1);
			}
			else {
  			dateParts = w.endDate.split('-');
  			i._endDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			i._firstDayOfWeek = w.firstDayOfWeek == undefined ? 0 : w.firstDayOfWeek;
		}
	};
}();

jQuery.fn.jcalendar = function(a) {
	this.each(function() {
    var day = jQuery(this).find('select.jcalendar-select-day');
    var month = jQuery(this).find('select.jcalendar-select-month');
    var year = jQuery(this).find('select.jcalendar-select-year');
    jQuery('div.jcalendar-selects').after('<div class="jcalendar"></div>');
		jQuery.jcalendar.setDateWindow(this, a, year);
		jQuery.jcalendar.show(this, day, month, year);

		day.change(function() {
		  // only if a valid day is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), month.val()-1, this.value);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		month.change(function() {
		  // only if a valid month is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), this.value-1, 1);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		year.change(function() {
		  // only if a valid year is selected
		  if (this.value > 0) {
  		  d = new Date(this.value, month.val()-1, 1);
    	  jQuery.jcalendar.changeMonth(d, a, day, month, year);
    	}
		});

	});
	return this;
};
/**
 * jCalendar 0.5
 *
 * Some code based on jQuery Date Picker (http://kelvinluck.com/assets/jquery/datePicker/)
 *
 * Copyright (c) 2007 Theodore Serbinski (http://tedserbinski.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
jQuery.jcalendar = function() {
	var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var days = ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'];
	var navLinks = {p:'Prev', n:'Next', t:'Today'};
	var _firstDayOfWeek;
	var _firstDate;
	var _lastDate;
	var _selectedDate;

	var _drawCalendar = function(dateIn, a, day, month, year) {
	  var today = new Date();
	  var d;

		if (dateIn == undefined) {
			// start from this month.
			d = new Date(today.getFullYear(), today.getMonth(), 1);
			year.val(today.getFullYear());
			month.val(today.getMonth()+1);
			day.val(today.getDate());
		}
		else {
			// start from the passed in date
			d = dateIn;
		  d.setDate(1);
		}

		// check that date is within allowed limits
		if ((d.getMonth() < _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear()) || d.getFullYear() < _firstDate.getFullYear()) {
			d = new Date(_firstDate.getFullYear(), _firstDate.getMonth(), 1);
		}
		else if ((d.getMonth() > _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear()) || d.getFullYear() > _lastDate.getFullYear()) {
			d = new Date(_lastDate.getFullYear(), _lastDate.getMonth(), 1);
		}

		var firstMonth = true;
		var firstDate = _firstDate.getDate();

		// create prev and next links
		if (!(d.getMonth() == _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear())) {
			// not in first display month so show a previous link
			firstMonth = false;
			var lastMonth = d.getMonth() == 0 ? new Date(d.getFullYear()-1, 11, 1) : new Date(d.getFullYear(), d.getMonth()-1, 1);
			var prevLink = jQuery('<a href="" class="link-prev">&lsaquo; '+ navLinks.p +'</a>').click(function() {
				jQuery.jcalendar.changeMonth(lastMonth, this, day, month, year);
				return false;
			});
		}

		var finalMonth = true;
		var lastDate = _lastDate.getDate();

		if (!(d.getMonth() == _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear())) {
			// in the last month - no next link
			finalMonth = false;
			var nextMonth = new Date(d.getFullYear(), d.getMonth()+1, 1);
			var nextLink = jQuery('<a href="" class="link-next">'+ navLinks.n +' &rsaquo;</a>').click(function() {
				jQuery.jcalendar.changeMonth(nextMonth, this, day, month, year);
				return false;
			});
		}

		var todayLink = jQuery('<a href="" class="link-today">'+ navLinks.t +'</a>').click(function() {
			day.val(today.getDate());
			jQuery.jcalendar.changeMonth(today, this, day, month, year);
			return false;
		});

    // update the year and month select boxes
  	year.val(d.getFullYear());
  	month.val(d.getMonth()+1);

		var headRow = jQuery("<tr></tr>");
		for (var i=_firstDayOfWeek; i<_firstDayOfWeek+7; i++) {
			var weekday = i%7;
			var wordday = days[weekday];
			headRow.append('<th scope="col" abbr="'+ wordday +'" title="'+ wordday +'" class="'+ (weekday == 0 || weekday == 6 ? 'weekend' : 'weekday') +'">'+ wordday +'</th>');
		}
		headRow = jQuery("<thead></thead>").append(headRow);

		var tBody = jQuery("<tbody></tbody>");
		var lastDay = (new Date(d.getFullYear(), d.getMonth()+1, 0)).getDate();
		var curDay = _firstDayOfWeek - d.getDay();
		if (curDay > 0) curDay -= 7;

		var todayDate = today.getDate();
		var thisMonth = d.getMonth() == today.getMonth() && d.getFullYear() == today.getFullYear();

    // render calendar
		do {
 		  var thisRow = jQuery("<tr></tr>");
  		for (var i=0; i<7; i++) {
  			var weekday = (_firstDayOfWeek + i) % 7;
  			var atts = {'class':(weekday == 0 || weekday == 6 ? 'weekend ' : 'weekday ')};

  			if (curDay < 0 || curDay >= lastDay) {
  				dayStr = ' ';
  			}
  			else if (firstMonth && curDay < firstDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else if (finalMonth && curDay > lastDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else {
  				d.setDate(curDay+1);

  				// attach a click handler to every day to select it if clicked
  				// we use the rel attribute to keep track of the day that is being clicked
  				dayStr = jQuery('<a href="" rel="'+ d +'">'+ (curDay+1) +'</a>').click(function(e) {
            if (_selectedDate) {
               _selectedDate.removeClass('selected');
            }
      			_selectedDate = jQuery(this);
      			_selectedDate.addClass('selected');
            day.val(new Date(_selectedDate.attr('rel')).getDate());
  					return false;
  				});

  				// highlight the current selected day
  				if (day.val() == d.getDate()) {
  				  _selectedDate = dayStr;
  				  _selectedDate.addClass('selected');
  				}
  			}

  			if (thisMonth && curDay+1 == todayDate) {
  				atts['class'] += 'today';
  			}
  			thisRow.append(jQuery("<td></td>").attr(atts).append(dayStr));
  			curDay++;
      }

			tBody.append(thisRow);
		} while (curDay < lastDay);

		jQuery('div.jcalendar').html('<table cellspacing="1"></table><div class="jcalendar-links"></div>');
		jQuery('div.jcalendar table').append(headRow, tBody);
		jQuery('div.jcalendar > div.jcalendar-links').append(prevLink, todayLink, nextLink);
	};

	return {
		show: function(a, day, month, year) {
 			_firstDate = a._startDate;
			_lastDate = a._endDate;
			_firstDayOfWeek = a._firstDayOfWeek;

			// pass in the selected form date if one was set
			var selected;
			if (year.val() > 0 && month.val() > 0 && day.val() > 0) {
			  selected = new Date(year.val(), month.val()-1, day.val());
			}
			else {
			  selected = null;
			}
			_drawCalendar(selected, a, day, month, year);
		},
		changeMonth: function(d, e, day, month, year) {
			_drawCalendar(d, e, day, month, year);
		},
		/**
		* Function: setLanguageStrings
		*
		* Allows you to localise the calendar by passing in relevant text for the english strings in the plugin.
		*
		* Arguments:
		* days		-	Array, e.g. ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
		* months	-	Array, e.g. ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		* navLinks	-	Object, e.g. {p:'Prev', n:'Next', c:'Close', b:'Choose date'}
		**/
		setLanguageStrings: function(aDays, aMonths, aNavLinks) {
			days = aDays;
			months = aMonths;
			navLinks = aNavLinks;
		},
		/**
		* Function: setDateWindow
		*
		* Used internally to set the start and end dates for a given date select
		*
		* Arguments:
		* i			-	The id of the INPUT element this date window is for
		* w			-	The date window - an object containing startDate and endDate properties
		*				e.g. {startDate:'24-11-1981', endDate:'25-12-2012}
		**/
		setDateWindow: function(i, w, year) {
			if (w == undefined) w = {};
			if (w.startDate == undefined) {
				// set the minimum browseable date equal to January of the min year in the select box
				// don't get the first option because that is an empty year

				// note we can't do this: year.find('option:eq(1)').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._startDate = new Date(jQuery(year).find('option:eq(1)').val(), 0, 1);
			}
			else {
  			dateParts = w.startDate.split('-');
  			i._startDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			if (w.endDate == undefined) {
			  // set the maximum browseable date equal to December of the max year in the select box

			  // note we can't do this: year.find('option:last').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._endDate = new Date(jQuery(year).find('option:last').val(), 11, 1);
			}
			else {
  			dateParts = w.endDate.split('-');
  			i._endDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			i._firstDayOfWeek = w.firstDayOfWeek == undefined ? 0 : w.firstDayOfWeek;
		}
	};
}();

jQuery.fn.jcalendar = function(a) {
	this.each(function() {
    var day = jQuery(this).find('select.jcalendar-select-day');
    var month = jQuery(this).find('select.jcalendar-select-month');
    var year = jQuery(this).find('select.jcalendar-select-year');
    jQuery('div.jcalendar-selects').after('<div class="jcalendar"></div>');
		jQuery.jcalendar.setDateWindow(this, a, year);
		jQuery.jcalendar.show(this, day, month, year);

		day.change(function() {
		  // only if a valid day is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), month.val()-1, this.value);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		month.change(function() {
		  // only if a valid month is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), this.value-1, 1);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		year.change(function() {
		  // only if a valid year is selected
		  if (this.value > 0) {
  		  d = new Date(this.value, month.val()-1, 1);
    	  jQuery.jcalendar.changeMonth(d, a, day, month, year);
    	}
		});

	});
	return this;
};
/**
 * jCalendar 0.5
 *
 * Some code based on jQuery Date Picker (http://kelvinluck.com/assets/jquery/datePicker/)
 *
 * Copyright (c) 2007 Theodore Serbinski (http://tedserbinski.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
jQuery.jcalendar = function() {
	var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var days = ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'];
	var navLinks = {p:'Prev', n:'Next', t:'Today'};
	var _firstDayOfWeek;
	var _firstDate;
	var _lastDate;
	var _selectedDate;

	var _drawCalendar = function(dateIn, a, day, month, year) {
	  var today = new Date();
	  var d;

		if (dateIn == undefined) {
			// start from this month.
			d = new Date(today.getFullYear(), today.getMonth(), 1);
			year.val(today.getFullYear());
			month.val(today.getMonth()+1);
			day.val(today.getDate());
		}
		else {
			// start from the passed in date
			d = dateIn;
		  d.setDate(1);
		}

		// check that date is within allowed limits
		if ((d.getMonth() < _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear()) || d.getFullYear() < _firstDate.getFullYear()) {
			d = new Date(_firstDate.getFullYear(), _firstDate.getMonth(), 1);
		}
		else if ((d.getMonth() > _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear()) || d.getFullYear() > _lastDate.getFullYear()) {
			d = new Date(_lastDate.getFullYear(), _lastDate.getMonth(), 1);
		}

		var firstMonth = true;
		var firstDate = _firstDate.getDate();

		// create prev and next links
		if (!(d.getMonth() == _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear())) {
			// not in first display month so show a previous link
			firstMonth = false;
			var lastMonth = d.getMonth() == 0 ? new Date(d.getFullYear()-1, 11, 1) : new Date(d.getFullYear(), d.getMonth()-1, 1);
			var prevLink = jQuery('<a href="" class="link-prev">&lsaquo; '+ navLinks.p +'</a>').click(function() {
				jQuery.jcalendar.changeMonth(lastMonth, this, day, month, year);
				return false;
			});
		}

		var finalMonth = true;
		var lastDate = _lastDate.getDate();

		if (!(d.getMonth() == _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear())) {
			// in the last month - no next link
			finalMonth = false;
			var nextMonth = new Date(d.getFullYear(), d.getMonth()+1, 1);
			var nextLink = jQuery('<a href="" class="link-next">'+ navLinks.n +' &rsaquo;</a>').click(function() {
				jQuery.jcalendar.changeMonth(nextMonth, this, day, month, year);
				return false;
			});
		}

		var todayLink = jQuery('<a href="" class="link-today">'+ navLinks.t +'</a>').click(function() {
			day.val(today.getDate());
			jQuery.jcalendar.changeMonth(today, this, day, month, year);
			return false;
		});

    // update the year and month select boxes
  	year.val(d.getFullYear());
  	month.val(d.getMonth()+1);

		var headRow = jQuery("<tr></tr>");
		for (var i=_firstDayOfWeek; i<_firstDayOfWeek+7; i++) {
			var weekday = i%7;
			var wordday = days[weekday];
			headRow.append('<th scope="col" abbr="'+ wordday +'" title="'+ wordday +'" class="'+ (weekday == 0 || weekday == 6 ? 'weekend' : 'weekday') +'">'+ wordday +'</th>');
		}
		headRow = jQuery("<thead></thead>").append(headRow);

		var tBody = jQuery("<tbody></tbody>");
		var lastDay = (new Date(d.getFullYear(), d.getMonth()+1, 0)).getDate();
		var curDay = _firstDayOfWeek - d.getDay();
		if (curDay > 0) curDay -= 7;

		var todayDate = today.getDate();
		var thisMonth = d.getMonth() == today.getMonth() && d.getFullYear() == today.getFullYear();

    // render calendar
		do {
 		  var thisRow = jQuery("<tr></tr>");
  		for (var i=0; i<7; i++) {
  			var weekday = (_firstDayOfWeek + i) % 7;
  			var atts = {'class':(weekday == 0 || weekday == 6 ? 'weekend ' : 'weekday ')};

  			if (curDay < 0 || curDay >= lastDay) {
  				dayStr = ' ';
  			}
  			else if (firstMonth && curDay < firstDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else if (finalMonth && curDay > lastDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else {
  				d.setDate(curDay+1);

  				// attach a click handler to every day to select it if clicked
  				// we use the rel attribute to keep track of the day that is being clicked
  				dayStr = jQuery('<a href="" rel="'+ d +'">'+ (curDay+1) +'</a>').click(function(e) {
            if (_selectedDate) {
               _selectedDate.removeClass('selected');
            }
      			_selectedDate = jQuery(this);
      			_selectedDate.addClass('selected');
            day.val(new Date(_selectedDate.attr('rel')).getDate());
  					return false;
  				});

  				// highlight the current selected day
  				if (day.val() == d.getDate()) {
  				  _selectedDate = dayStr;
  				  _selectedDate.addClass('selected');
  				}
  			}

  			if (thisMonth && curDay+1 == todayDate) {
  				atts['class'] += 'today';
  			}
  			thisRow.append(jQuery("<td></td>").attr(atts).append(dayStr));
  			curDay++;
      }

			tBody.append(thisRow);
		} while (curDay < lastDay);

		jQuery('div.jcalendar').html('<table cellspacing="1"></table><div class="jcalendar-links"></div>');
		jQuery('div.jcalendar table').append(headRow, tBody);
		jQuery('div.jcalendar > div.jcalendar-links').append(prevLink, todayLink, nextLink);
	};

	return {
		show: function(a, day, month, year) {
 			_firstDate = a._startDate;
			_lastDate = a._endDate;
			_firstDayOfWeek = a._firstDayOfWeek;

			// pass in the selected form date if one was set
			var selected;
			if (year.val() > 0 && month.val() > 0 && day.val() > 0) {
			  selected = new Date(year.val(), month.val()-1, day.val());
			}
			else {
			  selected = null;
			}
			_drawCalendar(selected, a, day, month, year);
		},
		changeMonth: function(d, e, day, month, year) {
			_drawCalendar(d, e, day, month, year);
		},
		/**
		* Function: setLanguageStrings
		*
		* Allows you to localise the calendar by passing in relevant text for the english strings in the plugin.
		*
		* Arguments:
		* days		-	Array, e.g. ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
		* months	-	Array, e.g. ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		* navLinks	-	Object, e.g. {p:'Prev', n:'Next', c:'Close', b:'Choose date'}
		**/
		setLanguageStrings: function(aDays, aMonths, aNavLinks) {
			days = aDays;
			months = aMonths;
			navLinks = aNavLinks;
		},
		/**
		* Function: setDateWindow
		*
		* Used internally to set the start and end dates for a given date select
		*
		* Arguments:
		* i			-	The id of the INPUT element this date window is for
		* w			-	The date window - an object containing startDate and endDate properties
		*				e.g. {startDate:'24-11-1981', endDate:'25-12-2012}
		**/
		setDateWindow: function(i, w, year) {
			if (w == undefined) w = {};
			if (w.startDate == undefined) {
				// set the minimum browseable date equal to January of the min year in the select box
				// don't get the first option because that is an empty year

				// note we can't do this: year.find('option:eq(1)').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._startDate = new Date(jQuery(year).find('option:eq(1)').val(), 0, 1);
			}
			else {
  			dateParts = w.startDate.split('-');
  			i._startDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			if (w.endDate == undefined) {
			  // set the maximum browseable date equal to December of the max year in the select box

			  // note we can't do this: year.find('option:last').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._endDate = new Date(jQuery(year).find('option:last').val(), 11, 1);
			}
			else {
  			dateParts = w.endDate.split('-');
  			i._endDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			i._firstDayOfWeek = w.firstDayOfWeek == undefined ? 0 : w.firstDayOfWeek;
		}
	};
}();

jQuery.fn.jcalendar = function(a) {
	this.each(function() {
    var day = jQuery(this).find('select.jcalendar-select-day');
    var month = jQuery(this).find('select.jcalendar-select-month');
    var year = jQuery(this).find('select.jcalendar-select-year');
    jQuery('div.jcalendar-selects').after('<div class="jcalendar"></div>');
		jQuery.jcalendar.setDateWindow(this, a, year);
		jQuery.jcalendar.show(this, day, month, year);

		day.change(function() {
		  // only if a valid day is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), month.val()-1, this.value);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		month.change(function() {
		  // only if a valid month is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), this.value-1, 1);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		year.change(function() {
		  // only if a valid year is selected
		  if (this.value > 0) {
  		  d = new Date(this.value, month.val()-1, 1);
    	  jQuery.jcalendar.changeMonth(d, a, day, month, year);
    	}
		});

	});
	return this;
};
/**
 * jCalendar 0.5
 *
 * Some code based on jQuery Date Picker (http://kelvinluck.com/assets/jquery/datePicker/)
 *
 * Copyright (c) 2007 Theodore Serbinski (http://tedserbinski.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
jQuery.jcalendar = function() {
	var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var days = ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'];
	var navLinks = {p:'Prev', n:'Next', t:'Today'};
	var _firstDayOfWeek;
	var _firstDate;
	var _lastDate;
	var _selectedDate;

	var _drawCalendar = function(dateIn, a, day, month, year) {
	  var today = new Date();
	  var d;

		if (dateIn == undefined) {
			// start from this month.
			d = new Date(today.getFullYear(), today.getMonth(), 1);
			year.val(today.getFullYear());
			month.val(today.getMonth()+1);
			day.val(today.getDate());
		}
		else {
			// start from the passed in date
			d = dateIn;
		  d.setDate(1);
		}

		// check that date is within allowed limits
		if ((d.getMonth() < _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear()) || d.getFullYear() < _firstDate.getFullYear()) {
			d = new Date(_firstDate.getFullYear(), _firstDate.getMonth(), 1);
		}
		else if ((d.getMonth() > _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear()) || d.getFullYear() > _lastDate.getFullYear()) {
			d = new Date(_lastDate.getFullYear(), _lastDate.getMonth(), 1);
		}

		var firstMonth = true;
		var firstDate = _firstDate.getDate();

		// create prev and next links
		if (!(d.getMonth() == _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear())) {
			// not in first display month so show a previous link
			firstMonth = false;
			var lastMonth = d.getMonth() == 0 ? new Date(d.getFullYear()-1, 11, 1) : new Date(d.getFullYear(), d.getMonth()-1, 1);
			var prevLink = jQuery('<a href="" class="link-prev">&lsaquo; '+ navLinks.p +'</a>').click(function() {
				jQuery.jcalendar.changeMonth(lastMonth, this, day, month, year);
				return false;
			});
		}

		var finalMonth = true;
		var lastDate = _lastDate.getDate();

		if (!(d.getMonth() == _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear())) {
			// in the last month - no next link
			finalMonth = false;
			var nextMonth = new Date(d.getFullYear(), d.getMonth()+1, 1);
			var nextLink = jQuery('<a href="" class="link-next">'+ navLinks.n +' &rsaquo;</a>').click(function() {
				jQuery.jcalendar.changeMonth(nextMonth, this, day, month, year);
				return false;
			});
		}

		var todayLink = jQuery('<a href="" class="link-today">'+ navLinks.t +'</a>').click(function() {
			day.val(today.getDate());
			jQuery.jcalendar.changeMonth(today, this, day, month, year);
			return false;
		});

    // update the year and month select boxes
  	year.val(d.getFullYear());
  	month.val(d.getMonth()+1);

		var headRow = jQuery("<tr></tr>");
		for (var i=_firstDayOfWeek; i<_firstDayOfWeek+7; i++) {
			var weekday = i%7;
			var wordday = days[weekday];
			headRow.append('<th scope="col" abbr="'+ wordday +'" title="'+ wordday +'" class="'+ (weekday == 0 || weekday == 6 ? 'weekend' : 'weekday') +'">'+ wordday +'</th>');
		}
		headRow = jQuery("<thead></thead>").append(headRow);

		var tBody = jQuery("<tbody></tbody>");
		var lastDay = (new Date(d.getFullYear(), d.getMonth()+1, 0)).getDate();
		var curDay = _firstDayOfWeek - d.getDay();
		if (curDay > 0) curDay -= 7;

		var todayDate = today.getDate();
		var thisMonth = d.getMonth() == today.getMonth() && d.getFullYear() == today.getFullYear();

    // render calendar
		do {
 		  var thisRow = jQuery("<tr></tr>");
  		for (var i=0; i<7; i++) {
  			var weekday = (_firstDayOfWeek + i) % 7;
  			var atts = {'class':(weekday == 0 || weekday == 6 ? 'weekend ' : 'weekday ')};

  			if (curDay < 0 || curDay >= lastDay) {
  				dayStr = ' ';
  			}
  			else if (firstMonth && curDay < firstDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else if (finalMonth && curDay > lastDate-1) {
  				dayStr = curDay+1;
  				atts['class'] += 'inactive';
  			}
  			else {
  				d.setDate(curDay+1);

  				// attach a click handler to every day to select it if clicked
  				// we use the rel attribute to keep track of the day that is being clicked
  				dayStr = jQuery('<a href="" rel="'+ d +'">'+ (curDay+1) +'</a>').click(function(e) {
            if (_selectedDate) {
               _selectedDate.removeClass('selected');
            }
      			_selectedDate = jQuery(this);
      			_selectedDate.addClass('selected');
            day.val(new Date(_selectedDate.attr('rel')).getDate());
  					return false;
  				});

  				// highlight the current selected day
  				if (day.val() == d.getDate()) {
  				  _selectedDate = dayStr;
  				  _selectedDate.addClass('selected');
  				}
  			}

  			if (thisMonth && curDay+1 == todayDate) {
  				atts['class'] += 'today';
  			}
  			thisRow.append(jQuery("<td></td>").attr(atts).append(dayStr));
  			curDay++;
      }

			tBody.append(thisRow);
		} while (curDay < lastDay);

		jQuery('div.jcalendar').html('<table cellspacing="1"></table><div class="jcalendar-links"></div>');
		jQuery('div.jcalendar table').append(headRow, tBody);
		jQuery('div.jcalendar > div.jcalendar-links').append(prevLink, todayLink, nextLink);
	};

	return {
		show: function(a, day, month, year) {
 			_firstDate = a._startDate;
			_lastDate = a._endDate;
			_firstDayOfWeek = a._firstDayOfWeek;

			// pass in the selected form date if one was set
			var selected;
			if (year.val() > 0 && month.val() > 0 && day.val() > 0) {
			  selected = new Date(year.val(), month.val()-1, day.val());
			}
			else {
			  selected = null;
			}
			_drawCalendar(selected, a, day, month, year);
		},
		changeMonth: function(d, e, day, month, year) {
			_drawCalendar(d, e, day, month, year);
		},
		/**
		* Function: setLanguageStrings
		*
		* Allows you to localise the calendar by passing in relevant text for the english strings in the plugin.
		*
		* Arguments:
		* days		-	Array, e.g. ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
		* months	-	Array, e.g. ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		* navLinks	-	Object, e.g. {p:'Prev', n:'Next', c:'Close', b:'Choose date'}
		**/
		setLanguageStrings: function(aDays, aMonths, aNavLinks) {
			days = aDays;
			months = aMonths;
			navLinks = aNavLinks;
		},
		/**
		* Function: setDateWindow
		*
		* Used internally to set the start and end dates for a given date select
		*
		* Arguments:
		* i			-	The id of the INPUT element this date window is for
		* w			-	The date window - an object containing startDate and endDate properties
		*				e.g. {startDate:'24-11-1981', endDate:'25-12-2012}
		**/
		setDateWindow: function(i, w, year) {
			if (w == undefined) w = {};
			if (w.startDate == undefined) {
				// set the minimum browseable date equal to January of the min year in the select box
				// don't get the first option because that is an empty year

				// note we can't do this: year.find('option:eq(1)').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._startDate = new Date(jQuery(year).find('option:eq(1)').val(), 0, 1);
			}
			else {
  			dateParts = w.startDate.split('-');
  			i._startDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			if (w.endDate == undefined) {
			  // set the maximum browseable date equal to December of the max year in the select box

			  // note we can't do this: year.find('option:last').val()
				// it doesn't work in 1.0 since find() is destructive
				// so we copy the object to a new var
				i._endDate = new Date(jQuery(year).find('option:last').val(), 11, 1);
			}
			else {
  			dateParts = w.endDate.split('-');
  			i._endDate = new Date(dateParts[2], Number(dateParts[1])-1, Number(dateParts[0]));
			}
			i._firstDayOfWeek = w.firstDayOfWeek == undefined ? 0 : w.firstDayOfWeek;
		}
	};
}();

jQuery.fn.jcalendar = function(a) {
	this.each(function() {
    var day = jQuery(this).find('select.jcalendar-select-day');
    var month = jQuery(this).find('select.jcalendar-select-month');
    var year = jQuery(this).find('select.jcalendar-select-year');
    jQuery('div.jcalendar-selects').after('<div class="jcalendar"></div>');
		jQuery.jcalendar.setDateWindow(this, a, year);
		jQuery.jcalendar.show(this, day, month, year);

		day.change(function() {
		  // only if a valid day is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), month.val()-1, this.value);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		month.change(function() {
		  // only if a valid month is selected
		  if (this.value > 0) {
		    d = new Date(year.val(), this.value-1, 1);
  	    jQuery.jcalendar.changeMonth(d, a, day, month, year);
  	  }
		});

		year.change(function() {
		  // only if a valid year is selected
		  if (this.value > 0) {
  		  d = new Date(this.value, month.val()-1, 1);
    	  jQuery.jcalendar.changeMonth(d, a, day, month, year);
    	}
		});

	});
	return this;
};
