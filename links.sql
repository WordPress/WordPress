--  --------------------------------------------------------
-- // $Id$
-- //
-- // Links
-- // Copyright (C) 2002 Mike Little -- mike@zed1.com
-- //
-- // This is an add-on to b2 weblog / news publishing tool
-- // b2 is copyright (c)2001, 2002 by Michel Valdrighi - m@tidakada.com
-- //
-- // **********************************************************************
-- // Copyright (C) 2002 Mike Little
-- //
-- // This program is free software; you can redistribute it and/or modify
-- // it under the terms of the GNU General Public License as published by
-- // the Free Software Foundation; either version 2 of the License, or
-- // (at your option) any later version.
-- //
-- // This program is distributed in the hope that it will be useful, but
-- // WITHOUT ANY WARRANTY; without even the implied warranty of
-- // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
-- // General Public License for more details.
-- //
-- // You should have received a copy of the GNU General Public License
-- // along with this program; if not, write to the Free Software
-- // Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
-- //
-- // Mike Little (mike@zed1.com)
-- // *****************************************************************

-- DROP TABLE IF EXISTS b2links;
CREATE TABLE b2links (
  link_id int(11) NOT NULL auto_increment,
  link_url varchar(255) NOT NULL default '',
  link_name varchar(255) NOT NULL default '',
  link_image varchar(255) NOT NULL default '',
  link_target varchar(25) NOT NULL default '',
  link_category int(11) NOT NULL default 0,
  link_description varchar(255) NOT NULL default '',
  link_visible enum ('Y','N') NOT NULL default 'Y',
  link_owner int NOT NULL DEFAULT '1',
  link_rating int NOT NULL DEFAULT '0',
  link_updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  link_rel varchar(255) NOT NULL default '',
  PRIMARY KEY (link_id)
) TYPE=MyISAM;


-- DROP TABLE IF EXISTS linkcategories;
CREATE TABLE linkcategories (
  cat_id int(11) NOT NULL auto_increment,
  cat_name tinytext NOT NULL,
  auto_toggle enum ('Y','N') NOT NULL default 'N',
  PRIMARY KEY (cat_id)
) TYPE=MyISAM;

INSERT INTO linkcategories (cat_id, cat_name) VALUES (1, 'General');

INSERT INTO b2links (link_id, link_url, link_name, link_image, link_target, link_category, link_description, link_visible, link_owner)
VALUES (1, 'http://www.cafelog.com/', 'Cafelog', '', '_blank', 1, 'Cafelog', 'Y', 1);
