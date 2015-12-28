<?php
//
// $Id: datasupplier.php 8828 2001-11-06 12:33:54Z jhe $
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


include_once( "ezuser/classes/ezpermission.php" );
include_once( "classes/ezhttptool.php" );

$user =& eZUser::currentUser();
if ( !eZPermission::checkPermission( $user, "eZBug", "ModuleEdit" ) )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

switch ( $url_array[2] )
{
    case "archive" :        
    {
        $ModuleID = $url_array[3];
        $Action = "";
        include( "ezbug/admin/buglist.php" );
    }
    break;

    case "search" :        
    {
        include( "ezbug/admin/search.php" );
    }
    break;
    
    case "bugpreview" :
    case "view" :        
    {
        $BugID = $url_array[3];
        
        include( "ezbug/user/bugview.php" );
    }
    break;
    
    case "unhandled" :
    {
        include( "ezbug/admin/unhandledbugs.php" );
    }
    break;

    case "priority" :
    {
        switch ( $url_array[3] )
        {
            case "list":
            {
                include( "ezbug/admin/prioritylist.php" );
            }
            break;
        }
    }
    break;

    case "category" :
    {
        switch ( $url_array[3] )
        {
            case "list":
            {
                include( "ezbug/admin/categorylist.php" );
            }
            break;
        }
    }
    break;

    case "module" :
    {
        switch ( $url_array[3] )
        {
            case "list":
            {
                if ( isSet( $AddModule ) )  // new
                {
                    $Action = "new";
                    $ParentID = $url_array[4];
                    include( "ezbug/admin/moduleedit.php" );
                }
                else 
                {
                    $ParentID = $url_array[4];
                    include( "ezbug/admin/modulelist.php" );
                }
            }
            break;

            case "insert":
            {
                $Action = "insert";
                include( "ezbug/admin/moduleedit.php" );
            }
            break;

            case "edit":
            {
                $Action = "edit";
                $ModuleID = $url_array[4];
                include( "ezbug/admin/moduleedit.php" );
            }
            break;

            case "update":
            {
                $Action = "update";
                $ModuleID = $url_array[4];
                include( "ezbug/admin/moduleedit.php" );
            }
            break;

            case "delete":
            {
                $Action = "delete";
                $ModuleID = $url_array[4];
                include( "ezbug/admin/moduleedit.php" );
            }
            break;

        }
    }
    break;

    case "status":
    {
        switch ( $url_array[3] )
        {
            case "list":
            {
                $ParentID = $url_array[4];
                include( "ezbug/admin/statuslist.php" );
            }
            break;
        }
    }
    break;
    
    case "edit":
    {
        if ( $url_array[3] == "new" )
        {
            $Action = "New";
        }
        else if ( $url_array[3] == "edit" )
        {
            $Action = "Edit";
            $BugID = $url_array[4];
        }
        else if ( $url_array[3] == "fileedit" )
        {
            switch ( $url_array[4] )
            {
                case  "new":
                {
                    $Action = "New";
                    $BugID = $url_array[5];
                    include( "ezbug/admin/fileedit.php" );
                }
                break;
                case  "edit":
                {
                    $Action = "Edit";
                    $BugID = $url_array[6];
                    $FileID = $url_array[5];
                    include( "ezbug/admin/fileedit.php" );
                }
                break;
                case "delete":
                {
                    $Action = "Delete";
                    $BugID = $url_array[6];
                    $FileID = $url_array[5];
                    include( "ezbug/admin/fileedit.php" );
                }
                break;
                default:
                {
                    include( "ezbug/admin/fileedit.php" );
                }
                break;
            }
        }
        else if ( $url_array[3] == "imageedit" )
        {
            switch ( $url_array[4] )
            {
                case "new":
                {
                    $Action = "New";
                    $BugID = $url_array[5];
                    include( "ezbug/admin/imageedit.php" );
                }
                break;
                case "edit":
                {
                    $Action = "Edit";
                    $BugID = $url_array[6];
                    $ImageID = $url_array[5];
                    include( "ezbug/admin/imageedit.php" );
                }
                break;
                case "delete":
                {
                    $Action = "Delete";
                    $BugID = $url_array[6];
                    $ImageID = $url_array[5];
                    include( "ezbug/admin/imageedit.php" );
                }
                break;
                default :
                {
                    include( "ezbug/admin/imageedit.php" );
                }
                break;
            }
        }
        include( "ezbug/admin/bugedit.php" );
    }
    break;

    case "report":
    {
        switch ( $url_array[3] )
        {
            case "fileedit":
            {
                if ( $url_array[4] == "new")
                {
                    $Action = "New";
                    $BugID = $url_array[5];
                    include( "ezbug/user/fileedit.php" );
                }
                else if ( $url_array[4] == "edit" )
                {
                    $Action = "Edit";
                    $BugID = $url_array[6];
                    $FileID = $url_array[5];
                    include( "ezbug/user/fileedit.php" );
                }
                else if ( $url_array[4] == "delete" )
                {
                    $Action = "Delete";
                    $BugID = $url_array[6];
                    $FileID = $url_array[5];
                    include( "ezbug/user/fileedit.php" );
                }
                else
                {
                    include( "ezbug/user/fileedit.php" );
                }
            }
            break;
            
            case "imageedit" :
            {
                if ( $url_array[4] == "new")
                {
                    $Action = "New";
                    $BugID = $url_array[5];
                    include( "ezbug/user/imageedit.php" );
                }
                else if ( $url_array[4] == "edit" )
                {
                    $Action = "Edit";
                    $BugID = $url_array[6];
                    $ImageID = $url_array[5];
                    include( "ezbug/user/imageedit.php" );
                }
                else if ( $url_array[4] == "delete" )
                {
                    $Action = "Delete";
                    $BugID = $url_array[6];
                    $ImageID = $url_array[5];
                    include( "ezbug/user/imageedit.php" );
                }
                else
                {
                    include( "ezbug/user/imageedit.php" );
                }
            }
            break;

            case "edit":
            {
                $BugID = $url_array[4];
                $Action = "Edit";
                include( "ezbug/admin/bugedit.php" );
            }
            break;
            
            default:
            {
                print( "Error: Bug file not found" );
            }
            break;
        }
    }
    break;

    case "support":
    {
        switch ( $url_array[3] )
        {
            case "list":
            {
                $Offset = $url_array[4] ? $url_array[4] : 0;
                include( "ezbug/admin/supportlist.php" );
            }
            break;

            case "edit":
            {
                $Action = $url_array[4];
                $id = $url_array[5];
                include( "ezbug/admin/supportedit.php" );
            }
            break;

            case "delete":
            {
                include( "ezbug/admin/supportdelete.php" );
            }
            break;

            case "category":
            {
                $Action = $url_array[4];
                switch ( $Action )
                {
                    case "list":
                    {
                        $Offset = $url_array[5] ? $url_array[5] : 0;
                        include( "ezbug/admin/supportcategorylist.php" );
                    }
                    break;

                    case "edit":
                    case "update":
                    {
                        $id = $url_array[5];
                        include( "ezbug/admin/supportcategoryedit.php" );
                    }
                    break;
                    
                    case "new":
                    case "insert":
                    {
                        include( "ezbug/admin/supportcategoryedit.php" );
                    }
                    break;

                    case "delete":
                    {
                        include( "ezbug/admin/supportcategorydelete.php" );
                    }
                    break;
                }
            }
            break;
        }
    }
    break;

    case "cron":
    {
        include( "ezbug/admin/cron.php" );
    }
    break;
}
?>
