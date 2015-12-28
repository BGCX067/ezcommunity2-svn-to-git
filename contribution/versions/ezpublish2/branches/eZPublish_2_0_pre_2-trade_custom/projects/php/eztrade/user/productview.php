<?
// 
// $Id: productview.php 4851 2001-03-12 10:59:55Z jb $
//
// B�rd Farstad <bf@ez.no>
// Created on: <24-Sep-2000 12:20:32 bf>
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
include_once( "classes/eztexttool.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTradeMain", "Language" );
$ShowPriceGroups = $ini->read_var( "eZTradeMain", "PriceGroupsEnabled" ) == "true";
$RequireUserLogin = $ini->read_var( "eZTradeMain", "RequireUserLogin" ) == "true";
$SimpleOptionHeaders = $ini->read_var( "eZTradeMain", "SimpleOptionHeaders" ) == "true";
$locale = new eZLocale( $Language );

$CapitalizeHeadlines = $ini->read_var( "eZArticleMain", "CapitalizeHeadlines" );

$MainImageWidth = $ini->read_var( "eZTradeMain", "MainImageWidth" );
$MainImageHeight = $ini->read_var( "eZTradeMain", "MainImageHeight" );

$SmallImageWidth = $ini->read_var( "eZTradeMain", "SmallImageWidth" );
$SmallImageHeight = $ini->read_var( "eZTradeMain", "SmallImageHeight" );

include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "eztrade/classes/ezoption.php" );
include_once( "eztrade/classes/ezpricegroup.php" );
include_once( "eztrade/classes/ezproductcurrency.php" );
include_once( "ezuser/classes/ezuser.php" );

$user = eZUser::currentUser();

if ( !isset( $IntlDir ) )
    $IntlDir = "eztrade/user/intl";
if ( !isset( $IniFile ) )
    $IniFile = "productview.php";

$t = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                     $IntlDir, $Language, $IniFile );

$t->setAllStrings();

if ( !isset( $productview ) )
    $productview = "productview.tpl";

if ( isset( $template_array ) and isset( $variable_array ) and
     is_array( $template_array ) and is_array( $variable_array ) )
{
    $standard_array = array( "product_view_tpl" => $productview );
    $temp_arr = array_merge( $standard_array, $template_array );
    $t->set_file( $temp_arr );
    $t->set_file_block( $template_array );
    if ( isset( $block_array ) and is_array( $block_array ) )
        $t->set_block( $block_array );
    $t->parse( $variable_array );
}
else
{
    $t->set_var( "extra_product_info", "" );
    $t->set_file( "product_view_tpl", $productview );
}

$t->set_block( "product_view_tpl", "product_number_item_tpl", "product_number_item" );
$t->set_block( "product_view_tpl", "price_tpl", "price" );
$t->set_block( "price_tpl", "alternative_currency_list_tpl", "alternative_currency_list" );
$t->set_block( "alternative_currency_list_tpl", "alternative_currency_tpl", "alternative_currency" );

$t->set_block( "product_view_tpl", "add_to_cart_tpl", "add_to_cart" );

$t->set_block( "product_view_tpl", "path_tpl", "path" );
$t->set_block( "product_view_tpl", "image_list_tpl", "image_list" );
$t->set_block( "image_list_tpl", "image_tpl", "image" );
$t->set_block( "product_view_tpl", "main_image_tpl", "main_image" );
$t->set_block( "product_view_tpl", "option_tpl", "option" );
$t->set_block( "option_tpl", "value_price_header_tpl", "value_price_header" );
$t->set_block( "value_price_header_tpl", "value_description_header_tpl", "value_description_header" );
$t->set_block( "value_price_header_tpl", "value_price_header_item_tpl", "value_price_header_item" );
$t->set_block( "value_price_header_tpl", "value_currency_header_item_tpl", "value_currency_header_item" );
$t->set_block( "option_tpl", "value_tpl", "value" );
$t->set_block( "value_tpl", "value_description_tpl", "value_description" );
$t->set_block( "value_tpl", "value_price_item_tpl", "value_price_item" );
$t->set_block( "value_tpl", "value_price_currency_list_tpl", "value_price_currency_list" );
$t->set_block( "value_price_currency_list_tpl", "value_price_currency_item_tpl", "value_price_currency_item" );
$t->set_block( "product_view_tpl", "external_link_tpl", "external_link" );

$t->set_block( "product_view_tpl", "attribute_list_tpl", "attribute_list" );
$t->set_block( "attribute_list_tpl", "attribute_tpl", "attribute" );

$t->set_block( "product_view_tpl", "numbered_page_link_tpl", "numbered_page_link" );
$t->set_block( "product_view_tpl", "print_page_link_tpl", "print_page_link" );

if ( !isset( $ModuleName ) )
    $ModuleName = "trade";
if ( !isset( $ModuleList ) )
    $ModuleList = "productlist";
if ( !isset( $ModuleView ) )
    $ModuleView = "productview";
if ( !isset( $ModulePrint ) )
    $ModulePrint = "productprint";

$t->set_var( "module", $ModuleName );
$t->set_var( "module_list", $ModuleList );
$t->set_var( "module_view", $ModuleView );
$t->set_var( "module_print", $ModulePrint );

$category = new eZProductCategory(  );
$category->get( $CategoryID );

$pathArray =& $category->path();


$t->set_var( "path", "" );
foreach ( $pathArray as $path )
{
    $t->set_var( "category_id", $path[0] );

    $t->set_var( "category_name", $path[1] );
    
    $t->parse( "path", "path_tpl", true );
}

$product = new eZProduct( $ProductID );

$mainImage = $product->mainImage();
if ( $mainImage )
{
    $variation = $mainImage->requestImageVariation( $MainImageWidth, $MainImageHeight );
    
    $t->set_var( "main_image_id", $mainImage->id() );
    $t->set_var( "main_image_uri", "/" . $variation->imagePath() );
    $t->set_var( "main_image_width", $variation->width() );
    $t->set_var( "main_image_height", $variation->height() );
    $t->set_var( "main_image_caption", $mainImage->caption() );

    $mainImageID = $mainImage->id();

    $t->parse( "main_image", "main_image_tpl" );    
}
else
{
    $t->set_var( "main_image", "" );    
}


if ( $CapitalizeHeadlines == "enabled" )
{
    include_once( "classes/eztexttool.php" );
    $t->set_var( "title_text", eZTextTool::capitalize( $product->name() ) );
}
else
{        
    $t->set_var( "title_text", $product->name() );
} 
$t->set_var( "intro_text", $product->brief() );
$t->set_var( "description_text", eZTextTool::nl2br( $product->description() ) );

$images = $product->images();

$i=0;
$t->set_var( "image", "" );
$t->set_var( "image_list", "" );
$image_count = 0;
foreach ( $images as $image )
{
    if ( $image->id() != $mainImageID )
    {
        if ( ( $i % 2 ) == 0 )
        {
            $t->set_var( "td_class", "bglight" );
        }
        else
        {
            $t->set_var( "td_class", "bgdark" );
        }
    
        $t->set_var( "image_name", $image->name() );

        $t->set_var( "image_title", $image->name() );
        $t->set_var( "image_caption", eZTextTool::nl2br( $image->caption() ) );
        $t->set_var( "image_id", $image->id() );
        $t->set_var( "product_id", $ProductID );

        $variation = $image->requestImageVariation( $SmallImageWidth, $SmallImageHeight );
    
        $t->set_var( "image_url", "/" .$variation->imagePath() );
        $t->set_var( "image_width", $variation->width() );
        $t->set_var( "image_height", $variation->height() );
    
        $t->parse( "image", "image_tpl", true );

        $image_count++;
        $i++;
    }
}

if ( $image_count > 0 )
    $t->parse( "image_list", "image_list_tpl" );

$options = $product->options();

$t->set_var( "option", "" );

$t->set_var( "value_price_header", "" );
if ( $ShowPrice and $product->showPrice() == true  )
    $t->parse( "value_price_header", "value_price_header_tpl" );

// show alternative currencies
$currency = new eZProductCurrency( );
$currencies =& $currency->getAll();
$t->set_var( "currency_count", count( $currencies ) );
$t->set_var( "value_price_header_item", "" );
$t->set_var( "value_currency_header_item", "" );
if ( !$RequireUserLogin or get_class( $user ) == "ezuser"  )
{
    $t->parse( "value_price_header_item", "value_price_header_item_tpl" );
    if ( count( $currencies ) > 0 )
        $t->parse( "value_currency_header_item", "value_currency_header_item_tpl" );
}

$currency_locale = new eZLocale( $Language );
foreach ( $options as $option )
{
    $values = $option->values();

    $t->set_var( "value", "" );    
    $i = 0;
    $headers = $option->descriptionHeaders();
    $t->set_var( "value_description_header", "" );
    if ( $SimpleOptionHeaders )
    {
        $t->set_var( "description_header", $headers[0] );
        $t->parse( "value_description_header", "value_description_header_tpl" );
    }
    else
    {
        foreach( $headers as $header )
        {
            $t->set_var( "description_header", $header );
            $t->parse( "value_description_header", "value_description_header_tpl", true );
        }
    }

    foreach ( $values as $value )
    {
        $t->set_var( "value_td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );
        $id = $value->id();

        $descriptions = $value->descriptions();
        $t->set_var( "value_description", "" );
        if ( $SimpleOptionHeaders )
        {
            $t->set_var( "value_id", $value->id() );
            
            $t->set_var( "value_name", $descriptions[0] );
            $t->parse( "value_description", "value_description_tpl" );
        }
        else
        {
            foreach( $descriptions as $description )
            {
                $t->set_var( "value_name", $description );
                $t->parse( "value_description", "value_description_tpl", true );
            }
        }

        $t->set_var( "value_price", "" );
        $t->set_var( "value_price_item", "" );
        $t->set_var( "value_price_currency_list", "" );
        if ( ( !$RequireUserLogin or get_class( $user ) == "ezuser"  ) and
             $ShowPrice and $product->showPrice() == true  )
        {
            $found_price = false;
            if ( $ShowPriceGroups and $PriceGroup > 0 )
            {
                $price = eZPriceGroup::correctPrice( $product->id(), $PriceGroup,
                                                     $option->id(), $value->id() );
                if ( $price )
                {
                    $found_price = true;
                    $price = new eZCurrency( $price );
                }
            }
            if ( !$found_price )
            {
                $price = new eZCurrency( $value->price() );
            }

            $t->set_var( "value_price", $locale->format( $price ) );

            $t->parse( "value_price_item", "value_price_item_tpl" );

            $t->set_var( "value_price_currency_item", "" );
            foreach ( $currencies as $currency )
            {
                $altPrice = $price;
                $altPrice->setValue( $price->value() * $currency->value() );

                $currency_locale->setSymbol( $currency->sign() );
                $currency_locale->setPrefixSymbol( $currency->prefixSign() );

                $t->set_var( "alt_value_price", $currency_locale->format( $altPrice ) );
                $t->parse( "value_price_currency_item", "value_price_currency_item_tpl", true );
            }

            $t->set_var( "value_price_currency_list", "" );
            if ( count( $currencies ) > 0 )
                $t->parse( "value_price_currency_list", "value_price_currency_list_tpl" );        
        }

        

        $t->parse( "value", "value_tpl", true );    
        $i++;
    }

    $t->set_var( "option_name", $option->name() );
    $t->set_var( "option_description", $option->description() );
    $t->set_var( "option_id", $option->id() );
    $t->set_var( "product_id", $ProductID );

    $t->parse( "option", "option_tpl", true );
}

// attribute list
$type = $product->type();
if ( $type )    
{
    $attributes = $type->attributes();

    $i=0;
    foreach ( $attributes as $attribute )
    {
        if ( ( $i % 2 ) == 0 )
        {
            $t->set_var( "begin_tr", "<tr>" );
            $t->set_var( "end_tr", "" );        
        }
        else
        {
            $t->set_var( "begin_tr", "" );
            $t->set_var( "end_tr", "</tr>" );
        }

        $value =& $attribute->value( $product );
        $t->set_var( "attribute_id", $attribute->id( ) );
        $t->set_var( "attribute_name", $attribute->name( ) );
        $t->set_var( "attribute_value", $value );

        if ( $value )
            $t->parse( "attribute", "attribute_tpl", true );
        
        $i++;
    }
}

if ( count ( $attributes ) > 0 )
{
    $t->parse( "attribute_list", "attribute_list_tpl" );
}
else
{
    $t->set_var( "attribute_list", "" );
}


$t->set_var( "product_id", $product->id() );

if ( trim( $product->externalLink() ) != "" )
{
    $t->set_var( "external_link_url", "http://" . $product->externalLink() );
    $t->parse( "external_link", "external_link_tpl" );
}
else
{
    $t->set_var( "external_link", "" );
}

$t->set_var( "product_number_item", "" );
if ( $product->productNumber() != "" )
{
    $t->set_var( "product_number", $product->productNumber() );
    $t->parse( "product_number_item", "product_number_item_tpl" );
}

if ( ( !$RequireUserLogin or get_class( $user ) == "ezuser"  ) and
     $ShowPrice and $product->showPrice() == true and $product->hasPrice()  )
{
    $found_price = false;
    if ( $ShowPriceGroups and $PriceGroup > 0 )
    {
        $price = eZPriceGroup::correctPrice( $product->id(), $PriceGroup );
        if ( $price )
        {
            $found_price = true;
            $price = new eZCurrency( $price );
        }
    }
    if ( !$found_price )
    {
        $price = new eZCurrency( $product->price() );
    }

    $t->set_var( "product_price", $locale->format( $price ) );

    // show alternative currencies

    $currency = new eZProductCurrency( );
    $currencies =& $currency->getAll();

    foreach ( $currencies as $currency )
    {
        $altPrice = $price;
        $altPrice->setValue( $price->value() * $currency->value() );

        $locale->setSymbol( $currency->sign() );

        if ( $currency->prefixSign() )
        {
            $locale->setPrefixSymbol( true );
        }
        else
        {
            $locale->setPrefixSymbol( false );
        }

        $t->set_var( "alt_price", $locale->format( $altPrice ) );

        $t->parse( "alternative_currency", "alternative_currency_tpl", true );
    }

    if ( count( $currencies ) > 0 )
    {
        $t->parse( "alternative_currency_list", "alternative_currency_list_tpl" );
    }
    else
    {
        $t->set_var( "alternative_currency_list", "" );
    }

    $t->parse( "price", "price_tpl" );
    $t->parse( "add_to_cart", "add_to_cart_tpl" );
}
else
{
    $t->set_var( "price", "" );
    $t->set_var( "add_to_cart", "" );
}

if ( $PrintableVersion == "enabled" )
{
    $t->parse( "numbered_page_link", "numbered_page_link_tpl" );
    $t->set_var( "print_page_link", "" );
}
else
{
    $t->parse( "print_page_link", "print_page_link_tpl" );
    $t->set_var( "numbered_page_link", "" );
}

if ( isset( $func_array ) and is_array( $func_array ) )
{
    foreach( $func_array as $func )
    {
        $func( $t, $ProductID );
    }
}

if ( $GenerateStaticPage == "true" )
{
    $template_var = "product_view_tpl";

    $output = $t->parse($target, $template_var );
    // print the output the first time while printing the cache file.
    print( $output );
    $CacheFile->store( $output );
}
else
{
    $t->pparse( "output", "product_view_tpl" );
}


?>