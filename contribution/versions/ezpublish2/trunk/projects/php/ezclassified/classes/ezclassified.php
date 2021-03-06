<?
// 
// $Id: ezclassified.php 2790 2000-12-21 20:20:07Z jb $
//
// Definition of eZClassified class
//
// <real-name><<email-name>>
// Created on: <09-Nov-2000 14:52:40 ce>
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

//!! eZClassified
//! eZClassified handles company information.
/*!

  Example code:
  \code
  $company = new eZClassified();
  $company->setTitle( "Coder" );
  $company->store();

  \endcode

  \sa eZPerson eZAddress
*/

//require "ezphputils.php";

include_once( "ezcontact/classes/ezcompany.php" );
include_once( "ezclassified/classes/ezcategory.php" );
include_once( "classes/ezdate.php" );
// include_once( "ezcontact/classes/ezonline.php" );

class eZClassified
{
    /*!
      Constructs a new eZClassified object.

      If $id is set the object's values are fetched from the
      database.
    */
    function eZClassified( $id="-1", $fetch=true )
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
      Stores a company to the database
    */
    function store( )
    {
        $this->dbInit();

        if ( !isSet( $this->ID ) )
        {
        
            $this->Database->query( "INSERT INTO eZClassified_Classified SET
                                                  Title='$this->Title',
	                                              Description='$this->Description',
	                                              UserID='$this->UserID',
	                                              Price='$this->Price',
	                                              Created=now(),
                                                  Updated=now(),
                                                  ValidUntil='$this->ValidUntil'
                                                  ");
            $this->ID = mysql_insert_id();
            
            $this->State_ = "Coherent";
            
            $this->Status_ = "Insert";
        }
        else
        {
            $this->Database->query( "UPDATE eZClassified_Classified SET
                                                  Title='$this->Title',
	                                              Description='$this->Description',
	                                              UserID='$this->UserID',
	                                              Price='$this->Price',
	                                              Created=Created,
                                                  Updated=now(),
                                                  ValidUntil='$this->ValidUntil'
                                               	  WHERE ID='$this->ID'
                                               	  " );
            $this->State_ = "Coherent";

            $this->Status_ = "Update";
        }

        return true;
    }

    /*
      Deletes a eZClassified object  from the database.
    */
    function delete()
    {
        $this->dbInit();

        if ( isSet( $this->ID ) )
        {
            $this->Database->query( "DELETE FROM eZClassified_ClassifiedCategoryLink WHERE ClassifiedID='$this->ID'" );
            $this->Database->query( "DELETE FROM eZClassified_ClassifiedCompanyLink WHERE ClassifiedID='$this->ID'" );

            // Temporary fix to deal with deleting the company/classified relation,
            // will be moved to eZClassified_ClassifiedCompanyLink later.
//              $this->Database->query( "DELETE FROM eZContact_CompanyClassifiedDict WHERE ClassifiedID='$this->ID'" );

            $this->Database->query( "DELETE FROM eZClassified_Classified WHERE ID='$this->ID'" );
            $this->Database->query( "DELETE FROM eZClassified_Position WHERE ID='$this->ID'" );
        }
        return true;
    }

  
    /*!
      Fetches the object information from the database.
    */
    function get( $id=-1 )
    {
        $this->dbInit();
        $ret = false;

        if ( $id != "" )
        {
            $this->Database->array_query( $classified_array, "SELECT * FROM eZClassified_Classified WHERE ID='$id'" );
            if ( count( $classified_array ) > 1 )
            {
                die( "Error: More than one company with the same id was found. " );
            }
            else if ( count( $classified_array ) == 1 )
            {
                $this->ID = $classified_array[0]["ID"];
                $this->Title = $classified_array[0]["Title"];
                $this->Description = $classified_array[0]["Description"];
                $this->UserID = $classified_array[0]["UserID"];
                $this->Price = $classified_array[0]["Price"];
                $this->Dato = $classified_array[0]["Dato"];
                $this->Created = $classified_array[0]["Created"];
                $this->validUntil = $classified_array[0]["ValidUntil"];
                     
                $ret = true;
            }
            $this->State_ = "Coherent";
        }
        else
        {
            $this->State_ = "Dirty";
        }
        return $ret;
    }
    

    /*
      Returns all the company found in the database.
      
      The company are returned as an array of eZClassified objects.
    */
    function getAll( )
    {
        $this->dbInit();
        
        $classified_array = array();
        $return_array = array();
    
        $this->Database->array_query( $classified_array, "SELECT ID FROM eZClassified_Classified ORDER BY Title" );

        foreach( $classified_array as $classifiedItem )
        {
            $return_array[] = new eZClassified( $classifiedItem["ID"] );
        }
        return $return_array;
    }

    /*
      Returns all the company found in the database.
      
      The company are returned as an array of eZClassified objects.
    */
    function getByCategory( $categoryID )
    {
        $this->dbInit();

        $classified_array = array();
        $return_array = array();
    
        $this->Database->array_query( $classified_array, "SELECT ClassifiedID FROM eZClassified_ClassifiedCategoryLink WHERE CategoryID='$categoryID'" );

        foreach( $classified_array as $classifiedItem )
        {
            $return_array[] = new eZClassified( $classifiedItem["ClassifiedID"] );
        }
        return $return_array;
    }

    /*
      Returns all the company found in the database.
      
      The company are returned as an array of eZClassified objects.
    */
    function getByCompanyID( $companyID )
    {
        $this->dbInit();
        
        $classified_array = array();
        $return_array = array();
    
        $this->Database->array_query( $classified_array, "SELECT ClassifiedID FROM eZClassified_ClassifiedCompanyLink WHERE CompanyID='$companyID'" );

        foreach( $classified_array as $classifiedItem )
        {
            $return_array[] = new eZClassified( $classifiedItem["ClassifiedID"] );
        }
        return $return_array;
    }

    /*!
        Get all valid classified.
     */
    function getAllValid( $categoryID, $valid=true )
    {
        $this->dbInit();
        $item_array = 0;
        $classified_array = 0;
        $return_array = array();

        if ( $valid == true )
        {
            $query = ( "SELECT ID as ClassifiedID FROM eZClassified_Classified WHERE ValidUntil >= CURRENT_DATE() ORDER BY Created" );
        }
        else
        {
            $query = ( "SELECT ID as ClassifiedID FROM eZClassified_Classified WHERE ValidUntil <= CURRENT_DATE() ORDER BY Created" );
        }
        $this->Database->array_query( $item_array, $query );

        for( $i=0; $i<count( $item_array ); $i++ )
        {
            $ClassifiedID = $item_array[$i]["ClassifiedID"];

            $query = ( "SELECT ClassifiedID FROM eZClassified_ClassifiedCategoryLink WHERE CategoryID='$categoryID' AND ClassifiedID='$ClassifiedID'" );
            $this->Database->array_query( $classified_array, $query );
            
            foreach( $classified_array as $item )
            {
                $return_array[] = new eZClassified( $item["ClassifiedID"] );
            }
        
        }
            return $return_array;
    }


    
    /*
      Henter ut alle firma i databasen som inneholder søkestrengen.
    */
    function search( $query )
    {
        $this->dbInit();    
        $classified_array = array();
        $return_array = array();
        
        $query = "SELECT ID FROM eZClassified_Classified WHERE Title LIKE '%%$query%%' ORDER BY Title";

        $this->Database->array_query( $classified_array, $query );

        foreach( $classified_array as $classifiedItem )
        {
            $return_array[] = new eZClassified( $classifiedItem["ID"] );
        }
        return $return_array;
    }

    /*
      Search the classified database in a single category, using query as the search string in classified title.
    */
    function searchByCategory( $categoryID, $query )
    {
        $this->dbInit();
        
        $classified_array = array();
        $return_array = array();
        if( !empty( $query ) )
        {
            $query = "
                SELECT 
                    Comp.ID 
                FROM
                    eZClassified_ClassifiedCategoryLink as Dict,
                    eZClassified_Classified as Comp
                WHERE
                    Comp.Title LIKE '%$query%'
                AND
                    Dict.CategoryID = '$categoryID'
                AND
                    Comp.ID = Dict.ClassifiedID
                ORDER BY Title";
            $this->Database->array_query( $classified_array, $query );
            foreach( $classified_array as $classifiedItem )
            {
                $return_array[] = new eZClassified( $classifiedItem["ID"] );
            }
        }
        
        return $return_array;
    }
    /*!
      Set the company to the eZClassified object.
    */
    function setCompany( $company )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $ret = false;

        $this->dbInit();
        
        if ( get_class( $company ) == "ezcompany" )
        {
            $companyID = $company->id();

            $ret_array = array();
            $this->Database->array_query( $ret_array, "SELECT * FROM eZClassified_ClassifiedCompanyLink
                                     WHERE ClassifiedID='$this->ID'" );
            $update = false;
            if ( count( $ret_array ) != 0 )
                $update = true;

            if ( $update )
                $this->Database->query( "UPDATE eZClassified_ClassifiedCompanyLink
                                     SET CompanyID='$companyID' WHERE ClassifiedID='$this->ID'" );
            else
                $this->Database->query( "INSERT INTO eZClassified_ClassifiedCompanyLink
                                     SET CompanyID='$companyID', ClassifiedID='$this->ID'" );
            $ret = true;
        }
        else
        {
            die( "eZClassified::setCompany(): cannot set the company relation without a company object" );
        }
        return $ret;
    }

    /*!
      Returns the logo image of the company as a eZImage object.
    */
    function company( )
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $ret = false;
       $this->dbInit();


//         $this->Database->array_query( $res_array, "SELECT CompanyID FROM eZContact_CompanyClassifiedDict
//                                       WHERE
//                                       ClassifiedID='$this->ID'
//                                     " );
       $this->Database->array_query( $res_array, "SELECT CompanyID FROM eZClassified_ClassifiedCompanyLink
                                     WHERE
                                     ClassifiedID='$this->ID'
                                   " );

       if ( count( $res_array ) < 1 )
       {
           die( "Error in eZClassified::company(): No company with ID=" . $this->ID . " was found. " );
       }
       else if ( count( $res_array ) == 1 )
       {
           if ( $res_array[0]["CompanyID"] != "NULL" )
           {
               $ret = new eZCompany( $res_array[0]["CompanyID"], false );
           }
       }
       else
       {
           die( "Error in eZClassified::company(): More than one company with the same id was found. " );
       }
       
       return $ret;
    }

    /*!
      Removes the company from every user category.
    */
    function removeCompanies()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $this->dbInit();
                                    
//         $this->Database->query( "DELETE FROM eZContact_CompanyClassifiedDict
//                                  WHERE ClassifiedID='$this->ID'" );
       $this->Database->query( "DELETE FROM eZClassified_ClassifiedCompanyLink
                                WHERE ClassifiedID='$this->ID'" );

    }

    /*!
      Removes the company from every user category.
    */
    function removeCategories()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->dbInit();

        $this->Database->query( "DELETE FROM eZClassified_ClassifiedCategoryLink
                                WHERE ClassifiedID='$this->ID'" );
    }

    /*!
      Returns the categories that belong to this eZClassified object.
    */
    function categories( $itemID )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $return_array = array();
        $this->dbInit();

        $query = "SELECT CategoryID
                                                 FROM eZClassified_ClassifiedCategoryLink
                                                 WHERE ClassifiedID='$itemID'";
        $this->Database->array_query( $categories_array, $query );

        foreach( $categories_array as $categoriesItem )
        {
            $return_array[] = new eZCategory( $categoriesItem["CategoryID"] );
        }

        return $return_array;
    }
   

    /*!
      Sets the title of the advertisement.
    */
    function setTitle( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Title = $value;
    }

    /*!
      Sets the description of the company.
    */
    function setDescription( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Description = $value;
    }


    /*!
      Sets the description of the company.
    */
    function setPrice( $value )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $this->Price = $value;
    }

    function setUser( $user )
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        if ( get_class( $user ) == "ezuser" )
        {
            $userID = $user->id();
            $this->UserID = $userID;
        }
    }

    function setValidUntil( $year, $month, $day )
    {
        if( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        $date = new eZDate( $year, $month, $day );

        $mySQLDate = $date->mySQLDate();

        $this->ValidUntil = $mySQLDate;
    }



    /*!
      Returnerer ID.
    */
    function id()
    {
        return $this->ID;
    }

    /*!
      Returnerer firmanavn.
    */
    function title()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Title;
    }

    /*!
      Returnerer kommentar.
    */
    function description()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Description;
    }

    /*!
      Returnerer kommentar.
    */
    function price()
    {
        if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

        return $this->Price;
    }
    
    /*!
      Returns the postimg time as a eZTimeDate object.
    */
    function created()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $dateTime = new eZDateTime();

       $dateTime->setMySQLTimeStamp( $this->Created );
       
       return $dateTime;
    }

    /*!
      Returns the postimg time as a eZTimeDate object.
    */
    function validUntil()
    {
       if ( $this->State_ == "Dirty" )
            $this->get( $this->ID );

       $date = new eZDate( );
       $date->setMySQLDate( $this->validUntil );
       
       return $date;
    }

    
    /*!
      \private
      Private function.
      Open the database for read and write. Gets all the database information from site.ini.
    */
    function dbInit( )
    {
        if ( $this->IsConnected == false )
        {
            $this->Database = new eZDB( "site.ini", "site" );
            $this->IsConnected = true;
        }
    }

    var $ID;
    var $Title;
    var $Description;
    var $UserID;
    var $Price;
    var $Date;
    var $Created;
    var $Updated;
    var $ValidUntil;

    ///  Variable for keeping the database connection.
    var $Database;

    /// Indicates the state of the object. In regard to database information.
    var $State_;
    /// Is true if the object has database connection, false if not.
    var $IsConnected;

    /// Check for update or insert
    var $Status_;
}

?>
