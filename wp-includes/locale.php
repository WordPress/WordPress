<?php
// Date and Time

// The Weekdays
$weekday[0] = __('Sunday');
$weekday[1] = __('Monday');
$weekday[2] = __('Tuesday');
$weekday[3] = __('Wednesday');
$weekday[4] = __('Thursday');
$weekday[5] = __('Friday');
$weekday[6] = __('Saturday');

// The first letter of each day.  The _%day%_initial suffix is a hack to make
// sure the day initials are unique.  They should be translated to a one
// letter initial.  
$weekday_initial[__('Sunday')]    = __('S_Sunday_initial');
$weekday_initial[__('Monday')]    = __('M_Monday_initial');
$weekday_initial[__('Tuesday')]   = __('T_Tuesday_initial');
$weekday_initial[__('Wednesday')] = __('W_Wednesday_initial');
$weekday_initial[__('Thursday')]  = __('T_Thursday_initial');
$weekday_initial[__('Friday')]    = __('F_Friday_initial');
$weekday_initial[__('Saturday')]  = __('S_Saturday_initial');

foreach ($weekday_initial as $weekday_ => $weekday_initial_) {
  $weekday_initial[$weekday_] = preg_replace('/_.+_initial$/', '', $weekday_initial_);
}

// Abbreviations for each day.
$weekday_abbrev[__('Sunday')]    = __('Sun');
$weekday_abbrev[__('Monday')]    = __('Mon');
$weekday_abbrev[__('Tuesday')]   = __('Tue');
$weekday_abbrev[__('Wednesday')] = __('Wed');
$weekday_abbrev[__('Thursday')]  = __('Thu');
$weekday_abbrev[__('Friday')]    = __('Fri');
$weekday_abbrev[__('Saturday')]  = __('Sat');

// The Months
$month['01'] = __('January');
$month['02'] = __('February');
$month['03'] = __('March');
$month['04'] = __('April');
$month['05'] = __('May');
$month['06'] = __('June');
$month['07'] = __('July');
$month['08'] = __('August');
$month['09'] = __('September');
$month['10'] = __('October');
$month['11'] = __('November');
$month['12'] = __('December');

// Abbreviations for each month. Uses the same hack as above to get around the
// 'May' duplication.
$month_abbrev[__('January')] = __('Jan_January_abbreviation');
$month_abbrev[__('February')] = __('Feb_February_abbreviation');
$month_abbrev[__('March')] = __('Mar_March_abbreviation');
$month_abbrev[__('April')] = __('Apr_April_abbreviation');
$month_abbrev[__('May')] = __('May_May_abbreviation');
$month_abbrev[__('June')] = __('Jun_June_abbreviation');
$month_abbrev[__('July')] = __('Jul_July_abbreviation');
$month_abbrev[__('August')] = __('Aug_August_abbreviation');
$month_abbrev[__('September')] = __('Sep_September_abbreviation');
$month_abbrev[__('October')] = __('Oct_October_abbreviation');
$month_abbrev[__('November')] = __('Nov_November_abbreviation');
$month_abbrev[__('December')] = __('Dec_December_abbreviation');

foreach ($month_abbrev as $month_ => $month_abbrev_) {
  $month_abbrev[$month_] = preg_replace('/_.+_abbreviation$/', '', $month_abbrev_);
}

?>