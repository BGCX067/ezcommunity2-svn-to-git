<?
// 
// $Id: forgot.php 4484 2001-03-02 09:45:14Z ce $
//
// Christoffer A. Elo <ce@ez.no>
// Created on: <20-Sep-2000 13:32:11 ce>
//
// This source file is part of eZ publish, publishing software.
// Copyright (C) 1999-2001 eZ systems as
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

require( "ezuser/user/usercheck.php" );

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "ezuser/classes/ezforgot.php" );
include_once( "classes/ezmail.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZUserMain", "Language" );
$headersInfo = getallheaders();

$languageIni = new INIFIle( "ezuser/user/intl/" . $Language . "/forgot.php.ini", false );
// Get the user.
if ( $Login )
{
    $getUser = new eZUser();
    $user = $getUser->getUser( $Login );
}

if ( isSet ( $ChangeButton ) && ( $user == false ) )
{

    eZHTTPTool::header( "Location: /user/unsuccessfull/" );
    exit();
}

// Store the user with a unic hash and mail the hash variable to the user.
if ( $user )
{
    $subjectText = ( $languageIni->read_var( "strings", "subject_text" ) . " " . $headersInfo["Host"] );
    $bodyText = $languageIni->read_var( "strings", "body_text" );

    $forgot = new eZForgot();
    $forgot->get( $user );
    $forgot->setUserID( $user->id() );
    $userID = $user->id();
    $forgot->store();

    $mailpassword = new eZMail();
    $mailpassword->setTo( $user->email() );
    $mailpassword->setSubject( $subjectText );

    $body = ( $bodyText . "\n");
    $body .= ( "http://" . $headersInfo["Host"] . "/user/forgot/change/" . $forgot->Hash() );

    $mailpassword->setBody( $body );
    $mailpassword->send();

    eZHTTPTool::header( "Location: /user/successfull/" );
    exit();
}

if ( $Action == "change" )
{
    $change = new eZForgot();

    if ( $change->check( $Hash ) )
    {
        $change->get( $change->check( $Hash ) );
        $subjectNewPassword = $languageIni->read_var( "strings", "subject_text_password" );
        $bodyNewPassword = $languageIni->read_var( "strings", "body_text_password" );
        $passwordText = $languageIni->read_var( "strings", "password" );
        $userID = $change->userID();
        $user = new eZUser( $userID );
        $password = substr( md5( microtime() ), 0, 7 );
        $user->setPassword( $password );
        $user->store();
        $mail = new eZMail();
        $mail->setTo( $user->email() );
        $mail->setSubject( $subjectNewPassword . " " . $headersInfo["Host"] );

        $body = ( $bodyNewPassword . " " . $headersInfo["Host"] .".\n" );
        $body .= ( $passwordText . ": "  .  $password );
        $mail->setBody( $body );
        $mail->send();

        // Cleanup
        $change->get( $change->check( $Hash ) );
        $change->delete();
        eZHTTPTool::header( "Location: /user/generated/" );
    }
}

// Template
$t = new eZTemplate( "ezuser/user/" . $ini->read_var( "eZUserMain", "TemplateDir" ),
                     "ezuser/user/intl", $Language, "forgot.php" );
$t->setAllStrings();

$t->set_file( array(
    "login" => "forgot.tpl"
    ) );

$t->pparse( "output", "login" );

?>
