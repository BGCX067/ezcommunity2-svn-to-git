<?php
// 
// $Id: ezrssimporter.php 9819 2002-02-26 17:29:46Z br $
//
// Definition of ezrdfimporter class
//
// Created on: <13-Nov-2000 17:07:56 bf>
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

//!! eZNewsFeed
//! eZRDFImporter handles importing of news bullets from rdf driver sites like freshmeat.net.
/*!
  Example code:
  \sa eZNews eZNewsCategory eZNewsImporter
*/

/*!TODO

*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezdatetime.php" );
include_once( "eznewsfeed/classes/eznews.php" );

class eZRSSImporter
{
    /*!
      Constructor.
    */
    function eZRSSImporter( $site, $login="", $password="" )
    {
        $this->Site = $site;
        $this->Login = $login;
        $this->Password = $password;
    }

    /*!
      Returns the news items as an array.
    */
    function &news( )
    {
        $db =& eZDB::globalDatabase();
        $return_array = array();
        $fp = eZFile::fopen( $this->Site, "r" );
        $output = "";
        while ( !feof ( $fp ) )
        {
            $output .= fgets( $fp, 4096 );
        }
        fclose( $fp );
        
        $doc = xmltree( $output );
        if ( count( $doc->children ) > 0 )
        foreach ( $doc->children as $child )
        {
            if ( $child->name == "rss" )
            {
                if ( count( $child->children ) > 0 )
                foreach ( $child->children as $channel )
                {
                    if ( $channel->name == "channel" )
                    {
                        foreach ( $channel->children as $item )
                        {
                            if ( $item->name == "item" )
                            {
                                $title = "";
                                $link = "";
                                $description = "";
                                
                                foreach ( $item->children as $value )
                                {
                                    $contentValue = $value->content;

                                    switch ( $value->name )
                                    {
                                        case "title" :
                                        {
                                            $title = $contentValue;
                                        }
                                        break;

                                        case "link" :
                                        {
                                            $link = $contentValue;
                                        }
                                        break;

                                        case "description" :
                                        {
                                            $description = $contentValue;
                                        }
                                        break;                                        
                                    }
                                }

                                $news = new eZNews();
                                $news->setName( $title );
                                $news->setIntro( $description );
                                $news->setURL( $link );

                                $dateTime = new eZDateTime();
                                $news->setOriginalPublishingDate( $dateTime );
                                
                                $return_array[] = $news;
                            }
                        }
                    }
                }
            }
        }
        
        return $return_array;
    }

    var $Site;
    var $Login;
    var $Password;    
}

?>
