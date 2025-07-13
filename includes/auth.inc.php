<?php

session_start();

function isLoggedIn()
{
    if (isset($_SESSION["email"]) && !empty($_SESSION["email"]))
    {
        return true;
    }
    return false;
}

function hasPermission($permission)
{
    if(isset($_SESSION["permissions"]) && !empty($_SESSION["permissions"]))
    {
        $permissionArray = $_SESSION["permissions"];
        foreach ($permissionArray as $userPermission)
        {
            if ($userPermission == $permission) return true;
        }
    }
    return false;
}

function forceLogin()
{
    if(!isLoggedIn())
    {
        header("Location: index.php");
        die();
    }
}

function forcePermission($permission)
{
    if(!hasPermission($permission))
    {
        header("Location: index.php");
        die();
    }
}
?>