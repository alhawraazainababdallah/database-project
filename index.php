<?php

require_once("./includes/header.inc.php");

if(isLoggedIn())
{
    header("Location: dashboard.php");
}
else
{
    header("Location: login.php");
}

?>
