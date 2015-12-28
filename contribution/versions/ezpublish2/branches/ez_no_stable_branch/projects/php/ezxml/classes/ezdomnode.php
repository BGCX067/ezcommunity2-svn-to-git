<?php
//
// $Id: ezdomnode.php 9027 2001-11-16 14:42:30Z bf $
//
// Definition of eZDOMNode class
//
// B�rd Farstad <bf@ez.no>
// Created on: <16-Nov-2001 12:11:43 bf>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2000 eZ systems as
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

//!! eZXML
//! eZDOMNode handles DOM nodes in DOM documents
/*!

*/

class eZDOMNode
{
    /*!
    */
    function eZDOMNode( )
    {

    }

    /// Name of the node
    var $name;

    /// Type of the DOM node
    var $type;

    /// Content of the node
    var $content;

    /// Subnodes
    var $children;

    /// Attributes
    var $attributes;
}

?>
