<?php
// 
// $Id: headlines.php 7335 2001-08-31 09:21:59Z th $
//
// Created on: <30-Nov-2000 14:35:24 bf>
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

include_once( "ezarticle/classes/ezarticlecategory.php" );
include_once( "ezarticle/classes/ezarticle.php" );
include_once( "ezarticle/classes/ezarticlerenderer.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZArticleMain", "Language" );
$ImageDir = $ini->read_var( "eZArticleMain", "ImageDir" );

$t = new eZTemplate( "ezarticle/user/" . $ini->read_var( "eZArticleMain", "TemplateDir" ),
                     "ezarticle/user/intl/", $Language, "headlines.php" );

$t->setAllStrings();

$t->set_file( array(
    "article_list_page_tpl" => "headlines.tpl"
    ) );


// product
$t->set_block( "article_list_page_tpl", "article_list_tpl", "article_list" );
$t->set_block( "article_list_tpl", "article_item_tpl", "article_item" );
$t->set_block( "article_item_tpl", "current_image_item_tpl", "current_image_item" );


// image dir
$t->set_var( "image_dir", $ImageDir );

if ( !isset( $Limit ) )
{
	$Limit = 10;
}

if ( !isset( $HeadlineOffset ) )
{
	$HeadlineOffset = 0;
}

// $category = new eZArticleCategory( $CategoryID );
$category = new eZArticleCategory( $CategoryID );

if ( $CategoryID == 0 )
{
    // do not set offset for the main page news
    // always sort by publishing date is the merged category
    $article = new eZArticle();
    $articleList =& $article->articles( "time", false, $HeadlineOffset, $Limit );
    $articleCount = $article->articleCount( false );
}
else
{
    $articleList =& $category->articles( $category->sortMode(), false, true, $HeadlineOffset, $Limit );
    $articleCount = $category->articleCount( false, true  );    
}


// should we allow currentuser to go get articles with permissions or should we not??
//$articleList = $category->articles( $SortMode, false, true, 0, 5 );

$locale = new eZLocale( $Language );
$i=0;
$t->set_var( "article_list", "" );
foreach ( $articleList as $article )
{
    $t->set_var( "article_id", $article->id() );
    $t->set_var( "article_name", $article->name() );

    // category image/icon
    $catDef = $article->categoryDefinition();

    $image =& $catDef->image();

    $t->set_var( "current_image_item", "" );
        
    if ( ( get_class( $image ) == "ezimage" ) && ( $image->id() != 0 ) )
    {
        $imageWidth =& $ini->read_var( "eZArticleMain", "CategoryImageWidth" );
        $imageHeight =& $ini->read_var( "eZArticleMain", "CategoryImageHeight" );

        $variation =& $image;

        $imageURL = $variation->filePath( );
        $imageWidth =& $variation->width();
        $imageHeight =& $variation->height();
        $imageCaption =& $image->caption();
            
        $t->set_var( "current_image_width", $imageWidth );
        $t->set_var( "current_image_height", $imageHeight );
        $t->set_var( "current_image_url", $imageURL );
        $t->set_var( "current_image_caption", $imageCaption );
        $t->parse( "current_image_item", "current_image_item_tpl" );
    }
    else
    {
        $t->set_var( "current_image_item", "" );
    }

    $published =& $article->published();
    $date =& $published->date();

    $t->set_var( "article_published", $locale->format( $date ) );    

    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
        $t->set_var( "td_alt", "1" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
        $t->set_var( "td_alt", "2" );
    }

    if ( $article->linkText() != "" )
    {
        $t->set_var( "article_link_text", $article->linkText() );
    }
    else
    {
        $t->set_var( "article_link_text", "more" );
    }

    $t->parse( "article_item", "article_item_tpl", true );
    $i++;
}

if ( count( $articleList ) > 0 )    
    $t->parse( "article_list", "article_list_tpl" );
else
    $t->set_var( "article_list", "" );


$t->pparse( "output", "article_list_page_tpl" );

?>

