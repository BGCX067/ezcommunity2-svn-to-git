<?php
// 
// $Id: fileview.php 7946 2001-09-22 11:32:13Z master $
//
// Created on: <04-Jan-2001 16:47:23 ce>
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
include_once( "classes/ezhttptool.php" );

include_once( "ezfilemanager/classes/ezvirtualfile.php" );
include_once( "ezfilemanager/classes/ezvirtualfolder.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezuser/classes/ezpermission.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );


$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZFileManagerMain", "Language" );


$t = new eZTemplate( "ezfilemanager/user/" . $ini->read_var( "eZFileManagerMain", "TemplateDir" ),
                     "ezfilemanager/user/intl/", $Language, "fileview.php" );

$t->set_file( "file_view", "fileview.tpl" );

$t->setAllStrings();

// $t->set_block( "file_view", "value_tpl", "value" );
$t->set_block( "file_view", "view_tpl", "view" );
$t->set_block( "file_view", "delete_tpl", "delete" );
$t->set_block( "file_view", "edit_tpl", "edit" );
$t->set_block( "file_view", "download_tpl", "download" );

$t->set_var( "delete", "" );
$t->set_var( "edit", "" );
$t->set_var( "download", "" );

if ( $FileID != 0 )
{
    $file = new eZVirtualFile( $FileID );

    if ( $file->id() == 0 )
    {
        eZHTTPTool::header( "Location: /error/404/" );
        exit();
    }
    
    $t->set_var( "file_name", $file->name() );
    $t->set_var( "file_id", $file->id() );
    $t->set_var( "file_description", $file->description() );

    $user =& eZUser::currentUser();

    // sections
    include_once( "ezsitemanager/classes/ezsection.php" );

    $parent_folder = $file->folder();

    // tempo fix for admin users - maybe in the future must be changed
    if ( $parent_folder != 0 && ! eZPermission::checkPermission( $user, "eZUser", "AdminLogin" ) )
    {
	$GlobalSectionID = eZVirtualFolder::sectionIDstatic ( $parent_folder->id() );
    }
    
    // init the section
    $sectionObject =& eZSection::globalSectionObject( $GlobalSectionID );
    $sectionObject->setOverrideVariables();

    if ( eZObjectPermission::hasPermission( $file->id(), "filemanager_file", "r", $user ) )
    {
        $t->parse( "download", "download_tpl" );
    }
    else
    {
        eZHTTPTool::header( "Location: /error/403/" );
        exit();
    }

    if ( eZObjectPermission::hasPermission( $file->id(), "filemanager_file", "w", $user ) )
    {
        $t->parse( "delete", "delete_tpl" );
        $t->parse( "edit", "edit_tpl" );
    }

    $filePath =& $file->filePath( true );

    $size = $file->siFileSize();
    $t->set_var( "file_size", $size["size-string"] );
    $t->set_var( "file_unit", $size["unit"] );

    $fileOwner = $file->user();

    if ( $fileOwner )
        $t->set_var( "file_owner", $fileOwner->firstName() . " " . $fileOwner->lastName() );

    $t->parse( "view", "view_tpl" );
}


$folder = new eZVirtualFolder( $FolderID );

$folderList =& $folder->getByParent( $folder );

foreach ( $folderList as $folder )
{
    $t->set_var( "option_name", $folder->name() );
    $t->set_var( "option_value", $folder->id() );

    $t->set_var( "selected", "" );
    $t->set_var( "option_level", "" );

//    $t->parse( "value", "value_tpl", true );
}

$t->pparse( "output", "file_view" );


?>

