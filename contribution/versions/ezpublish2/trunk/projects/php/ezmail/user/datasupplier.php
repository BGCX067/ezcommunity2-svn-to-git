<?php
//
// $Id: datasupplier.php 9980 2002-04-17 20:48:37Z fh $
//
// Created on: <23-Oct-2000 17:53:46 bf>
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

include_once( "classes/ezhttptool.php" );
include_once( "ezmail/classes/ezmailaccount.php" );
include_once( "ezmail/classes/ezmailfolder.php" );
include_once( "ezuser/classes/ezuser.php" );
include_once( "ezmail/classes/imapfunctions.php" );

$ini =& INIFile::globalINI();
$GlobalSectionID = $ini->read_var( "eZMailMain", "DefaultSection" );

switch ( $url_array[2] )
{
    case "foldersort" : // change the sort mode of the folder list
    {
        $AccountType = $url_array[3];
        $FolderID = $url_array[4];
        $SortMethod = $url_array[5];
        $Offset = 0;
        include( "ezmail/user/maillist.php" );
    }
    break;

    case "folder" :
    {
        $AccountType = $url_array[3];
        $FolderID = urldecode($url_array[4]);
        $Offset = $url_array[5];
        if ( $Offset == "" )
            $Offset = 0;
//        if ( $FolderID == "" )
//            $FolderID = get INBOX.
        
        include( "ezmail/user/maillist.php" );
    }
    break;

    case "view" :
    {
        $AccountType = $url_array[3];
        $MailID = $url_array[4];
        $MailID = urldecode( $MailID );
        include( "ezmail/user/mailview.php" );
    }
    break;

    case "folderedit" :
    {
        $FolderID = urldecode( $url_array[3] );
        if ( $FolderID == "" )
            $FolderID = 0;
        include( "ezmail/user/folderedit.php" );
    }
    break;

    case "folderlist" :
    {
        include( "ezmail/user/folderlist.php" );
    }
    break;
    
    case "mailedit" :
    {
        $MailID = $url_array[3];
        if ( $MailID == "" )
            $MailID = 0;
        include( "ezmail/user/mailedit.php" );
    }
    break;

    case "fileedit" :
    {
        $MailID = $url_array[3];
        if ( $MailID == "" )
            $MailID = 0;
        include( "ezmail/user/fileedit.php" );
    }
    break;
    
    case "config" :
    {
        include( "ezmail/user/configure.php" );
    }
    break;

    case "accountedit" :
    {
        $AccountID = $url_array[3];
        if ( $AccountID == "" )
            $AccountID = 0;
        include( "ezmail/user/accountedit.php" );
    }
    break;

    case "check" : // check the mail for this user!
    {

        $user =& eZUser::currentUser();
        $accounts = eZMailAccount::getByUser( $user->id() );

        foreach ( $accounts as $account )
        {
            if ( $account->isActive() )
                $account->checkMail();
        }

        eZHTTPTool::header( "Location: /mail/folderlist/" );
        exit();
    }
    break;

    case "filteredit" :
    {
        $FilterID = $url_array[3];
        if ( $FilterID == "" )
            $FilterID = 0;
        include( "ezmail/user/filteredit.php" );
    }
    break;

    case "search" :
    {
        include( "ezmail/user/search.php" );
    }
    break;

    case "link" :
    {
        $MailID = $url_array[3];
        include( "ezmail/user/link.php" );
    }
    break;

    case "imapdownload" :
    {
        $AttachmentID = urldecode($url_array[3]);
        $FileName = $url_array[4];
        include( "ezmail/user/fetchimapattachment.php" );
    }
    break;
        
    case "test":
    {
        include( "ezmail/user/imap.php" );
    }
    break;

    default:
    {
        eZHTTPTool::header( "Location: /error/404/" );
        exit();
    }
    break;
}

?>
