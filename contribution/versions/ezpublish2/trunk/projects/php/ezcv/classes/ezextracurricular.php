<?
// 
// $Id: ezextracurricular.php 2594 2000-12-11 12:08:19Z pkej $
//
// Definition of eZExtracurricular class
//
// <Paul K Egell-Johnsen><pkej@ez.no>
// Created on: <20-Nov-2000 10:34:14 pkej>
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

//!! eZExtracurricular
//! eZExtracurricular handles educational information.
/*!
 */
 
class eZExtracurricular
{
    /*!
        Constructs an eZExtracurricular object.
      
        If $id is set, the object's values are fetched from the database.
     */
    function eZExtracurricular( $id='', $fetch=false )
    {
        $this->IsConnected = false;
        if( !empty( $id ) )
        {
            $this->ID = $id;
            if( $fetch == true )
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
        Store this object's data.
     */
    function store()
    {
        $this->dbInit();
        if( !isSet( $this->ID ) )
        {
        
            $this->Created = gmdate( "YmdHis", time());
            
            $this->Database->query
            ( "
                INSERT INTO
                    eZCV_Extracurricular
                SET
                    Start='$this->Start',
                    End='$this->End',
                    Organization='$this->Organization',
	                Position='$this->Position',
	                Speciality='$this->Speciality',
	                Comment='$this->Comment'
            " );
            $this->ID = mysql_insert_id();            
            $this->State_ = "Coherent";
        }
        else
        {
            $this->Database->query
            ( "
                UPDATE
                    eZCV_Extracurricular
                SET
                    Start='$this->Start',
                    End='$this->End',
                    Organization='$this->Organization',
	                Position='$this->Position',
	                Speciality='$this->Speciality',
	                Comment='$this->Comment'
                WHERE
                    ID='$this->ID'
            " );
            $this->State_ = "Coherent";
        }
    }
    
    
    /*!
        Get this object's data.
     */
    function get( $id )
    {
        $this->dbInit();    
        if( $id != "" )
        {
            $this->Database->array_query( $objectArray, "SELECT * FROM eZCV_Extracurricular WHERE ID='$id'" );
            if( count( $objectArray ) > 1 )
            {
                die( "Feil: Flere cver med samme ID funnet i database, dette skal ikke v�re mulig. " );
            }
            else if( count( $objectArray ) == 1 )
            {
                $this->ID = $objectArray[ 0 ][ "ID" ];
                $this->Start = $objectArray[ 0 ][ "Start" ];
                $this->End = $objectArray[ 0 ][ "End" ];
                $this->Organization = $objectArray[ 0 ][ "Organization" ];
                $this->Position = $objectArray[ 0 ][ "Position" ];
                $this->Speciality = $objectArray[ 0 ][ "Speciality" ];
                $this->Comment = $objectArray[ 0 ][ "Comment" ];
            }
        }
    }
    
    /*!
        Get all objects.
     */
    function getAll()
    {
        $this->dbInit();
        $item_array = 0;
        $return_array = array();
    
        $this->Database->array_query( $item_array, "SELECT ID FROM eZCV_Extracurricular ORDER BY End" );

        foreach( $item_array as $item )
        {
            $return_array[] = new eZExtracurricular( $item["ID"] );
        }
        return $return_array;
    }
    
    /*!
        Returns the ID of this object.
     */
    function id()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->ID;
    }
    
    /*!
        Set the ID of this object to $value.
    */
    function setID( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->ID = $value;
    }

    /*!
        Returns the Start of this object.
     */
    function start()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Start;
    }
    
    /*!
        Set the Start of this object to $value.
    */
    function setStart( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Start = $value;
    }
    /*!
        Returns the End of this object.
     */
    function end()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->End;
    }
    
    /*!
        Set the End of this object to $value.
    */
    function setEnd( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->End = $value;
    }
    /*!
        Returns the Organization of this object.
     */
    function organization()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Organization;
    }
    
    /*!
        Set the Organization of this object to $value.
    */
    function setOrganization( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Organization = $value;
    }
    /*!
        Returns the Position of this object.
     */
    function position()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Position;
    }
    
    /*!
        Set the Position of this object to $value.
    */
    function setPosition( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Position = $value;
    }

    /*!
        Returns the Speciality of this object.
     */
    function speciality()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Speciality;
    }
    
    /*!
        Set the Speciality of this object to $value.
    */
    function setSpeciality( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Speciality = $value;
    }

    /*!
        Returns the Comment of this object.
     */
    function comment()
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Comment;
    }
    
    /*!
        Set the Comment of this object to $value.
    */
    function setComment( $value )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Comment = $value;
    }


    /*!
      \private
      Used by this class to connect to the database.
    */
    function dbInit()
    {
        if( $this->IsConnected == false )
        {
            $this->Database = new eZDB( "site.ini", "site" );
            $this->IsConnected = true;
        }
    }
    
    var $ID;
    var $Start;
    var $End;
    var $Comment;
    var $Speciality;
    var $Position;
    var $Organization;
    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;
};
?>
