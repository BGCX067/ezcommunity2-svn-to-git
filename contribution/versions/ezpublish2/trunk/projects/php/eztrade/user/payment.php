<?php
// 
// $Id: payment.php 9174 2001-11-26 18:16:50Z br $
//
// Created on: <02-Feb-2001 16:31:53 bf>
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

unset( $PaymentSuccess );

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlocale.php" );
include_once( "classes/ezcurrency.php" );

include_once( "classes/ezhttptool.php" );
include_once( "classes/ezcachefile.php" );

include_once( "ezuser/classes/ezuser.php" );

include_once( "eztrade/classes/ezproduct.php" );
include_once( "eztrade/classes/ezoption.php" );
include_once( "eztrade/classes/ezoptionvalue.php" );
include_once( "eztrade/classes/ezcart.php" );
include_once( "eztrade/classes/ezcartitem.php" );
include_once( "eztrade/classes/ezcartoptionvalue.php" );
include_once( "eztrade/classes/ezpreorder.php" );
include_once( "eztrade/classes/ezorder.php" );
include_once( "eztrade/classes/ezorderitem.php" );
include_once( "eztrade/classes/ezorderoptionvalue.php" );
include_once( "eztrade/classes/ezwishlist.php" );
include_once( "eztrade/classes/ezcheckout.php" );
include_once( "eztrade/classes/ezvoucher.php" );
include_once( "eztrade/classes/ezvoucherused.php" );
include_once( "eztrade/classes/ezvoucheremail.php" );
include_once( "eztrade/classes/ezvouchersmail.php" );

include_once( "ezcontact/classes/ezperson.php" );
include_once( "ezcontact/classes/ezcompany.php" );

include_once( "ezsession/classes/ezsession.php" );
include_once( "ezmail/classes/ezmail.php" );
include_once( "classes/ezgpg.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTradeMain", "Language" );
$OrderSenderEmail = $ini->read_var( "eZTradeMain", "OrderSenderEmail" );
$OrderReceiverEmail = $ini->read_var( "eZTradeMain", "OrderReceiverEmail" );
$ShowPriceGroups = $ini->read_var( "eZTradeMain", "PriceGroupsEnabled" ) == "true";
$PricesIncludeVAT = $ini->read_var( "eZTradeMain", "PricesIncludeVAT" ) == "enabled" ? true : false;
$ShowExTaxColumn = $ini->read_var( "eZTradeMain", "ShowExTaxColumn" ) == "enabled" ? true : false;
$ShowIncTaxColumn = $ini->read_var( "eZTradeMain", "ShowIncTaxColumn" ) == "enabled" ? true : false;
$ShowExTaxTotal = $ini->read_var( "eZTradeMain", "ShowExTaxTotal" ) == "enabled" ? true : false;
$ColSpanSizeTotals = $ini->read_var( "eZTradeMain", "ColSpanSizeTotals" );
$DiscontinueQuantityless = $ini->read_var( "eZTradeMain", "DiscontinueQuantityless" ) == "true";
$SiteURL =  $ini->read_var( "site", "SiteURL" );
$PricesIncludeVAT = $ini->read_var( "eZTradeMain", "PricesIncludeVAT" );

// Set some variables to defaults.
$ShowCart = false;
$ShowSavingsColumn = false;

function deleteCache( $ProductID, $CategoryID, $CategoryArray, $Hotdeal )
{
    if ( get_class( $ProductID ) == "ezproduct" )
    {
        $CategoryID =& $ProductID->categoryDefinition( false );
        $CategoryArray =& $ProductID->categories( false );
        $Hotdeal = $ProductID->isHotDeal();
        $ProductID = $ProductID->id();
    }

    $files = eZCacheFile::files( "eztrade/cache/", array( array( "productview", "productprint" ),
                                                          $ProductID, $CategoryID ),
                                 "cache", "," );
    foreach ( $files as $file )
    {
        $file->delete();
    }
    $files = eZCacheFile::files( "eztrade/cache/",
                                 array( "productlist",
                                        array_merge( $CategoryID, $CategoryArray ) ),
                                 "cache", "," );
    foreach ( $files as $file )
    {
        $file->delete();
    }
    
    if ( $Hotdeal )
    {
        $files = eZCacheFile::files( "eztrade/cache/", array( "hotdealslist", NULL ),
                                     "cache", "," );
        foreach ( $files as $file )
        {
            $file->delete();
        }
    }
}

$session =& eZSession::globalSession();

// fetch the cart
$cart = new eZCart();
$cart = $cart->getBySession( $session, "Cart" );

if ( !$cart )
{
    $orderCompletedID = $session->variable( "OrderCompletedID" ) ;

    if( $orderCompletedID > 0 )
    {
        $user =& eZUser::currentUser();
        $order = new eZOrder( $orderCompletedID );
        $orderUser = $order->user();
        
        if ( $user->id() == $orderUser->id() )
        {
            eZHTTPTool::header( "Location: http://$HTTP_HOST/trade/ordersendt/$orderCompletedID/" );
            exit();
        }
        else
        {
            eZHTTPTool::header( "Location: /trade/cart/" );
            exit();
        }
    }
    else
    {
        eZHTTPTool::header( "Location: /trade/cart/" );
        exit();
    }
}

$items = $cart->items();

// generate a new checkout instance, must have a unique number
// for VISA clearing etc.

// this is the value to charge the customer with
$ChargeTotal = $session->variable( "TotalCost" ) ;

// this is the total vat.
$ChargeVATTotal = $session->variable( "TotalVAT" ) ;

// The comment from the user.
$Comment = $session->variable( "Comment" ) ;


$checkout = new eZCheckout();
$instance =& $checkout->instance();

//print( $ChargeTotal );

$PreOrderID = $session->variable( "PreOrderID" ) ;

$preOrder = new eZPreOrder( $PreOrderID );

// print( "Checkout number: " . $PreOrderID . "<br>" );

$paymentMethod = $session->arrayValue( "PaymentMethod" );

$paymentMethod = $paymentMethod[0];

$locale = new eZLocale( $Language );

if ( $paymentMethod == true and $paymentMethod != "voucher_done" )
    include( $instance->paymentFile( $paymentMethod ) );
else
$PaymentSuccess = "true";

$currency = new eZCurrency();

// create an order and empty the cart.
// only do this if the payment was OK.
if ( $PaymentSuccess == "true" ) 
{
    // create a new order
    $order = new eZOrder();
    $user =& eZUser::currentUser();
    $order->setUser( $user );

    if ( $ini->read_var( "eZTradeMain", "ShowBillingAddress" ) != "enabled" )
    {
        $billingAddressID = $shippingAddressID;
    }
    
    $shippingAddress = new eZAddress( $session->variable( "ShippingAddressID" ) );
    $billingAddress = new eZAddress( $session->variable( "BillingAddressID" ) );

    $order->setShippingCharge( $session->variable( "ShippingCost" ) );
    $order->setShippingVAT( $session->variable( "ShippingVAT" ) );

    $order->setShippingAddress( $shippingAddress, $user );
    $order->setBillingAddress( $billingAddress, $user );
    
    $order->setPaymentMethod( $session->arrayValue( "PaymentMethod" ) );

    $order->setShippingTypeID( $session->variable( "ShippingTypeID" ) );

    $order->setComment( $Comment );

    $order->setPersonID( $cart->personID() );
    $order->setCompanyID( $cart->companyID() );

    $order->setIsVATInc( false );
    

    // fetch the cart items
    $items = $cart->items();

    // exit if no items exist
    if ( count ( $items ) == 0 )
    {
       $orderCompletedID = $session->variable( "OrderCompletedID" ) ;
    
       if( $orderCompletedID > 0 )
       {
          eZHTTPTool::header( "Location: http://$HTTP_HOST/trade/ordersendt/$orderCompletedID/" );
          exit();
       }
       else
       {
          eZHTTPTool::header( "Location: /trade/cart/" );
          exit();
       }
    }

    $order->store();


    $order_id = $order->id();
    
    foreach ( $items as $item )
    {
        $totalVAT=0.0;
        $price=0.0;
        $totalPrice=0.0;
        $product = $item->product();

            // product price

        $priceobj = new eZCurrency();
        
        if ( ( !$RequireUserLogin or get_class( $user ) == "ezuser" ) and
             $ShowPrice and $product->showPrice() == true  )
        {
            $price = $item->correctPrice( false, true, false );
        }
        
        
        $totalVAT = $item->correctPrice( false, true, true ) - $price;
        $currency->setValue( $price );
        
// create a new order item
        $orderItem = new eZOrderItem();
        $orderItem->setOrder( $order );
        $orderItem->setProduct( $product );
        $orderItem->setCount( $item->count() );
        $orderItem->setPrice( $price );
        $orderItem->setVAT( $totalVAT );

        $expiryTime = $product->expiryTime();
        if ( $expiryTime > 0 )
            $orderItem->setExpiryDate( eZDateTime::timeStamp( true ) + ( $expiryTime * 86400 ) );
        else
            $orderItem->setExpiryDate( 0 );
    
        $orderItem->store();
    
        $optionValues =& $item->optionValues();
    
        foreach ( $optionValues as $optionValue )
        {
            $option =& $optionValue->option();
            $value =& $optionValue->optionValue();
        
            $orderOptionValue = new eZOrderOptionValue();
            $orderOptionValue->setOrderItem( $orderItem );
        
            $orderOptionValue->setRemoteID( $optionValue->remoteID() );
        
            $descriptions =& $value->descriptions();
        
            $orderOptionValue->setOptionName( $option->name() );
            $orderOptionValue->setValueName( $descriptions[0] );
            // fix
        
            $orderOptionValue->store();
        }
    }
    
    $cart->cartTotals( $tax, $total );

    //
    // Send mail confirmation
    //      
    $mailTemplate = new eZTemplate( "eztrade/user/" . $ini->read_var( "eZTradeMain", "TemplateDir" ),
                                    "eztrade/user/intl", $Language, "mailorder.php" );

    $mailTemplateIni = new INIFile( "eztrade/user/intl/" . $Language . "/ordersendt.php.ini", false );
    $mailTemplate->set_file( "order_sendt_tpl", "mailorder.tpl" );
    $mailTemplate->setAllStrings();

    $mailTemplate->set_block( "order_sendt_tpl", "billing_address_tpl", "billing_address" );
    $mailTemplate->set_block( "order_sendt_tpl", "shipping_address_tpl", "shipping_address" );
    $mailTemplate->set_block( "order_sendt_tpl", "order_item_list_tpl", "order_item_list" );

    $mailTemplate->set_block( "order_sendt_tpl", "full_cart_tpl", "full_cart" );
    $mailTemplate->set_block( "full_cart_tpl", "cart_item_list_tpl", "cart_item_list" );

    $mailTemplate->set_block( "cart_item_list_tpl", "cart_item_tpl", "cart_item" );

    $mailTemplate->set_block( "cart_item_tpl", "cart_item_option_tpl", "cart_item_option" );

    $mailTemplate->set_block( "full_cart_tpl", "tax_specification_tpl", "tax_specification" );
    $mailTemplate->set_block( "tax_specification_tpl", "tax_item_tpl", "tax_item" );

    // get the customer
    $user = $order->user();

    $currentUser =& eZUser::currentUser();

    // check if the user is logged in
    if ( !( $currentUser && $user ) ) 
    {
        eZHTTPTool::header( "Location: /trade/cart/" );
        exit();
    }

    // check if the user owns the order
    if ( $currentUser->id() != $user->id() )
    {
        eZHTTPTool::header( "Location: /trade/cart/" );
        exit();
    }

    if ( $user )
    {
        // print out the addresses
        $billingAddress = $order->billingAddress();

        $title =& $user->title();
        $mailTemplate->set_var( "customer_title", $title->name() );
        if ( $order->personID() == 0 && $order->companyID() == 0 )
        {
            $mailTemplate->set_var( "customer_first_name", $user->firstName() );
            $mailTemplate->set_var( "customer_last_name", $user->lastName() );
        }
        else
        {
            if ( $order->personID() > 0 )
            {
                $customer = new eZPerson( $order->personID() );
                $mailTemplate->set_var( "customer_first_name", $customer->firstName() );
                $mailTemplate->set_var( "customer_last_name", $customer->lastName() );
            }
            else
            {
                $customer = new eZCompany( $order->companyID() );
                $mailTemplate->set_var( "customer_first_name", $customer->name() );
                $mailTemplate->set_var( "customer_last_name", "" );
            }
        }

        $mailTemplate->set_var( "billing_street1", $billingAddress->street1() );
        $mailTemplate->set_var( "billing_street2", $billingAddress->street2() );
        $mailTemplate->set_var( "billing_zip", $billingAddress->zip() );
        $mailTemplate->set_var( "billing_place", $billingAddress->place() );

        $country = $billingAddress->country();

        if ( $country )
        {
            if ( $ini->read_var( "eZUserMain", "SelectCountry" ) == "enabled" )
                $mailTemplate->set_var( "billing_country", $country->name() );
            else
                $mailTemplate->set_var( "billing_country", "" );
        }
        else
        {
            $mailTemplate->set_var( "billing_country", "" );
        }

        if ( $ini->read_var( "eZTradeMain", "ShowBillingAddress" ) == "enabled" )
            $mailTemplate->parse( "billing_address", "billing_address_tpl" );
        else
            $mailTemplate->set_var( "billing_address", "" );

        
        if ( $order->personID() == 0 && $order->companyID() == 0 )
        {
            $shippingUser = $order->shippingUser();

            if ( $shippingUser )
            {
                $title =& $shippingUser->title();

                $mailTemplate->set_var( "shipping_first_name", $shippingUser->firstName() );
                $mailTemplate->set_var( "shipping_last_name", $shippingUser->lastName() );
            }
        }
        else
        {
            if ( $order->personID() > 0 )
            {
                $customer = new eZPerson( $order->personID() );
                $mailTemplate->set_var( "shipping_first_name", $customer->firstName() );
                $mailTemplate->set_var( "shipping_last_name", $customer->lastName() );
            }
            else
            {
                $customer = new eZCompany( $order->companyID() );
                $mailTemplate->set_var( "shipping_first_name", $customer->name() );
                $mailTemplate->set_var( "shipping_last_name", "" );
            }
        }

        #$mailTemplate->set_var( "shipping_title", $title->name() );
        
        $shippingAddress = $order->shippingAddress();

        $mailTemplate->set_var( "shipping_street1", $shippingAddress->street1() );
        $mailTemplate->set_var( "shipping_street2", $shippingAddress->street2() );
        $mailTemplate->set_var( "shipping_zip", $shippingAddress->zip() );
        $mailTemplate->set_var( "shipping_place", $shippingAddress->place() );

        $country = $shippingAddress->country();

        if ( $country )
        {
            if ( $ini->read_var( "eZUserMain", "SelectCountry" ) == "enabled" )
                $mailTemplate->set_var( "shipping_country", $country->name() );
            else
                $mailTemplate->set_var( "shipping_country", "" );
        }
        else
        {
            $mailTemplate->set_var( "shipping_country", "" );
        }

        $mailTemplate->parse( "shipping_address", "shipping_address_tpl" );
    }


    // fetch the order items
    $items = $order->items( $OrderType );


    $i = 0;
    $sum = 0.0;
    $totalVAT = 0.0;

    $numberOfItems = 0;
    $i = 0;

    $search="/&nbsp;/";
    $replace=" ";

    // Add headers!

    $productsForEmail[$i]["product_id"] = trim( $mailTemplateIni->read_var( "strings", "product_id" ) );
    $productsForEmail[$i]["product_name"] = trim( $mailTemplateIni->read_var( "strings", "product_name" ) );
    $productsForEmail[$i]["product_number"] = trim( $mailTemplateIni->read_var( "strings", "product_number" ) );
    $productsForEmail[$i]["product_price"] = trim( $mailTemplateIni->read_var( "strings", "product_price" ) );
    $productsForEmail[$i]["product_count"] = trim( $mailTemplateIni->read_var( "strings", "product_qty" ) );
    $productsForEmail[$i]["product_savings"] = trim( $mailTemplateIni->read_var( "strings", "product_savings" ) );
    $productsForEmail[$i]["product_total_ex_tax"] = trim( $mailTemplateIni->read_var( "strings", "product_total_ex_tax" ) );
    $productsForEmail[$i]["product_total_inc_tax"] = trim( $mailTemplateIni->read_var( "strings", "product_total_inc_tax" ) );

    foreach ( $items as $item )
    {
        $mailTemplate->set_var( "td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );
        $i++;
        $mailTemplate->set_var( "cart_item_id", $item->id() );
        $product =& $item->product();
        $vatPercentage = $product->vatPercentage();
        $productHasVAT = $product->priceIncVAT();

        $productID = $product->id();

        $productsForEmail[$i]["product_id"] = $productID;
        $productsForEmail[$i]["product_name"] = trim( $product->name() );
        $productsForEmail[$i]["product_number"] = trim( $product->productNumber() );
        $productsForEmail[$i]["product_price"] = preg_replace( $search, $replace, $item->localePrice( false, true, $PricesIncludeVAT ) );
        $productsForEmail[$i]["product_count"] = $item->count();
        $productsForEmail[$i]["product_total_ex_tax"] = preg_replace( $search, $replace, $item->localePrice( true, true, false ) );
        $productsForEmail[$i]["product_total_inc_tax"] = preg_replace( $search, $replace, $item->localePrice( true, true, true ) );
        $productsForEmail[$i]["product_savings"] = "";

        $numberOfItems++;

        $numberOfOptions = 0;

        $optionValues =& $item->optionValues();

        foreach ( $optionValues as $optionValue )
        {
            $productOptions[$productID][$numberOfOptions]["option_id"] = "";
            $productOptions[$productID][$numberOfOptions]["option_name"] = trim( $optionValue->valueName() );
            $productOptions[$productID][$numberOfOptions]["option_value"] = trim( $optionValue->optionName() ) . ": ";
            $productOptions[$productID][$numberOfOptions]["option_price"] = "";

            $numberOfOptions++;
        }
    }

    $separateBy = 2;

    $order->orderTotals( $tax, $total );

    $mailTemplate->set_var( "empty_cart", "" );

    if ( $ShowExTaxColumn == true )
    {
        $currency->setValue( $total["subextax"] );
        $subextax = $locale->format( $currency );
        $subextax = preg_replace( $search, $replace, $subextax );

        $currency->setValue( $total["extax"] );
        $extax =  $locale->format( $currency );
        $extax = preg_replace( $search, $replace, $extax );

        $currency->setValue( $total["shipextax"] );
        $shipextax =  $locale->format( $currency );
        $shipextax = preg_replace( $search, $replace, $shipextax );

        $len_product_total_ex_tax = strlen( $subextax ) > $len_product_total_ex_tax ? strlen( $subextax ) : $len_product_total_ex_tax;
        $len_product_total_ex_tax = strlen( $extax ) > $len_product_total_ex_tax ? strlen( $extax ) : $len_product_total_ex_tax;
        $len_product_total_ex_tax = strlen( $shipextax ) > $len_product_total_ex_tax ? strlen( $shipextax ) : $len_product_total_ex_tax;
    }

    if ( $ShowIncTaxColumn == true )
    {
        $currency->setValue( $total["subinctax"] );
        $subinctax = $locale->format( $currency );
        $subinctax = preg_replace( $search, $replace, $subinctax );

        $currency->setValue( $total["inctax"] );
        $inctax =  $locale->format( $currency );
        $inctax = preg_replace( $search, $replace, $inctax );

        $currency->setValue( $total["shipinctax"] );
        $shipinctax =  $locale->format( $currency );
        $shipinctax = preg_replace( $search, $replace, $shipinctax );

        $len_product_total_inc_tax = strlen( $subinctax ) > $len_product_total_inc_tax ? strlen( $subinctax ) : $len_product_total_inc_tax;
        $len_product_total_inc_tax = strlen( $inctax ) > $len_product_total_inc_tax ? strlen( $inctax ) : $len_product_total_inc_tax;
        $len_product_total_inc_tax = strlen( $shipinctax ) > $len_product_total_inc_tax ? strlen( $shipinctax ) : $len_product_total_inc_tax;
    }

    if ( count ( $productOptions ) > 0 )
    {
        foreach( $productOptions as $line )
        {
            $len_option_name = strlen( $line["option_name"] ) > $len_option_name ? strlen( $line["option_name"] ) : $len_option_name;
            $len_option_value = strlen( $line["option_value"] ) > $len_option_value ? strlen( $line["option_value"] ) : $len_option_value;
            $len_option_price = strlen( $line["option_price"] ) > $len_option_price ? strlen( $line["option_price"] ) : $len_option_price;
        }
    }
    
    $len_option_name += $separateBy;
    $len_option_value += $separateBy;
    $len_option_price += $separateBy;

    $optionLen = $len_option_name + $len_option_value;

    $len_product_name = $optionLen;

    foreach( $productsForEmail as $line )
    {
        $len_product_name = strlen( $line["product_name"] ) > $len_product_name ? strlen( $line["product_name"] ) : $len_product_name;
        $len_product_number = strlen( $line["product_number"] ) > $len_product_number ? strlen( $line["product_number"] ) : $len_product_number;
        $len_product_price = strlen( $line["product_price"] ) > $len_product_price ? strlen( $line["product_price"] ) : $len_product_price;
        $len_product_count = strlen( $line["product_count"] ) > $len_product_count ? strlen( $line["product_count"] ) : $len_product_count;
        $len_product_total_ex_tax = strlen( $line["product_total_ex_tax"] ) > $len_product_total_ex_tax ? strlen( $line["product_total_ex_tax"] ) : $len_product_total_ex_tax;
        $len_product_total_inc_tax = strlen( $line["product_total_inc_tax"] ) > $len_product_total_inc_tax ? strlen( $line["product_total_inc_tax"] ) : $len_product_total_inc_tax;
        $len_product_savings = strlen( $line["product_savings"] ) > $len_product_savings ? strlen( $line["product_savings"] ) : $len_product_savings;
    }

    $separateBy = 2;

    $items = "";

    $count = count( $productsForEmail );

    $len_product_number += $separateBy;
    $len_product_name += $separateBy;
    $len_product_price += $separateBy;
    $len_product_count += $separateBy;
    $len_product_total_ex_tax += $separateBy;
    $len_product_total_inc_tax += $separateBy;
    $len_product_savings += $separateBy;

    $lineFillLen = $len_product_number + $len_product_name + $len_product_price + $len_product_count;
    $totalsLen = $len_product_number + $len_product_name + $len_product_price + $len_product_count;
    $len_option_indent = $len_product_number;


    $i = 0;

    $headers = "";

    $headers = $headers . str_pad( $productsForEmail[$i]["product_number"], $len_product_number, " ", STR_PAD_RIGHT );
    $headers = $headers . str_pad( $productsForEmail[$i]["product_name"], $len_product_name, " ", STR_PAD_RIGHT );
    $headers = $headers . str_pad( $productsForEmail[$i]["product_price"], $len_product_price, " ", STR_PAD_LEFT );
    $headers = $headers . str_pad( $productsForEmail[$i]["product_count"], $len_product_count, " ", STR_PAD_LEFT );

    if ( $ShowExTaxColumn == true )
    {
        $headers = $headers . str_pad( $productsForEmail[$i]["product_total_ex_tax"], $len_product_total_ex_tax , " ", STR_PAD_LEFT );
        $lineFillLen += $len_product_total_ex_tax;
    }

    if ( $ShowIncTaxColumn == true )
    {
        $headers = $headers . str_pad( $productsForEmail[$i]["product_total_inc_tax"], $len_product_total_inc_tax, " ", STR_PAD_LEFT );
        $lineFillLen += $len_product_total_inc_tax;
    }

    if ( $ShowSavingsColumn == true )
    {
        $headers = $headers . str_pad( $productsForEmail[$i]["product_savings"], $len_product_savings, " ", STR_PAD_LEFT );
        $lineFillLen += $len_product_savings;
    }

    $mailTemplate->set_var( "headers", $headers );
    $mailTemplate->set_var( "hyphen_line", str_pad( "", $lineFillLen, "-", STR_PAD_LEFT ) );
    $mailTemplate->set_var( "equal_line", str_pad( "", $lineFillLen, "=", STR_PAD_LEFT ) );
    $mailTemplate->set_var( "cart_item", "" );

    for( $i = 1; $i < $count; $i++ )
    {
        $mailTemplate->set_var( "product_id", str_pad( $productsForEmail[$i]["product_id"], $len_product_id, " ", STR_PAD_RIGHT ) );
        $mailTemplate->set_var( "product_number", str_pad( $productsForEmail[$i]["product_number"], $len_product_number, " ", STR_PAD_RIGHT ) );
        $mailTemplate->set_var( "product_name", str_pad( $productsForEmail[$i]["product_name"], $len_product_name, " ", STR_PAD_RIGHT ) );
        $mailTemplate->set_var( "product_price", str_pad( $productsForEmail[$i]["product_price"], $len_product_price, " ", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "product_count", str_pad( $productsForEmail[$i]["product_count"], $len_product_count, " ", STR_PAD_LEFT ) );

        if ( $ShowExTaxColumn == true )
            $mailTemplate->set_var( "product_total_ex_tax", str_pad( $productsForEmail[$i]["product_total_ex_tax"], $len_product_total_ex_tax, " ", STR_PAD_LEFT ) );
        else
            $mailTemplate->set_var( "product_total_ex_tax", "" );

        if ( $ShowIncTaxColumn == true )
            $mailTemplate->set_var( "product_total_inc_tax", str_pad( $productsForEmail[$i]["product_total_inc_tax"], $len_product_total_inc_tax, " ", STR_PAD_LEFT ) );
        else
            $mailTemplate->set_var( "product_total_inc_tax", "" );

        if ( $ShowSavingsColumn == true )
            $mailTemplate->set_var( "product_savings", str_pad( $productsForEmail[$i]["product_savings"], $len_product_savings, " ", STR_PAD_LEFT ) );
        else
            $mailTemplate->set_var( "product_savings", "" );

        $productID = $productsForEmail[$i]["product_id"];

        $mailTemplate->set_var( "cart_item_option", "" );

        if( is_array( $productOptions[$productID] ) )
        {
            foreach( $productOptions[$productID] as $option )
            {
                $mailTemplate->set_var( "option_indent", str_pad( "", $len_option_indent, " ", STR_PAD_LEFT ) );
                $mailTemplate->set_var( "option_id", str_pad( $option["option_id"], $len_option_id, " ", STR_PAD_LEFT ) );
                $mailTemplate->set_var( "option_name", str_pad( $option["option_name"], $len_option_name, " ", STR_PAD_RIGHT ) );
                $mailTemplate->set_var( "option_value", str_pad( $option["option_value"], $len_option_name, " ", STR_PAD_RIGHT ) );
                $mailTemplate->set_var( "option_price", str_pad( $option["option_price"], $len_option_price, " ", STR_PAD_LEFT ) );

                $mailTemplate->parse( "cart_item_option", "cart_item_option_tpl", true );
            }
        }

        $mailTemplate->parse( "cart_item", "cart_item_tpl", true );
    }

    if ( $numberOfItems > 0 )
    {
        $ShowCart = true;
    }

    $mailTemplate->setAllStrings();

    if ( $ShowCart == true )
    {
        if ( $ShowSavingsColumn == true )
        {
            $totalsLen += $len_product_savings;
        }

        $mailTemplate->set_var( "intl-confirming-order", trim( $mailTemplateIni->read_var( "strings", "confirming-order" ) ) );
        $mailTemplate->set_var( "intl-thanks_for_shopping", trim( $mailTemplateIni->read_var( "strings", "thanks_for_shopping" ) ) );;
        $mailTemplate->set_var( "intl-goods_list", trim( $mailTemplateIni->read_var( "strings", "goods_list" ) ) );;

        $mailTemplate->set_var( "subtotal_inc_tax", str_pad( $subinctax, $len_product_total_inc_tax, " ", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "total_inc_tax", str_pad( $inctax, $len_product_total_inc_tax, " ", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "shipping_inc_tax", str_pad( $shipinctax, $len_product_total_inc_tax, " ", STR_PAD_LEFT ) );

        $mailTemplate->set_var( "subtotal_ex_tax", str_pad( $subextax, $len_product_total_ex_tax, " ", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "total_ex_tax", str_pad( $extax, $len_product_total_ex_tax, " ", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "shipping_ex_tax", str_pad( $shipextax, $len_product_total_ex_tax, " ", STR_PAD_LEFT ) );

        $mailTemplate->set_var( "intl-subtotal", str_pad( trim( $mailTemplateIni->read_var( "strings", "subtotal" ) ), $totalsLen , " ", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "intl-shipping", str_pad( trim( $mailTemplateIni->read_var( "strings", "shipping" ) ), $totalsLen , " ", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "intl-total", str_pad( trim( $mailTemplateIni->read_var( "strings", "total" ) ), $totalsLen , " ", STR_PAD_LEFT ) );

        $mailTemplate->parse( "cart_item_list", "cart_item_list_tpl" );
        $mailTemplate->parse( "full_cart", "full_cart_tpl" );

        $taxBasisLen = strlen( trim( $mailTemplateIni->read_var( "strings", "tax_basis" ) ) );
        $taxPercentageLen = strlen( trim( $mailTemplateIni->read_var( "strings", "tax_percentage" ) ) );
        $taxLen = strlen( trim( $mailTemplateIni->read_var( "strings", "tax" ) ) );

        $currency->setValue( $total["tax"] );    
        $taxValue = preg_replace( $search, $replace, $locale->format( $currency ) );
        $taxLen =  strlen( $taxValue ) > $taxLen ? strlen(  $taxValue ) : $taxLen;

        foreach( $tax as $taxGroup )
        {
            $currency->setValue( $taxGroup["basis"] );
            $subTaxBasis = trim( preg_replace( $search, $replace, $locale->format( $currency ) ) );
            $taxBasisLen = strlen( $subTaxBasis ) > $taxBasisLen ? strlen(  $subTaxBasis ) : $taxBasisLen;

            $currency->setValue( $taxGroup["tax"] );    
            $subTax = trim( preg_replace( $search, $replace, $locale->format( $currency ) ) );
            $taxLen = strlen( $subTax ) > $taxLen ? strlen(  $subTax ) : $taxLen;

            $taxPercentageLen = strlen( trim( $taxGroup["sub_tax_percentage"] ) ) > $taxPercentageLen ? strlen(  trim( $taxGroup["sub_tax_percentage"] ) ) : $taxPercentageLen;
        }

        $taxPercentageLen += $separateBy;
        $taxLen += $separateBy;
        $taxBasisLen;

        foreach( $tax as $taxGroup )
        {
            $currency->setValue( $taxGroup["basis"] );
            $subTaxBasis = trim( preg_replace( $search, $replace, $locale->format( $currency ) ) );
            $mailTemplate->set_var( "sub_tax_basis", str_pad( $subTaxBasis, $taxBasisLen, " ", STR_PAD_LEFT ) );

            $mailTemplate->set_var( "sub_tax_percentage", str_pad( $taxGroup["percentage"], $taxPercentageLen, " ", STR_PAD_LEFT ) );

            $currency->setValue( $taxGroup["tax"] );    
            $subTax = trim( preg_replace( $search, $replace, $locale->format( $currency ) ) );
            $mailTemplate->set_var( "sub_tax", str_pad( $subTax, $taxLen, " ", STR_PAD_LEFT ) );

            $mailTemplate->parse( "tax_item", "tax_item_tpl", true );
        }


        $mailTemplate->set_var( "intl-tax_basis", str_pad( trim( $mailTemplateIni->read_var( "strings", "tax_basis" ) ), $taxBasisLen, " ", STR_PAD_RIGHT ) );;
        $mailTemplate->set_var( "intl-tax_percentage", str_pad( trim( $mailTemplateIni->read_var( "strings", "tax_percentage" ) ), $taxPercentageLen, " ", STR_PAD_LEFT ) );;
        $mailTemplate->set_var( "intl-tax", str_pad( trim( $mailTemplateIni->read_var( "strings", "tax" ) ), $taxLen, " ", STR_PAD_LEFT ) );;

        $mailTemplate->set_var( "tax", str_pad( $taxValue, $taxBasisLen + $taxLen + $taxPercentageLen, " ", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "tax_hyphen_line", str_pad( "", $taxBasisLen + $taxLen + $taxPercentageLen, "-", STR_PAD_LEFT ) );
        $mailTemplate->set_var( "tax_equal_line", str_pad( "", $taxBasisLen + $taxLen + $taxPercentageLen, "=", STR_PAD_LEFT ) );

        $mailTemplate->parse( "tax_specification", "tax_specification_tpl" );
    }

        $mailBody = $mailTemplate->parse( "dummy", "full_cart_tpl" );
        $subjectINI = new INIFile( "eztrade/user/intl/" . $Language . "/mailorder.php.ini", false );

        $mailSubjectUser = $subjectINI->read_var( "strings", "mail_subject_user" ) . " " . $ini->read_var( "site", "SiteURL" );
        $mailSubject = $subjectINI->read_var( "strings", "mail_subject_admin" ) . " " . $ini->read_var( "site", "SiteURL" );

    $checkout = new eZCheckout();
    $instance =& $checkout->instance();
    $paymentMethod = $instance->paymentName( $order->paymentMethod() );

    $mailTemplate->set_var( "payment_method", $paymentMethod );

    $mailTemplate->set_var( "comment", $order->comment() );

    $shippingType = $order->shippingType();
    if ( $shippingType )
    {    
        $mailTemplate->set_var( "shipping_type", $shippingType->name() );
    }

    $shippingCost = $order->shippingCharge();

    $shippingVAT = $order->shippingVAT();

    $currency->setValue( $shippingCost );

    $mailTemplate->set_var( "shipping_cost", $locale->format( $currency ) );

    $sum += $shippingCost;
    $currency->setValue( $sum );
    $mailTemplate->set_var( "order_sum", $locale->format( $currency ) );

    $currency->setValue( $totalVAT + $shippingVAT );
    $mailTemplate->set_var( "order_vat_sum", $locale->format( $currency ) );

    $mailTemplate->set_var( "order_id", $order->id() );
    
    // Send E-mail    
    $mail = new eZMail();
    
    $mailBody = $mailTemplate->parse( "dummy", "order_sendt_tpl" );
    $mail->setFrom( $OrderSenderEmail );
    
    $mail->setTo( $user->email() );
    $mail->setSubject( $mailSubjectUser );
    $mail->setBody( $mailBody );
    $mail->send();
    
    // admin email
    // check to see if the email should be encrypted for the administrator
    $mailEncrypt = $ini->read_var( "eZTradeMain", "MailEncrypt" );
    
	if ( $mailEncrypt == "GPG" )
	{	
	    // initialize GPG class 
	    $wwwUser = $ini->read_var( "eZTradeMain", "ApacheUser" );
        $mailKeyname = $ini->read_var( "eZTradeMain", "RecipientGPGKey" );
        // At this point you can add any information to the template as needed
        // remember to provide a variable in the template for it.
     	// add credit card info for the administrator
    	$mailTemplate->set_var( "payment_method", $paymentMethod );
    	$mailTemplate->set_var( "cc_number", $CCNumber );
    	$mailTemplate->set_var( "cc_expiremonth", $ExpireMonth );
    	$mailTemplate->set_var( "cc_expireyear", $ExpireYear );

	    $mailBody = $mailTemplate->parse( "dummy", "order_sendt_tpl" );
    	// encrypt mailBody
		$mytext = new ezgpg( $mailBody, $mailKeyname, $wwwUser );
		$mailBody=($mytext->body);
		$mail->setBody( $mailBody );
	}
    
    $mail->setSubject( $mailSubject );
    $mail->setTo( $OrderReceiverEmail );
    $mail->setFrom( $user->email() );

    $mail->send();

    // get the cart or create it
    $cart = new eZCart();
    $cart = $cart->getBySession( $session );
    foreach ( $cart->items() as $item )
    {
        // set the wishlist item to bought if the cart item is
        // fetched from a wishlist
        $wishListItem = $item->wishListItem();
        if ( $wishListItem )
        {
            $wishListItem->setIsBought( true );
            $wishListItem->store();
        }
    }

    // Decrease product/option quantity
    $items =& $cart->items();
    foreach ( $items as $item )
    {
        $product =& $item->product();
        $count = $item->count();
        $quantity = $product->totalQuantity();
        $values =& $item->optionValues();
        $selected_values = array();
        foreach ( $values as $value )
            {
                $option_value =& $value->optionValue();
                $selected_values[] = $option_value->id();
            }

        $changed_quantity = false;
        if ( !(is_bool( $quantity ) and !$quantity) )
        {
            $max_value = max( $quantity - $count, 0 );
            $product->setTotalQuantity( $max_value );
            if ( $max_value == 0 and $DiscontinueQuantityless )
                $product->setDiscontinued( true );
            $product->store();
            $changed_quantity = true;
        }
        $options =& $product->options();
        $change_discontinuity = false;
        $max_max_value = 0;
        $has_value = false;
        foreach( $options as $option )
        {
            $option_values =& $option->values();
            foreach( $option_values as $option_value )
            {
                if ( in_array( $option_value->id(), $selected_values ) )
                {
                    $value_quantity = $option_value->totalQuantity();
                    if ( !(is_bool( $value_quantity ) and !$value_quantity) )
                    {
                        $max_value = max( $value_quantity - $count, 0 );
                        $max_max_value = max( $max_max_value, $max_value );
                        $option_value->setTotalQuantity( $max_value );
                        $option_value->store();
                        $changed_quantity = true;
                    }
                }
                $value_quantity = $option_value->totalQuantity();
                if ( (is_bool( $value_quantity ) and !$value_quantity) or $value_quantity > 0 )
                {
                    $has_value = true;
                }
            }
        }
        $productQuantity = $product->totalQuantity();
        if ( ( $max_max_value == 0 and !$has_value and $DiscontinueQuantityless ) and !(is_bool( $productQuantity ) and !$productQuantity ) )
        {
            $product->setDiscontinued( true );
            $product->store();
        }
        if ( $changed_quantity )
        {
            deleteCache( $product, false, false, false );
        }

        $user =& eZUser::currentUser();



        for( $i=0; $i < $count; $i++ )
        {
            // Create vouchers
            $voucherInfo =& $item->voucherInformation();
            if ( $item->voucherInformation() )
            {
                $voucher = new eZVoucher( );
                $voucher->generateKey();
                $voucher->setAvailable( true );
                $voucher->setUser( $user );
                $voucher->setPrice( $voucherInfo->price() );
                $voucher->setTotalValue( $voucherInfo->price() );
                $voucher->setProduct( $item->product() );
                $voucher->store();
                $voucherInfo->setVoucher( $voucher );
                $voucherInfo->store();
                $voucherInfo->sendMail();

            }
        }
    }
    
    //
    if ( is_file ( "checkout/user/postpayment.php" ) )
    {
        include( "checkout/user/postpayment.php" );
    }
    
    $cart->clear();

    $OrderID = $order->id();

    $preOrder = new eZPreOrder( $PreOrderID );
    $preOrder->setOrderID( $OrderID );
    $preOrder->store();

    $payedWith = $session->arrayValue( "PayedWith" );

    if ( is_array ( $payedWith ) )
    {
        while( list($voucherID,$price) = each( $payedWith ) )
        {
            $voucher = new eZVoucher( $voucherID );
            $voucher->setPrice( $voucher->price() - $price );
            if ( $voucher->price() <= 0 )
                $voucher->setAvailable( false );

            $voucher->store();
            
            $voucherUsed = new eZVoucherUsed();
            $voucherUsed->setVoucher( $voucher );
            $voucherUsed->setPrice( $price );
            $voucherUsed->setOrder( $order );
            $voucherUsed->setUser( $user );
            $voucherUsed->store();
            
        }
        $session->setVariable( "PayedWith", "" );
        $session->setVariable( "PayWithVoucher", "" );
    }

    $cart->delete();
    $session->setVariable( "OrderCompletedID", $OrderID );
    // call the payment script after the payment is successful.
    // some systems needs this, e.g. to print out the OrderID which was cleared..
    $Action = "PostPayment";
    include( $instance->paymentFile( $paymentMethod ) );

    // Turn of SSL and redirect to http://

    $session->setVariable( "SSLMode", "" );

    eZHTTPTool::header( "Location: http://$HTTP_HOST/trade/ordersendt/$OrderID/" );
    exit();
}
else if( $OrderID > 0 )
{
    $user =& eZUser::currentUser();
    $order = new eZOrder( $OrderID );
    $orderUser = $order->user();

    if( $user->id() == $orderUser->id() )
    {
        eZHTTPTool::header( "Location: http://$HTTP_HOST/trade/ordersendt/$OrderID/" );
        exit();
    }
}


?>
