<?
/*!
    $Id: main.php 635 2000-07-14 12:55:44Z lw-cvs $

    Author: Lars Wilhelmsen <lw@ez.no>
    
    Created on: <14-Jul-2000 12:49:01 lw>
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/
include( "ezforum/dbsettings.php" );
include( "ezphputils.php" );
include( "template.inc" );

include( "$DOCROOT/classes/ezdb.php" );
include( "$DOCROOT/classes/ezforumcategory.php" );
include( "$DOCROOT/classes/ezuser.php" );
include( "$DOCROOT/classes/ezsession.php" );

//preliminary setup
$cat = new eZforumCategory;
$usr = new eZUser;
$session = new eZSession();

$t = new Template(".");
$t->set_file( array("main" => "$DOCROOT/templates/main.tpl",
                    "elements" => "$DOCROOT/templates/main-elements.tpl",
                    "login" => "$DOCROOT/templates/main-login.tpl",
                    "logout" => "$DOCROOT/templates/main-logout.tpl"
                    ) );

$t->set_var( "docroot", $DOCROOT);
$categories = $cat->getAllCategories();

// category list
for ($i = 0; $i < count($categories); $i++)
{
    $Id = $categories[$i]["Id"];
    $Name = $categories[$i]["Name"];
    $Description = $categories[$i]["Description"];
        
    $t->set_var("id", $Id);
    $t->set_var("name", $Name);
    $t->set_var("link",$link);
    $t->set_var("description",$Description);
        
    if ( ($i % 2) != 0)
        $t->set_var( "color", "#eeeeee" );
    else
        $t->set_var( "color", "#bbbbbb" );
            
    $t->parse( "categories", "elements", true);
}

// login / logout

if ( $session->validate( $AuthenticatedSession ) == 0   )
{
    //UserId = $session->UserID();
    $usr->get( $session->UserID() );
    $t->set_var( "nick_name", $usr->nickName() );
    $t->set_var( "first_name", $usr->firstName() );
    $t->set_var( "last_name", $usr->lastName() );
    $t->set_var( "email", $usr->email() );
    $t->parse( "loginlogout", "logout", true);
}
else
{
    $t->parse( "loginlogout", "login", true);
}

//parse template file
$t->pparse("output","main");
?>
