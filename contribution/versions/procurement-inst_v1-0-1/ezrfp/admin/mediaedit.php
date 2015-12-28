<?php
// 
// $Id: mediaedit.php,v 1.5.2.1 2001/11/01 18:13:26 ce Exp $
//
// Created on: <21-Sep-2000 10:32:36 bf>
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
include_once( "classes/ezlog.php" );

include_once( "classes/ezmediafile.php" );

include_once( "ezmediacatalogue/classes/ezmedia.php" );
include_once( "ezmediacatalogue/classes/ezmediatype.php" );
include_once( "ezuser/classes/ezauthor.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZRfpMain", "Language" );

include_once( "ezrfp/classes/ezrfpcategory.php" );
include_once( "ezrfp/classes/ezrfp.php" );

if ( isset ( $OK ) )
{
    $file = new eZMediaFile();

    $rfp = new eZRfp( $RfpID );
    if ( is_numeric( $MediaID ) )
        $media = new eZMedia( $MediaID );
    else
        $media = new eZMedia( );

    if ( trim( $NewCreatorName ) != "" &&
         trim( $NewCreatorEmail ) != ""
         )
    {
        $author = new eZAuthor( );
        $author->setName( $NewCreatorName );
        $author->setEmail( $NewCreatorEmail );
        $author->store();
        $media->setPhotographer( $author );
    }
    else
    {
        $media->setPhotographer( $PhotoID );
    }

    $media->setName( $Name );
    $media->setCaption( $Caption );
    $media->setDescription( $Description );
    if ( $file->getUploadedFile( "userfile" ) )
    { 
        $media->setMedia( $file );
    }
        
    $media->store();
    
    if ( $TypeID == -1 )
    {
        $media->removeType();
    }
    else
    {
        $media->removeType();
            
        $media->setType( new eZMediaType( $TypeID ) );
            
        $i = 0;
        if ( count( $AttributeValue ) > 0 )
        {
            foreach ( $AttributeValue as $attribute )
            {
                $att = new eZMediaAttribute( $AttributeID[$i] );
                    
                $att->setValue( $media, $attribute );
                        
                $i++;
            }
        }
    }
    
    if ( !is_numeric( $MediaID ) )
        $rfp->addMedia( $media );
        
    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /rfp/rfpedit/medialist/" . $RfpID . "/" );
    exit();
}

if ( isSet ( $DeleteSelected ) )
{
    $rfp = new eZRfp( $RfpID );

    if ( count ( $MediaArrayID ) != 0 )
    {
        foreach( $MediaArrayID as $MediaID )
        {
            $media = new eZMedia( $MediaID );
            $rfp->deleteMedia( $media );
        }
    }

    include_once( "classes/ezhttptool.php" );
    if ( !isset( $Update ) )
    {
        eZHTTPTool::header( "Location: /rfp/rfpedit/medialist/" . $RfpID . "/" );
        exit();
    }
}

$t = new eZTemplate( "ezrfp/admin/" . $ini->read_var( "eZRfpMain", "AdminTemplateDir" ),
                     "ezrfp/admin/intl/", $Language, "mediaedit.php" );

$t->setAllStrings();

$t->set_file( array(
    "media_edit_page" => "mediaedit.tpl",
    ) );


$t->set_block( "media_edit_page", "media_tpl", "media" );
$t->set_block( "media_edit_page", "type_tpl", "type" );
$t->set_block( "media_edit_page", "file_name_tpl", "file_name" );
$t->set_block( "media_edit_page", "no_file_name_tpl", "no_file_name" );
$t->set_block( "media_edit_page", "attribute_list_tpl", "attribute_list" );
$t->set_block( "media_edit_page", "photographer_item_tpl", "photographer_item" );
$t->set_block( "attribute_list_tpl", "attribute_tpl", "attribute" );

$t->set_var( "media_unit", "" );
$t->set_var( "no_file_name", "" );
$t->set_var( "file_name", "" );
$t->set_var( "media_size", "" );
$t->set_var( "media_id", "" );

//default values
if ( is_numeric( $MediaID ) )
{
    $rfp = new eZRfp( $RfpID );
    $media = new eZMedia( $MediaID );

    $t->set_var( "media_id", $media->id() );
    $t->set_var( "name_value", $media->name() );
    $t->set_var( "caption_value", $media->caption() );
    $t->set_var( "decription_value", $media->description() );
    $t->set_var( "action_value", "Update" );
    $mediaType = $media->type();

    $photographer = $media->photographer();
    $PhotographerID = $photographer->id();

    if ( $media->fileExists( true ) )
    {
        $mediaPath =& $media->filePath( true );
        $size = eZFile::filesize( $mediaPath );
    }
    else
    {
        $size = 0;
    }

    $size = eZFile::siFileSize( $size );

    $t->set_var( "media_size", $size["size-string"] );
    $t->set_var( "media_unit", $size["unit"] );
    if ( $media->originalFileName() )
    {
        $t->set_var( "no_file_name", "" );
        $t->set_var( "media_file", $media->originalFileName() );
        $t->parse( "file_name", "file_name_tpl" );
    }
    else
    {
        $t->set_var( "file_name", "" );
        $t->parse( "no_file_name", "no_file_name_tpl" );
    }
        
}
else
{
    $t->set_var( "name_value", "$Name" );
    $t->set_var( "caption_value", "$Caption" );
    $t->set_var( "description_value", "$Description" );
    $t->set_var( "action_value", "Insert" );
    $t->set_var( "option_id", "" );
    $t->set_var( "media", "" );
}


    $author = new eZAuthor();
    $authorArray = $author->getAll();
    foreach ( $authorArray as $author )
    {
        if ( $PhotographerID == $author->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
        $t->set_var( "photo_id", $author->id() );
        $t->set_var( "photo_name", $author->name() );
        $t->parse( "photographer_item", "photographer_item_tpl", true );
    }


// Print out all the types.
$type = new eZMediaType();
$types = $type->getAll();

if ( isset( $TypeID ) )
    $mediaType = new eZMediaType( $TypeID );

foreach ( $types as $typeItem )
{
    if ( get_class( $mediaType ) == "ezmediatype"  )
    {
        if ( $mediaType->id() == $typeItem->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }
    else
    {
        $t->set_var( "selected", "" );
    }
    
    $t->set_var( "type_id", $typeItem->id( ) );
    $t->set_var( "type_name", $typeItem->name( ) );
    
    $t->parse( "type", "type_tpl", true );
}

if ( get_class( $mediaType) == "ezmediatype" )    
{
    $attributes = $mediaType->attributes();

    foreach ( $attributes as $attribute )
    {
        $t->set_var( "attribute_id", $attribute->id( ) );
        $t->set_var( "attribute_name", $attribute->name( ) );

        if ( !$attribute->value( $media ) )
            $t->set_var( "attribute_value", $attribute->defaultValue() );
        else
            $t->set_var( "attribute_value", $attribute->value( $media ) );
        
        $t->parse( "attribute", "attribute_tpl", true );
    }
}

if ( count( $attributes ) > 0 || !isSet( $type ) )
{
    $t->parse( "attribute_list", "attribute_list_tpl" );
}
else
{
    $t->set_var( "attribute_list", "" );
}


$rfp = new eZRfp( $RfpID );
    
$t->set_var( "rfp_name", $rfp->name() );
$t->set_var( "rfp_id", $rfp->id() );

$t->pparse( "output", "media_edit_page" );

?>
