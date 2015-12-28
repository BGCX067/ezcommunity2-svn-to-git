<?php
//
// $Id: searchsupplier.php,v 1.6 2001/10/10 11:38:27 bf Exp $
//
// Created on: <23-Oct-2000 17:53:46 bf>
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


$ModuleName = "eZ rfp";
$SearchResult["DetailedSearchPath"] = "/rfp/search/";
$SearchResult["DetailedSearchVariable"] = "SearchText";
$SearchResult["DetailViewPath"] = "/rfp/view/";
$SearchResult["IconPath"] = "/images/document.gif";

include_once( "ezrfp/classes/ezrfp.php" );

$rfp = new eZRfp();
$SearchResult["Result"] = $rfp->search( $SearchText, "time", false, 0, $Limit, array(), $SearchResult["SearchCount"] );
// $SearchResult["SearchCount"] = $rfp->searchCount( $SearchText, "time", false );



?>
