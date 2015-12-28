<?php
// 
// $Id: menubox.php 9100 2001-11-21 14:49:02Z bf $
//
// Created on: <20-Nov-2001 14:57:39 bf>
//
// This source file is part of eZ publish, publishing software.
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//

// Supply $menuItems to get a menubox

$menuItems = array(
    array( "/datamanager/typelist/", "{intl-type_list}" ),
    array( "/datamanager/typeedit/", "{intl-new_type}" ),
    array( "/datamanager/itemedit/", "{intl-new_item}" ),
    array( "/datamanager/search/", "{intl-search}" )
    );

?>
