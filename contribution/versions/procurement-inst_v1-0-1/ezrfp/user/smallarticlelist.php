<?php
// 
// $Id: smallrfplist.php,v 1.11.2.3 2001/11/05 16:44:16 th Exp $
//
// Created on: <18-Oct-2000 14:41:37 bf>
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

include_once( "ezrfp/classes/ezrfpcategory.php" );
include_once( "ezrfp/classes/ezrfp.php" );
include_once( "ezrfp/classes/ezrfprenderer.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZRfpMain", "Language" );
$ImageDir = $ini->read_var( "eZRfpMain", "ImageDir" );
$CapitalizeHeadlines = $ini->read_var( "eZRfpMain", "CapitalizeHeadlines" );
$DefaultLinkText =  $ini->read_var( "eZRfpMain", "DefaultLinkText" );
$PageCaching = $ini->read_var( "eZRfpMain", "PageCaching" );

unset( $menuCachedFile );
// do the caching
if ( $PageCaching == "enabled" )
{
    $menuCachedFile = "ezrfp/cache/smallrfplist,". $GlobalSiteDesign .".cache";

    if ( eZFile::file_exists( $menuCachedFile ) )
    {
        include( $menuCachedFile );
    }
    else
    {
        createSmallRfpList( true );
    }            
}
else
{
    createSmallRfpList();
}

function createSmallRfpList( $generateStaticPage = false )
{
    global $ini;
    global $menuCachedFile;
    global $noItem;
	global $GlobalSiteDesign;
    global $CategoryID;
    global $Offset;
    global $Limit;
    global $Language;
	global $DefaultLinkText;

    $t = new eZTemplate( "ezrfp/user/" . $ini->read_var( "eZRfpMain", "TemplateDir" ),
                         "ezrfp/user/intl/", $Language, "smallrfplist.php" );

    $t->setAllStrings();

    $t->set_file( "rfp_list_page_tpl", "smallrfplist.tpl" );

    $t->set_block( "rfp_list_page_tpl", "rfp_list_tpl", "rfp_list" );
    $t->set_block( "rfp_list_tpl", "rfp_item_tpl", "rfp_item" );
    $t->set_block( "rfp_item_tpl", "read_more_tpl", "read_more_item" );

    $category = new eZRfpCategory( $CategoryID );

    $t->set_var( "current_category_name", $category->name() );
    $t->set_var( "current_category_description", $category->description() );

    $t->set_var( "sitedesign", $GlobalSiteDesign );

    $rfpList = $category->rfps( $category->sortMode(), false, true, $Offset, $Limit );

    $locale = new eZLocale( $Language );
    $i = 0;
    $t->set_var( "rfp_list", "" );
    foreach ( $rfpList as $rfp )
    {
        $nr = ( $i % 2 ) + 1;
        $t->set_var( "alt_nr", $nr );

        $t->set_var( "rfp_id", $rfp->id() );
        $t->set_var( "rfp_name", $rfp->name() );

        $renderer = new eZRfpRenderer( $rfp );

        $published = $rfp->published();
        
        $t->set_var( "rfp_published", $locale->format( $published ) );

        $t->set_var( "category_id", $CategoryID );

        $t->set_var( "rfp_intro", $renderer->renderIntro(  ) );

	    $contents =& $renderer->renderPage();

        if ( $rfp->linkText() != "" )
        {
            $t->set_var( "rfp_link_text", $rfp->linkText() );
        	$t->parse( "read_more_item", "read_more_tpl" );		
        }
        else if ( !( trim( $contents[1] ) == "" && count( $rfp->attributes( false ) ) <= 0 ))
        {
            $t->set_var( "rfp_link_text", $DefaultLinkText );
        	$t->parse( "read_more_item", "read_more_tpl" );
        }
		else
		{
            $t->set_var( "rfp_link_text", "" );
        	$t->set_var( "read_more_item", "" );
		}
		
        $t->parse( "rfp_item", "rfp_item_tpl", true );
        $i++;
    }

    if ( count( $rfpList ) > 0 )    
        $t->parse( "rfp_list", "rfp_list_tpl" );
    else
        $t->set_var( "rfp_list", "" );


    if ( $generateStaticPage )
    {
        $fp = eZFile::fopen( $menuCachedFile, "w+");

        $output = $t->parse( $target, "rfp_list_page_tpl" );
        // print the output the first time while printing the cache file.
    
        print( $output );
        fwrite ( $fp, $output );
        fclose( $fp );
    }
    else
    {
        $t->pparse( "output", "rfp_list_page_tpl" );
    }
}

?>