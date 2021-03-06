<?

// 
// $Id: ezcertificatecategory.php 2602 2000-12-11 15:56:43Z ce $
//
// Definition of eZCertificateCategory class
//
// Paul K Egell-Johnsen <pkej@ez.no>
// Created on: <09-Nov-2000 14:52:40 pkej>
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

//!! eZCV
//! eZCertificateCategory handles the certificate categories.
/*!
*/

include_once( "classes/ezdb.php" );

class eZCertificateCategory
{
    /*!
      Constructor of the eZCertificateCategory.
    */
    function eZCertificateCategory( $id="-1", $fetch=true )
    {
        $this->IsConnected = false;
        
        if ( $id != -1 )
        {
            $this->ID = $id;
            if ( $fetch == true )
            {
                
                $this->get( $this->ID );
            }
            else
            {
                $this->State_ = "Dirty";
                
            }
        }
        else
        {
            $this->State_ = "New";
        }
    }
    
    /*!
      Stores the object in the database.
    */
    function store()
    {
        $this->dbInit();
        
        $ret = false;
        
        if ( !isSet( $this->ID ) )
        {
            $this->Database->query( "INSERT INTO eZCV_CertificateCategory set Name='$this->Name', Description='$this->Description',  Institution='$this->Institution', ParentID='$this->ParentID'" );

            $this->ID = mysql_insert_id();

            $this->State_ = "Coherent";
            $ret = true;
        }
        else
        {
            $this->Database->query( "UPDATE eZCV_CertificateCategory set Name='$this->Name', Description='$this->Description', Institution='$this->Institution', ParentID='$this->ParentID' WHERE ID='$this->ID'" );

            $this->State_ = "Coherent";
            $ret = true;
        }
        return $ret;
    }


    /*
      Delets the company type from the database.
     */
    function delete()
    {
        $this->dbInit();
        $this->Database->query( "DELETE FROM eZCV_CertificateCategory WHERE ID='$this->ID'" );
    }

    /*
        Fetches a company type with the  ID == $id
    */  
    function get( $id )
    {
        $this->dbInit();    
        if ( $id != "" )
        {
            $this->Database->array_query( $company_type_array, "SELECT * FROM eZCV_CertificateCategory WHERE ID='$id'" );
            
            if ( count( $company_type_array ) > 1 )
            {
                die( "Error: More than one company type with the same ID found. Major problem, clean up the table eZCV_CertificateCategory. " );
            }
            elseif( count( $company_type_array ) == 1 )
            {
                $this->ID = $company_type_array[ 0 ][ "ID" ];
                $this->Name = $company_type_array[ 0 ][ "Name" ];
                $this->Description = $company_type_array[ 0 ][ "Description" ];
                $this->ParentID = $company_type_array[ 0 ][ "ParentID" ];
                $this->Institution = $company_type_array[ 0 ][ "Institution" ];
           }
        }
    }
    
    /*!
        Fetches all the company types in the db and return them as an array of objects.
     */
    function getAll( $OrderBy = "ID", $LimitStart = "None", $LimitBy = "None" )
    {
        $this->dbInit();
        
        switch( strtolower( $OrderBy ) )
        {
            case "description":
            case "desc":
                $OrderBy = "ORDER BY Description";
                break;
            case "name":
                $OrderBy = "ORDER BY Name";
                break;
            case "parentid":
            case "pid":
                $OrderBy = "ORDER BY ParentID";
                break;
            case "id":
            case "typeid":
                $OrderBy = "ORDER BY ID";
                break;
            default:
                $OrderBy = "ORDER BY ID";
                break;
        }
        
        if( is_numeric( $LimitStart ) )
        {
            $LimitClause = "LIMIT $LimitStart";
            
            if( is_numeric( $LimitBy ) )
            {
                $LimitClause = $LimitClause . ", $LimitBy";
            }
        }
        else
        {
            $LimitClause = "";
        }
        
        $company_type_array = array();
        $return_array = array();

        
        $this->Database->array_query( $company_type_array, "SELECT ID FROM eZCV_CertificateCategory $OrderBy $LimitClause" );

        foreach( $company_type_array as $companyTypeItem )
        {
            $return_array[] = new eZCertificateCategory( $companyTypeItem["ID"] );
        }
        return $return_array;
    }
 
    /*!
        Fetches all the company types in the db and return them as an array of objects.
     */
    function getByParentID( $parent = 0, $OrderBy = "ID", $LimitStart = "None", $LimitBy = "None" )
    {
        $this->dbInit();

        if ( get_class( $parent ) == "ezcertificatecategory" )
        {
            $id = $parent->id();
        }
        else
        {
            $id = $parent;
        }
        
        switch( strtolower( $OrderBy ) )
        {
            case "description":
            case "desc":
                $OrderBy = "ORDER BY Description";
                break;
            case "name":
                $OrderBy = "ORDER BY Name";
                break;
            case "parentid":
            case "pid":
                $OrderBy = "ORDER BY ParentID";
                break;
            case "id":
            case "typeid":
                $OrderBy = "ORDER BY ID";
                break;
            default:
                $OrderBy = "ORDER BY ID";
                break;
        }
        
        if( is_numeric( $LimitStart ) )
        {
            $LimitClause = "LIMIT $LimitStart";
            
            if( is_numeric( $LimitBy ) )
            {
                $LimitClause = $LimitClause . ", $LimitBy";
            }
        }
        else
        {
            $LimitClause = "";
        }
        
        $company_type_array = array();
        $return_array = array();
        
        $this->Database->array_query( $company_type_array, "SELECT ID FROM eZCV_CertificateCategory WHERE ParentID='$id' $OrderBy $LimitClause" );

        foreach( $company_type_array as $companyTypeItem )
        {
            $return_array[] = new eZCertificateCategory( $companyTypeItem["ID"] );
        }
        return $return_array;
    }

    /*!
        Check if this item has children
     */
    function hasChildren( &$childrenCount, $id = "this" )
    {
        $ret = false;
        
        if( $id == "this" )
        {
            $id = $this->ID;
        }
        
        if( is_numeric( $id ) )
        {
            $this->dbInit();
            
            $company_type_array = array();
            $this->Database->array_query( $company_type_array, "SELECT ParentID FROM eZCV_CertificateCategory WHERE ParentID='$id'" );
            $childrenCount = count( $company_type_array );
            
            if( $childrenCount != 0 )
            {
                $ret = true;
            }
        }
        
        return $ret;
    }

    /*!
      Print out the group path.
    */
    function path( $categoryID = 0 )
    {
        $this->dbInit();
        
        if( $categoryID == 0 )
        {
            $categoryID = $this->ID;
        }
        
        $category = new eZCertificateCategory( $categoryID );
        
        $path = array();
        
        $parent = $category->parentID();
        
        if ( $parent != 0 )
        {
            $path = array_merge( $path, $this->path( $parent ) );
        }
        else
        {
//              array_push( $path, $category->name() );
        }

        if ( $categoryID != 0 )
            array_push( $path, array( $category->id(), $category->name() ) );
        
        return $path;
    }
    
    /*!
     Fetches all the company types in the db and return them as an array of objects.
    */
    function getTree( $parentID=0, $level=0 )
    {                   
        $category = new eZCertificateCategory( $parentID );

        $categoryList = $category->getByParentID( $category );
        
        $tree = array();
        $level++;
        foreach ( $categoryList as $category )
        {
            array_push( $tree, array( $returnObj = new eZCertificateCategory( $category->id() ), $level ) );

            if ( $category != 0 )
            {
                $tree = array_merge( $tree, $this->getTree( $category->id(), $level ) );
            }
        }

        return $tree;
    }


    /*!
      Set the name.
    */
    function setName( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Name = $value;
    }
    /*!
      Set the description.
    */
    function setDescription( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Description = $value;
    }

    /*!
      Set the Institution.
    */
    function setInstitution( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Institution = $value;
    }

    /*!
      Set parent
    */
    function setParentID( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->ParentID = $value;
    }

  
    /*!
      Returns the ID of the company type.
    */
    function id()
    {
        return $this->ID;
    }
  
    /*!
      Returns the name.
    */
    function name( )
    {
        return $this->Name;
    }
  
    /*!
      Returns the description.
    */
    function description( )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Description;
    }
    
    /*!
      Returns the parent.
    */
    function parentID( )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->ParentID;
    }
    
    
    /*!
      Returns the Institution.
    */
    function institution( )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Institution;
    }
    
    
    
    /*!
      \private
      Open the database.
    */
    function dbInit()
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = new eZDB( "site.ini", "site" );
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $ParentID;
    var $Name;
    var $Description;
    var $Institution;

    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;

}

?>
