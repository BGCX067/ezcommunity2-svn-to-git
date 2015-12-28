<?php
// 
// $Id: ezsimplerenderer.php,v 1.4 2001/07/19 12:19:21 jakobn Exp $
//
// Definition of eZSimpleRenderer class
//
// Created on: <18-Oct-2000 17:45:32 bf>
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
//!! eZRfp
//! eZSimpleRenderer renders XML contents into html rfps.
/*!
  This class wil decode the simple rfps generated by eZSimpleGenerator.

  \sa eZSimpleGenerator  
*/

include_once( "classes/eztexttool.php" );

class eZSimpleRenderer
{
    /*!
      Creates a new eZSimpleGenerator object.
    */
    function eZSimpleRenderer( &$rfp )
    {
        $this->Rfp = $rfp;
    }

    /*!
      Returns the XHTML contents of the introduction of the rfp.
    */
    function &renderIntro()
    {
        $xml = xmltree( $this->Rfp->contents() );

        if ( !$xml )
        {
            print( "<br /><b>Error: eZSimpleRenderer::docodeXML() could not decode XML</b><br />" );
        }
        else
        {
            $into = "";
            $body = "";
            
            $i=0;
            foreach ( $xml->root->children as $child )
            {
                if ( $child->name == "intro" )
                {
                    $intro = $child->children[0]->content;
                }
            }

            $newRfp = eZTextTool::nl2br( $intro );
        }
        
        return $newRfp;
    }

    /*!
      Returns the XHTML rfp of the rfp.
    */
    function &renderPage( $page=0 )
    {
        $xml = xmltree( $this->Rfp->contents() );

        if ( !$xml )
        {
            print( "<br /><b>Error: eZSimpleRenderer::docodeXML() could not decode XML</b><br />" );
        }
        else
        {
            $into = "";
            $body = "";
            
            $i=0;
            foreach ( $xml->root->children as $child )
            {
                if ( $child->name == "intro" )
                {
                    $intro = $child->children[0]->content;
                }
                
                if ( $child->name == "body" )
                {
                    $body = $child->children[0]->content;                    
                }
            }

            $newRfp = eZTextTool::nl2br( $intro ) . "</p><p>". eZTextTool::nl2br( $body );
        }
        
        return $newRfp;
    }
    
    var $Rfp;
}

?>
