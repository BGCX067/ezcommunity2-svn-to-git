<?php
// 
// $Id: ezcountry.php,v 1.10 2001/08/01 15:15:47 ce Exp $
//
// Definition of eZCountry class
//
// Created on: <31-Oct-2000 11:49:30 bf>
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

//!! eZAddress
//! eZRegion handles countries.
/*!
  
*/

include_once( "classes/ezdb.php" );
include_once( "classes/ezquery.php" );

class eZRegion
{
    /*!
      Constructs a new eZRegion object.
    */
    function eZRegion( $id = "" )
    {
        if ( is_array( $id ) )
        {
            $this->fill( $id );
        }
        else if ( $id != "" )
        {
            $this->ID = $id;
            $this->get( $this->ID );
        }
    }

    /*!
      Stores a region to the database.
    */  
    function store()
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        $name = $db->escapeString( $this->Name );
        if ( !isset( $this->ID ) )
        {
            $db->lock( "eZAddress_Region" );
            $this->ID = $db->nextID( "eZAddress_Region", "ID" );

            $res[] = $db->query( "INSERT INTO eZAddress_Region
                    ( ID, CountryID, Abbreviation, Name, UserAdded , Removed)
                    VALUES ( '$this->ID', '$this->CountryID', '$this->Abbreviation', '$name', '$this->UserAddedHasVAT', '$this->Removed' )" );
            $db->unlock();
        }
        else
        {

            $res[] = $db->query( "UPDATE eZAddress_Region
                    SET CountryID='$this->CountryID',
			Abbreviation='$this->Abbreviation',
			Name='$this->Name',
			UserAdded=$this->UserAdded',
			Removed='$this->Removed'
                    WHERE ID='$this->ID'" );            

        }        
        eZDB::finish( $res, $db );
        return $dbError;
    }
  
    /*!
      Fetches a region with object id==$id;
    */  
    function get( $id="" )
    {
        $db =& eZDB::globalDatabase();
        if ( $id != "" )
        {
            $db->array_query( $region_array, "SELECT * FROM eZAddress_Region WHERE ID='$id'" );
            if ( count( $region_array ) == 1 )
            {
                $this->fill( $region_array[0] );
            }
        }
    }

    /*!
      Extracts the information from the array and puts it in the object.
    */
    function fill( &$region_array )
    {
        $db =& eZDB::globalDatabase();
        
        $this->ID =& $region_array[ $db->fieldName( "ID" ) ];
        $this->ISO =& $region_array[ $db->fieldName( "CountryID" ) ];
        $this->Name =& $region_array[ $db->fieldName( "Abbreviation" ) ];
        $this->HasVAT =& $region_array[ $db->fieldName( "Name" ) ];
	$this->HasVAT =& $region_array[ $db->fieldName( "UserAdded" ) ];
	$this->HasVAT =& $region_array[ $db->fieldName( "Removed" ) ];
    }

    /*!
      Returns the total number of regions
    */
    function &getAllCount( $search = "" )
    {
        $db =& eZDB::globalDatabase();

        if ( !empty( $search ) )
        {
            $query = new eZQuery( array( "Name", "Abbreviation" ), $search );
            $search_arg = "AND " . $query->buildQuery();
//              $search_arg = "WHERE Name like '%$search%'";
        }

        $db->query_single( $region_countries, "SELECT COUNT( ID ) as Count FROM eZAddress_Region
                                        WHERE Removed=0 $search_arg
                                        " );
        return $region_countries[ $db->fieldName("Count") ];
    }

    /*!
      Returns every region as a eZRegion object
    */
    function &getAll( $as_object = true, $search = "", $offset = 0, $max = -1 )
    {
        $db =& eZDB::globalDatabase();

        $region_array = 0;
        $return_array = array();
    
        if ( $max >= 0 && is_numeric( $offset ) && is_numeric( $max ) )
        {
            $limit = array( "Limit" => $max,
                            "Offset" => $offset );
        }

        if ( !empty( $search ) )
        {
            $query = new eZQuery( array( "Name", "Abbreviation" ), $search );
            $search_arg = "AND " . $query->buildQuery();
//              $search_arg = "WHERE Name like '%$search%'";
        }

        if ( $as_object )
            $select = "*";
        else
            $select = "ID";

        $db->array_query( $region_array, "SELECT $select FROM eZAddress_Region
                                           WHERE Removed=0 $search_arg
                                           ORDER BY Name", $limit );
        
        if ( $as_object )
        {
            foreach ( $region_array as $region )
            {
                $return_array[] = new eZRegion( $region[ $db->fieldName( "ID" ) ] );
            }
        }
        else
        {
            foreach ( $region_array as $region )
            {
                $return_array[] = $region[ $db->fieldName( "ID" ) ];
            }
        }
        return $return_array;
    }

    /*!
      Returns every region as an array. This function is faster then the one above.
    */
    function &getAllArrayByCountry( $value )
    {
        $db =& eZDB::globalDatabase();
        $region_array = 0;
        $return_array = array();

        $db->array_query( $region_array, "SELECT * FROM eZAddress_Region
                                           WHERE Removed=0 AND CountryID=$value
                                           ORDER BY Name" );
        foreach ( $region_array as $region )
        {
            $return_array[] = array( "ID" =>      $region[ $db->fieldName( "ID" ) ],
                                     "CountryID" =>     $region[ $db->fieldName( "CountryID" ) ],
                                     "Abbreviation" =>    $region[ $db->fieldName( "Abbreviation" ) ],
                                     "Name" => $region[ $db->fieldName( "Name" ) ],
                                     "UserAdded" => $region[ $db->fieldName( "UserAdded" ) ],
                                     "Removed" => $region[ $db->fieldName( "Removed" ) ] );


        }
        return $return_array;
    }


    /*!
      Returns every region as an array. This function is faster then the one above.
    */
    function &getAllArray( )
    {
        $db =& eZDB::globalDatabase();
        $region_array = 0;
        $return_array = array();
        
        $db->array_query( $region_array, "SELECT * FROM eZAddress_Region
                                           WHERE Removed=0
                                           ORDER BY Name" );
        foreach ( $region_array as $region )
        {
            $return_array[] = array( "ID" =>      $region[ $db->fieldName( "ID" ) ],
                                     "CountryID" =>     $region[ $db->fieldName( "CountryID" ) ],
                                     "Abbreviation" =>    $region[ $db->fieldName( "Abbreviation" ) ],
		                     "Name" => $region[ $db->fieldName( "Name" ) ],
				     "UserAdded" => $region[ $db->fieldName( "UserAdded" ) ],
				     "Removed" => $region[ $db->fieldName( "Removed" ) ] );


        }
        return $return_array;
    }
    
    /*!
      Sletter adressen med ID == $id;
     */
    function delete( $id = false)
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();

        $db->begin();
        $res[] = $db->query( "DELETE FROM eZAddress_Region WHERE ID='$id'" );
        eZDB::finish( $res, $db );
    }    
    

    /*!
      Returns the object ID.
    */
    function id( )
    {
        return $this->ID;
    }
    
    /*!
      Returns the name of a region.
    */

    function name( )
    {
        return $this->Name;
    }

    /*!
      Sets the name of the region.
    */
    function setName( $value )
    {
       $this->Name = $value;
    }
    
    /*!      
      Returns the Id of the country.
    */
    function countryId( )
    {
        return $this->CountryID;
    }

    /*!      
      Sets the Id of the country.
    */
    function setCountryId( $value )
    {
       $this->CountryID = $value;
    }

    /*!
      Returns the abrev of a region.
    */
    function Abbreviation( )
    {
        return $this->Abbreviation;
    }

    /*!
      Sets the Abreviation of the region.
    */
    function setAbbreviation( $value )
    {
       $this->Abbreviation = $value;
    }

    /*!      
      Returns a boolian value (yes or no, 1 or 0).
    */
    function userAdded( )
    {
        return $this->UserAdded;
    }

    /*!
      Sets the boolian for UserAdded for the region.
    */
    function setUserAdded( $value )
    {
       $this->UserAdded = $value;
    }

    /*!      
      Returns a boolian value (yes or no, 1 or 0) if the region is removed from use.
    */
    function removed( )
    {
        return $this->Removed;
    }

    /*!
      Sets the boolian for Removed for the region if it is removed from use.
    */
    function setRemoved( $value )
    {
       $this->Removed = $value;
    }

    var $ID;
    var $CountryID;
    var $Name;
    var $Abbreviation;
    var $UserAdded;
    var $Removed;
}


?>
