<?php
// 
// $Id: supportdelete.php 8613 2001-10-29 14:13:03Z jhe $
//
// Created on: <29-Oct-2001 14:21:21 jhe>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2001 eZ systems as
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

include_once( "ezbug/classes/ezbugsupport.php" );

require( "ezuser/admin/admincheck.php" );

if ( !is_array( $SupportArrayID ) )
{
    $SupportArrayID = array( $SupportArrayID );
}

foreach ( $SupportArrayID as $supportID )
{
    eZBugSupport::delete( $supportID );
}

include_once( "classes/ezhttptool.php" );
eZHTTPTool::header( "Location: /bug/support/list/" );
exit();

?>
