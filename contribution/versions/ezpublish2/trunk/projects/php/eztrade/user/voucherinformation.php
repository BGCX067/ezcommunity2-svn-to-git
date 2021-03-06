<?php
// 
// $Id: voucherinformation.php 8919 2001-11-12 08:03:48Z ce $
//
// Created on: <06-Aug-2001 13:02:18 ce>
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
include_once( "classes/ezhttptool.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTradeMain", "Language" );
$locale = new eZLocale( $Language );

include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezuser.php" );

include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezvoucherinformation.php" );
include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezproductpricerange.php" );
include_once( "ezsession/classes/ezsession.php" );

$t = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                     "eztrade/user/intl/", $Language, "voucherinformation.php" );

$t->setAllStrings();

$t->set_file( "voucher_tpl", "voucherinformation.tpl" );

$t->set_block( "voucher_tpl", "email_tpl", "email" );
$t->set_block( "voucher_tpl", "smail_tpl", "smail" );

$t->set_block( "voucher_tpl", "price_to_high_tpl", "price_to_high" );
$t->set_block( "voucher_tpl", "price_to_low_tpl", "price_to_low" );

$t->set_block( "smail_tpl", "to_country_tpl", "to_country" );
$t->set_block( "smail_tpl", "from_country_tpl", "from_country" );

$t->set_block( "to_country_tpl", "to_country_option_tpl", "to_country_option" );
$t->set_block( "from_country_tpl", "from_country_option_tpl", "from_country_option" );

$GLOBALS["DEBUG"] = true;

setType( $PriceRange, "integer" );

$t->set_var( "to_email", "" );
$t->set_var( "to_name", "" );
$t->set_var( "from_name", "" );
$t->set_var( "smail_var", "" );
$t->set_var( "description", "" );
$t->set_var( "next", "" );
$t->set_var( "to_name_value", "" );
$t->set_var( "to_street1_value", "" );
$t->set_var( "to_street2_value", "" );
$t->set_var( "to_zip_value", "" );
$t->set_var( "to_place_value", "" );
$t->set_var( "from_name_value", "" );
$t->set_var( "from_street1_value", "" );
$t->set_var( "from_street2_value", "" );
$t->set_var( "from_zip_value", "" );
$t->set_var( "from_place_value", "" );
$t->set_var( "country_name", "" );
$t->set_var( "smail", "" );
$t->set_var( "email", "" );
$t->set_var( "from_email", "" );
$t->set_var( "price_to_low", "" );
$t->set_var( "price_to_high", "" );
$error = false;

$product = new eZProduct( $ProductID );

// Check for wrong price range
if ( isSet ( $OK ) )
{
    $range = $product->priceRange();
    if ( ( $range->min() != 0 ) && ( $range->min() > $PriceRange ) )
    {
        $error = true;
        $t->parse( "price_to_low", "price_to_low_tpl" );
    }
    if ( ( $range->max() != 0 ) && ( $range->max() < $PriceRange ) )
    {
        $error = true;
        $t->parse( "price_to_high", "price_to_high_tpl" );
    }
}

$user =& eZUser::currentUser();

// Insert the voucher information
if ( ( $product ) and ( isSet( $OK ) and $error == false ) )
{
    $voucherInfo = new eZVoucherInformation( $VoucherInformationID );
    $voucherInfo->setUser( $user );
    
    if ( $Mail == 1 )
    {
        $online = new eZOnline();
        $online->setUrl( $Email );
        $online->store();
        $voucherInfo->setToOnline( $online );

        $online = new eZOnline();
        $online->setUrl( $FromEmail );
        $online->store();
        $voucherInfo->setFromOnline( $online );
        
    }
    else if ( $Mail == 2 )
    {
        $toAddress = new eZAddress();
        $toAddress->setName( $ToName );
        $toAddress->setStreet1( $ToStreet1 );
        $toAddress->setStreet2( $ToStreet2 );
        $toAddress->setZip( $ToZip );
        $toAddress->setPlace( $ToPlace );
        $toAddress->store();
        $voucherInfo->setToAddress( $toAddress );

        $fromAddress = new eZAddress();
        $fromAddress->setName( $FromName );
        $fromAddress->setStreet1( $FromStreet1 );
        $fromAddress->setStreet2( $FromStreet2 );
        $fromAddress->setZip( $FromZip );
        $fromAddress->setPlace( $FromPlace );
        $fromAddress->store();
        $voucherInfo->setFromAddress( $fromAddress );
    }

    $voucherInfo->setMailMethod( $Mail );
    $voucherInfo->setFromName( $FromName );
    $voucherInfo->setFromName( $FromName );
    $voucherInfo->setToName( $ToName );
    
    $voucherInfo->setDescription( $Description );

    if ( $PriceRange == 0 )
    {
        $priceRange =& $product->priceRange();
        $voucherInfo->setPrice( $priceRange->min() );
    }
    else
        $voucherInfo->setPrice( $PriceRange );

    $voucherInfo->store();

    $id = $voucherInfo->id();

    $session->setVariable( "VoucherInformationID", $id );

    if ( isSet ( $OK ) && $id )
    {
        if ( $VoucherInformationID != "" )
                      eZHTTPTool::header( "Location: /trade/cart/" );
        else
            eZHTTPTool::header( "Location: /trade/cart/add/$ProductID/" );
        exit();
    }
}
else if ( $product ) // Print out the addresses forms
{
    $range = $product->priceRange();

    $currency = new eZCurrency();
    $currency->setValue( $range->min() );
    $t->set_var( "min_price", $locale->format( $currency ) );
    $currency->setValue( $range->max() );
    $t->set_var( "max_price", $locale->format( $currency ) );
    $error = false;

    $t->set_var( "mail_method", $MailMethod );
    $t->set_var( "product_name", $product->name() );
    $t->set_var( "product_id", $product->id() );

    if ( $VoucherInformationID != 0 )
    {
        $info = new eZVoucherInformation( $VoucherInformationID );

        $infoUser =& $info->user();
        if ( $user->id() != $infoUser->id() )
        {
            eZHTTPTool::header( "Location: /error/403/" );
            exit();
        }
        
        $t->set_var( "description", $info->description() );

        $t->set_var( "price_range", $info->price() );
        
        if ( $info->mailMethod() == 1 )
        {
            $from =& $info->fromOnline();
            $to =& $info->toOnline();
            $t->set_var( "voucher_info_id", $info->id() );
            $t->set_var( "from_email", $from->url() );
            $t->set_var( "to_email", $to->url() );
            $t->set_var( "to_name", $info->toName() );
            $t->set_var( "from_name", $info->fromName() );
        }
        else if ( $info->mailMethod() == 2 )
        {
            $toAddress =& $info->toAddress();
            $t->set_var( "to_name", $toAddress->name() );
            $t->set_var( "to_street1_value", $toAddress->street1() );
            $t->set_var( "to_street2_value", $toAddress->street2() );
            $t->set_var( "to_zip_value", $toAddress->zip() );
            $t->set_var( "to_place_value", $toAddress->place() );
            $toCountry =& $toAddress->country();
            if ( $toCountry )
                $toCountryID = $toCountry->id();

            $fromAddress =& $info->fromAddress();

            $t->set_var( "from_name_value", $fromAddress->name() );
            $t->set_var( "from_street1_value", $fromAddress->street1() );
            $t->set_var( "from_street2_value", $fromAddress->street2() );
            $t->set_var( "from_zip_value", $fromAddress->zip() );
            $t->set_var( "from_place_value", $fromAddress->place() );
            $fromCountry =& $fromAddress->country();
            if ( $fromCountry )
                $fromCountryID = $fromCountry->id();

        }
    }
    else
    {
        $t->set_var( "voucher_info_id", "" );
        $t->set_var( "price_range", "" );
    }

    if ( $MailMethod == 1 )
    {
        $t->set_var( "smail", "" );
        $t->parse( "email", "email_tpl" );
    }
    else if ( $MailMethod == 2 )
    {
        $t->set_var( "email", "" );
        $t->parse( "smail", "smail_tpl" );
    }

    $countryList =& eZCountry::getAll();
    
    foreach ( $countryList as $country )
    {
        $t->set_var( "to_is_selected", $country->id() == $toCountryID ? "selected" : "" );
        $t->set_var( "from_is_selected", $country->id() == $fromCountryID ? "selected" : "" );
        $t->set_var( "country_id", $country->id() );
        $t->set_var( "country_name", $country->name() );
        $t->parse( "to_country_option", "to_country_option_tpl", true );
        $t->parse( "from_country_option", "from_country_option_tpl", true );
    }
    $t->parse( "to_country", "to_country_tpl" );
    $t->parse( "from_country", "from_country_tpl" );



}

$t->pparse( "output", "voucher_tpl" );
?>
