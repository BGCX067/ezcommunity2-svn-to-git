<?php
// 
// $Id: userlogin.php 7961 2001-09-24 11:53:43Z jhe $
//
// Created on: <14-Oct-2000 15:41:17 bf>
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
include_once( "classes/ezhttptool.php" );

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZForumMain", "Language" );

include_once( "ezuser/classes/ezuser.php" );

if ( eZUser::currentUser() )
{
    if ( isset( $RedirectURL ) )
    {
        $AdditionalURLInfo="?RedirectURL=$RedirectURL";
    }

    if ( $Action == "newsimple" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/new/$ForumID/$AdditionalURLInfo" );
    }

    if ( $Action == "replysimple" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/reply/$ReplyToID/$ForumID/$AdditionalURLInfo" );
    }
    
    if ( $Action == "new" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/new/$ForumID/$AdditionalURLInfo" );
    }

    if ( $Action == "edit" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/edit/$MessageID/$AdditionalURLInfo" );
    }

    if ( $Action == "delete" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/delete/$MessageID/$AdditionalURLInfo" );
    }

    if ( $Action == "reply" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/reply/$ReplyToID/$AdditionalURLInfo" );
    }    
}
else
{
    $Anonymous == false;
    
    
    switch ( $Action )
    {
        case "new":
        {
            include_once( "ezforum/classes/ezforum.php" );
            include_once( "ezforum/classes/ezforummessage.php" );

            $CheckForumID = $ForumID;
           
            include( "ezforum/user/messagepermissions.php" );
            
            if ( $ForumPost == true )
            {
                eZHTTPTool::header( "Location: /forum/messageedit/new/$ForumID/$AdditionalURLInfo" );
            }
        }
        
        case "reply":
        {
            include_once( "ezforum/classes/ezforum.php" );
            include_once( "ezforum/classes/ezforummessage.php" );
            
            $msg = new eZForumMessage( $ReplyToID );
            
            $CheckForumID = $msg->forumID();

            include( "ezforum/user/messagepermissions.php" );
            
            if ( $ForumPost == true )
            {
                eZHTTPTool::header( "Location: /forum/messageedit/reply/$ReplyToID/$AdditionalURLInfo" );
            }
        }
    }
    
    if ( $Anonymous == false )
    {
        $t = new eZTemplate( "ezforum/user/" . $ini->read_var( "eZForumMain", "TemplateDir" ),
                             "ezforum/user/intl/", $Language, "userlogin.php" );

        $t->setAllStrings();

        $t->set_file( "user_login_tpl", "userlogin.tpl" );

        if ( $Action == "newsimple" )
        {
            $t->set_var( "redirect_url", $RedirectURL );
        }

        if ( $Action == "replysimple" )
        {
            $t->set_var( "redirect_url", $RedirectURL );
        }

        if ( $Action == "new" )
        {
            $t->set_var( "redirect_url", "/forum/messageedit/new/$ForumID/" );
        }

        if ( $Action == "edit" )
        {
            $t->set_var( "redirect_url", "/forum/messageedit/edit/$MessageID/" );
        }

        if ( $Action == "delete" )
        {
            $t->set_var( "redirect_url", "/forum/messageedit/delete/$MessageID/" );
        }

        if ( $Action == "reply" )
        {
            $t->set_var( "redirect_url", "/forum/messageedit/reply/$ReplyToID/" );
        }

        $t->pparse( "output", "user_login_tpl" );
    }
}

?>
