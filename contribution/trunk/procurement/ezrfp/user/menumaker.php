<?php
// 
// $Id: menumaker.php,v 1.5.2.1 2001/11/05 16:44:15 th Exp $
//
// Definition of ||| class
//
// Bj�rn Reiten <br@ez.no>
// Created on: <29-Aug-2001 16:47:13 br>
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

//!! |||
//! 
/*!
 
  Example code:
  \code
  \endcode

*/

function menuMaker()
{
    global $CategoryID;
    global $GlobalSiteDesign;
    include_once( "classes/INIFile.php" );
    include_once( "classes/ezcachefile.php" );
    include_once( "classes/eztemplate.php" );
    
    include_once( "ezrfp/classes/ezrfpcategory.php" );
    include_once( "ezrfp/classes/ezrfp.php" );
    include_once( "ezuser/classes/ezobjectpermission.php" );
    
    $ini =& INIFile::globalINI();
    
    $Language = $ini->read_var( "eZRfpMain", "Language" );
    $t = new eZTemplate( "ezrfp/user/" . $ini->read_var( "eZRfpMain", "TemplateDir" ),
                         "ezrfp/user/intl", $Language, "menumaker.php" );
    
    $t->setAllStrings();
    
    $t->set_file( array(
        "menu_maker_tpl" => "menumaker.tpl"
        ) );
    
    $t->set_block( "menu_maker_tpl", "menu_box_tpl", "menu_box" );
    $t->set_block( "menu_box_tpl", "menu_header_tpl", "menu_header" );
    $t->set_block( "menu_box_tpl", "menu_rfp_tpl", "menu_rfp" );
    $t->set_block( "menu_box_tpl", "menu_category_tpl", "menu_category" );

    $t->set_var( "menu_box", "" );
    $t->set_var( "menu_header", "" );
    $t->set_var( "menu_category", "" );
    
    if ( !isset( $CategoryID  ) )
    {
        $category_id = 0;
    }
    else
    {
        $category_id = $CategoryID;
    }
    
    $rfpCategory = new eZRfpCategory( $category_id );
    $rfpCategory_array = $rfpCategory->getByParent( $rfpCategory );
    $rfp_art_array =& $rfpCategory->rfps( "absolute_placement", false, true, 0, 50 );
    $i = 0;

    if ( count( $rfp_art_array ) > 0 )
    {
        foreach( $rfp_art_array as $rfp_item )
        {
            $t->set_var( "sitedesign", $GlobalSiteDesign );
            $t->set_var( "rfp_link_text", $rfp_item->name() );
            $t->set_var( "rfp_id", $rfp_item->id() );
            $t->parse( "menu_rfp", "menu_rfp_tpl", true );
        }
    }
    if( count( $rfp_cat_array ) > 0 || count( $rfp_art_array ) > 0 )
    {
        $t->set_var( "current_category_name", $rfpCategory->name() );
        $t->parse( "menu_box", "menu_box_tpl", true );
    }
   
    foreach( $rfpCategory_array as $categoryItem )
    {
        $t->set_var( "menu_header", "" );
        $t->set_var( "menu_rfp", "" );
        $t->set_var( "menu_category", "" );
        

        $rfp_cat_array = $categoryItem->getByParent( $categoryItem );
        $rfp_art_array =& $categoryItem->rfps( "absolute_placement", false, true, 0, 50 );
        
        
        if ( count( $rfp_cat_array ) > 0 )
        {
            foreach( $rfp_cat_array as $rfp_item )
            {
                $t->set_var( "sitedesign", $GlobalSiteDesign );
                $t->set_var( "category_link_text", $rfp_item->name() );
                $t->set_var( "category_id", $rfp_item->id() );
                $t->parse( "menu_category", "menu_category_tpl", true );
            }
        }
        
        if ( count( $rfp_art_array ) > 0 )
        {
            foreach( $rfp_art_array as $rfp_item )
            {
                $t->set_var( "sitedesign", $GlobalSiteDesign );
                $t->set_var( "rfp_link_text", $rfp_item->name() );
                $t->set_var( "rfp_id", $rfp_item->id() );
                $t->parse( "menu_rfp", "menu_rfp_tpl", true );
            }
        }
        
        if( count( $rfp_cat_array ) > 0 || count( $rfp_art_array ) > 0 )
        {
            $t->set_var( "current_category_name", $categoryItem->name() );
            $t->parse( "menu_header", "menu_header_tpl" );
            $t->parse( "menu_box", "menu_box_tpl", true );
        }
    }
    $t->pparse( "output", "menu_maker_tpl" );

}
?>
