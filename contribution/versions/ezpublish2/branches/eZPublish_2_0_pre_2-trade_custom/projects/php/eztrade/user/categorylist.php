<?
// 
// $Id: categorylist.php 4466 2001-03-01 14:06:26Z jb $
//
// B�rd Farstad <bf@ez.no>
// Created on: <23-Nov-2000 09:23:42 bf>
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

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZTradeMain", "Language" );

include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezproductcategory.php" );

$t = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                     "eztrade/user/intl/", $Language, "categorylist.php" );

$t->set_file( "category_list_page_tpl", "categorylist.tpl" );

$t->set_block( "category_list_page_tpl", "category_list_tpl", "category_list" );
$t->set_block( "category_list_tpl", "category_tpl", "category" );


$t->setAllStrings();

$category = new eZProductCategory(  );
$category->get( $CategoryID );


$categoryList = $category->getByParent( $category );

// categories
$i=0;
foreach ( $categoryList as $categoryItem )
{
    $t->set_var( "category_id", $categoryItem->id() );

    $t->set_var( "category_name", $categoryItem->name() );

    $t->set_var( "category_description", $categoryItem->description() );

    $parent = $categoryItem->parent();
    
    if ( $categoryItem->parent() != 0 )
    {
        $parent = $categoryItem->parent();
        $t->set_var( "category_parent", $parent->name() );
    }
    else
    {
        $t->set_var( "category_parent", "&nbsp;" );
    }

    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }

    
    $t->parse( "category", "category_tpl", true );
    $i++;
}
if ( count( $categoryList ) == 0 )
{
    $t->set_var( "category_list", "" );
}
else
{
    $t->parse( "category_list", "category_list_tpl" );
}



if ( $GenerateStaticPage == "true" )
{
    $cachedFile = "eztrade/cache/cateorylist," . $CategoryID .".cache";
    $fp = fopen ( $cachedFile, "w+");

    $output = $t->parse( $target, "category_list_page_tpl" );
    // print the output the first time while printing the cache file.
    print( $output );
    fwrite ( $fp, $output );
    fclose( $fp );
}
else
{
    $t->pparse( "output", "category_list_page_tpl" );
}


?>
