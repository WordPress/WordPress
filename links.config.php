<?php
// $Id$
//
// Links
// Copyright (C) 2002 Mike Little -- mike@zed1.com
//
// This is an add-on to b2 weblog / news publishing tool
// b2 is copyright (c)2001, 2002 by Michel Valdrighi - m@tidakada.com
//
// **********************************************************************
// Copyright (C) 2002 Mike Little
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
//
// Mike Little (mike@zed1.com)
// *****************************************************************

// Various variables to control your Links configuration

// table names in the database
$tablelinks = "b2links";
$tablelinkcategories = "b2linkcategories";
$minadminlevel = 5;
$use_adminlevels = true; // set this to false to have all links visible to
                         // everyone in the link manager

// Set this to the type rating system you wish to use.
// allowed values are: none, number, char, image
$links_rating_type = 'image';

// if we are set to 'char' which char to use.
$links_rating_char = '*';

// what do we do with a value of zero?
// set this to 1 to output nothing
// set it to 0 to output as normal (number/image)
$links_rating_ignore_zero = 0;

//use the same image (time rating)?
// uses $links_rating_image[0]
$links_rating_single_image = 1;

//or use an individual image for each value?
$links_rating_image[0]='links-images/star.gif';
//$links_rating_image[0]='links-images/rating-0.gif';
$links_rating_image[1]='links-images/rating-1.gif';
$links_rating_image[2]='links-images/rating-2.gif';
$links_rating_image[3]='links-images/rating-3.gif';
$links_rating_image[4]='links-images/rating-4.gif';
$links_rating_image[5]='links-images/rating-5.gif';
$links_rating_image[6]='links-images/rating-6.gif';
$links_rating_image[7]='links-images/rating-7.gif';
$links_rating_image[8]='links-images/rating-8.gif';
$links_rating_image[9]='links-images/rating-9.gif';


//weblogs.com lookup values

// path/to/cachefile needs to be writable by web server
$weblogs_cache_file = 'weblogs.com.changes.cache';

// Which file to grab. changes.xml contains about 3 hours worth of updates and
// is at least 100kb (you don't want to grab this every 10 minutes)
// shortChanges.xml is about 5 minutes worth and around 5kb you don't want to
// grab this less frequently than every five minutes.

//$weblogs_xml_url = 'http://www.weblogs.com/changes.xml';
$weblogs_xml_url = 'http://www.weblogs.com/shortChanges.xml';
$weblogs_cacheminutes = 5; // cache time in minutes (if it is older than this get a new copy)

?>
