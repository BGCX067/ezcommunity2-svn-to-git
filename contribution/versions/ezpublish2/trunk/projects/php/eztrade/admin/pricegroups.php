<?php
// 
// $Id: pricegroups.php 9052 2001-11-19 10:31:36Z br $
//
// Created on: <23-Feb-2001 12:09:51 amos>
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

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );
include_once( "classes/ezlist.php" );

include_once( "eztrade/classes/ezpricegroup.php" );

$page_path = "/trade/pricegroups";

if ( isset( $New ) )
{
    eZHTTPTool::header( "Location: $page_path/new" );
    exit();
}

if ( isset( $Delete ) )
{
    if ( count( $ItemArrayID ) > 0 )
    {
        foreach( $ItemArrayID as $item )
        {
            eZPriceGroup::delete( $item );
        }
    }
    eZHTTPTool::header( "Location: $page_path/list" );
    exit();
}

$language_file = "pricegroups.php";

$item_type = new eZAddressType();
$item_type_array = eZPriceGroup::getAll();
$move_item = true;

eZList::drawList( array( "language_file" => $language_file,
                         "page_path" => $page_path,
                         "item_type_array" => $item_type_array,
                         "total_types" => 0,
                         "action" => $Action,
                         "offset" => $Offset,
                         "list_type" => $ListType,
                         "module" => "eztrade",
                         "module_main" => "eZTradeMain",
                         "header_names" => array( "{intl-name}", "{intl-description}" ),
                         "custom_func_call" => array( "description" ),
                         "form_command" => "$page_path/list" ) );

?>
