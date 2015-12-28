<?
// 
// $Id: mastercard.php 4691 2001-03-07 11:37:47Z bf $
//
// B�rd Farstad <bf@ez.no>
// Created on: <02-Feb-2001 17:42:45 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2000 eZ systems as
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
include_once( "classes/ezhttptool.php" );
include_once( "classes/ezcctool.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTradeMain", "Language" );

if ( $Action == "Verify" )
{
    // add CC clearing code here:
    $PaymentSuccess = "true";        
}

$t = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                     "eztrade/user/intl/", $Language, "mastercard.php" );

$t->set_file( "mastercard_tpl", "mastercard.tpl" );

$t->setAllStrings();

$t->set_var( "order_id", $OrderID );
$t->set_var( "payment_type", $PaymentType );

$t->pparse( "output", "mastercard_tpl" );
?>
