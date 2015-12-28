<?php
// 
// $Id: eznewsadmin.php 1341 2000-10-14 05:22:50Z pkej-cvs $
//
// Definition of eZNewsAdmin class
//
// Paul K Egell-Johnsen <pkej@ez.no>
// Created on: <20-Sep-2000 13:03:00 pkej>
//
// Copyright (C) 1999-2000 eZ Systems.  All rights reserved.
//
// IMPORTANT NOTE: You may NOT copy this file or any part of it into
// your own programs or libraries.
//
//!! eZNews
//! eZNewsAdmin handles the kind of info the admin user sees.
/*!
 */

include_once( "classes/ezurl.php" );
include_once( "eznews/classes/eznewsitem.php" );  
include_once( "eznews/classes/eznewsitemtype.php" );  
include_once( "eznews/classes/eznewsoutput.php" );  

/*!TODO
    Implement the doDefault function

    Implement the doItem (call to viewer class)
    
    Write examples.
 */
class eZNewsAdmin
{
    /*!
        Just initalizing some variables.
        
        \in
            \$inURLObject An eZURL object.
            \$inNewsConfigFileName The name of the file with the global config options.
     */
    function eZNewsAdmin( $inConfigFileName )
    {
        #echo "eZNewsAdmin::eZNewsAdmin( \$inConfigFileName = $inConfigFileName )<br />\n";

        $this->URLObject = new eZURL();
        $this->IniObject = new eZNewsOutput( $inConfigFileName );
        
        $this->ConfigFileName = $inConfigFileName;
    }
    
    
    
    /*!
        This function will try to find out what the user is trying to access.
        It will pass control to the function which best determines what item/
        items to show.
        
        \return
            Returns true if successful.
     */
    function doActions()
    {
        #echo "eZNewsItemViewer::doActions()<br />\n";
        $value = false;
        
        $count = $this->URLObject->getURLCount();

        $urlLength = $this->IniObject->GlobalIni->read_var( "eZNewsMain", "URLLength" );

        $count = $count - $urlLength;

        // We don't have any parts of the url which isn't defined as something special.
        // Lets do default action.
        if( $count == 0 )
        {
            $value = $this->doDefault();
        }
        else
        {
            // Lets see what we have.
            
            $urlPathOne = $this->URLObject->getURLPart( 0 + $urlLength );
            $urlPathLast = $this->URLObject->getURLPart( $count );
            
            switch( $urlPathOne )
            {
                case "itemtype":
                case "changetype":
                    break;
                case "id":
                case "article":
                    break;
                case "date":
                    break;
                case "author":
                    break;
                case "category":
                case "path":
                case "definition":
                    break;
                default:
                    // If it''s only one element in the path then we just
                    // try to make an item from that.
                    // Else we try with the last element.
                    if( $count == 1 )
                    {
                        $value = $this->doItem( $urlPathOne );
                    }
                    else
                    {
                        $value = $this->doItem( $urlPathLast );
                    }
                    break;
                
            }
        }
        
        if( $value == false )
        {
            $value = $this->doDefault();
        }
        
        return $value;
    }



    /*!
        This function will do a default action based on the initalization value found
        in the eZNewsMain category and the key URLDefault.
        
        \return
            Returns true if successful.
     */
    function doDefault()
    {
        #echo "eZNewsAdmin::doDefault()<br />\n";
        $value = false;

        echo $this->IniObject->GlobalIni->read_var( "eZNewsMain", "URLDefault" );
        
        $item = new eZNewsItem();
        $item->getAllByType( $returnArray, $maxCount, "flowerarticle", "publish" );
        
        foreach( $returnArray as $child )
        {
            echo "child " . $child->name() . "<br>";
        }
        
        $value = true;

        return $value;
    }



    /*!
        This function will try to show us an item based on the incoming data.
        If it finds an item and corresponding viewer it will use the viewer
        to show the info about the item.
        
        \in
            \$inItemData    An item's name or id.
        
        \return
            Returns true if successful.
     */
    function doItem( $inItemData = 0 )
    {
        #echo "eZNewsAdmin::doItem( \$inItemData = $inItemData )<br />\n";
        $value = false;
        
        $tempItem = new eZNewsItem( $inItemData );
        // If the temp item was found, ie. we have something to show.
        if( $tempItem->isCoherent() == true )
        {
            $changeType = new eZNewsChangeType( "publish" );

            // If the temp item we found is published.
            if( $changeType->id() == $tempItem->status() )
            {
                $itemType = new eZNewsItemType( $tempItem->itemTypeID() );

                $class = $itemType->eZClass() . "Viewer";
                $classPath = "eznews/admin/" . $class . ".php";
                $classPath = strtolower( $classPath );

                include_once( $classPath );

                $viewer = new $class( $tempItem, $this->IniObject, $this->URLObject );

                $value = $viewer->renderPage( $outPage );
                echo $outPage;
            }
            
            $changeType = new eZNewsChangeType( "temporary" );
            
            if( $changeType->id() == $tempItem->status() )
            {
                $itemType = new eZNewsItemType( $tempItem->itemTypeID() );

                $class = $itemType->eZClass() . "Viewer";
                $classPath = "eznews/admin/" . $class . ".php";
                $classPath = strtolower( $classPath );

                include_once( $classPath );

                $viewer = new $class( $tempItem, $this->IniObject, $this->URLObject );

                $value = $viewer->renderPage( $outPage );
                echo $outPage;
            }
            
            
        }
        
        return $value;
    }



    // Private members
    
    /// The global initalization file, usually "site.ini"
    var $IniObject;

    /// The object which decodes the url and url query.
    var $URLObject;
    
    /// The name of the global configuration file for eznews (ie. "site.ini")
    var $ConfigFileName;
};

?>