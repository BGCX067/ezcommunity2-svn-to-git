<?php
// 
// $Id: filedownload.php 4436 2001-02-28 15:24:58Z ce $
//
// B�rd Farstad <bf@ez.no>
// Created on: <10-Dec-2000 16:39:10 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2001 eZ systems as
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

// clear what might be in the output buffer
ob_end_clean();

include_once( "ezimagecatalogue/classes/ezimage.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );
include_once( "ezuser/classes/ezuser.php" );

$file = new eZImage( $ImageID );
$fileName = $file->name();
$originalFileName = $file->originalFileName();
$filePath = $file->filePath( true );

$user = eZUser::currentUser();
$image = new eZImage( $ImageID );
if ( eZObjectPermission::hasPermission( $image->id(), "imagecatalogue_image", "r", $user ) == false )
{
    eZHTTPTool::header( "Location: /error/403/" );
    exit();
}

//  print( $filePath );

//  # the file may be a local file with full path. 
$fileSize = filesize( $filePath );
$fp = fopen( $filePath, "r" );
$content =& fread( $fp, $fileSize );

Header("Content-type: application/oct-stream"); 
Header("Content-length: $fileSize"); 
Header("Content-disposition: attachment; filename=\"$originalFileName\"");

echo($content);
exit();

?> 
