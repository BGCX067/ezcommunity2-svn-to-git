<?
/*!
    $Id: index.php 635 2000-07-14 12:55:44Z lw-cvs $

    Author: Lars Wilhelmsen <lw@ez.no>
    
    Created on: <14-Jul-2000 12:51:24 lw>
    
    Copyright (C) 2000 eZ systems. All rights reserved.
*/
if ( file_exists( $prePage ) )
{
    include( $prePage );
    die();
}

require('header.php');
echo $userId;

if ($page)
{
    include($page);
}
else include('main.php');
require('footer.php');
?>
