<?
/*!
    $Id: logout.php 635 2000-07-14 12:55:44Z lw-cvs $

    Author: Lars Wilhelmsen <lw@ez.no>
    
    Created on: <14-Jul-2000 12:45:59 lw>
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/
include( "ezforum/dbsettings.php" );
include( "ezphputils.php" );
include( "$DOCROOT/classes/ezsession.php" );

$session = new eZSession();
$session->delete( $AuthenticatedSession );
//setCookie( "AuthenticatedSession", "", time() - 10000);

printRedirect( "index.php?page=$DOCROOT/main.php" );
//echo "<a href=\"index.php?page=$DOCROOT/main.php\">main</a><br>";

//echo $AuthenticatedSession;
?>
