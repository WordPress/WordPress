--  --------------------------------------------------------
-- // $Id$
-- //
-- // B2Links
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

alter table b2links modify column link_url varchar(255) NOT NULL default '';
-- this will have silently changed all the other chars to varchars!
alter table b2links add column link_description varchar(255) NOT NULL default '';
alter table b2links add column link_visible enum ('Y','N') NOT NULL default 'Y';
update b2links set link_description = link_name;
