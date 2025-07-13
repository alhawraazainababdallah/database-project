<?php

require_once("./includes/database.inc.php");
require_once("./includes/auth.inc.php");

$failed_login_attempt = false;

//Login form
if(isset($_POST["email"]) && isset($_POST["password"]))
{
    $email = $_POST["email"];
    $password = $_POST["password"];
    //mysql_query                 $db->query()
    $query = $db->query("SELECT * FROM [User] WHERE [Email]='{$email}' AND [Password]='{$password}'");
    //mysql_num_rows($query)      $db->num_rows
    if($db->num_rows($query) > 0)
    {
        //User exists with this Email and Password, now we will get all the permissions
        $row = $db->fetch_assoc($query);
        $name = $row["FullName"];
        $userId = $row["Id"];

        //Create the array of permissions
        $permissions = array(); //Has no permissions at the start

        //Fetch permissions if the user has any
        $perQuery = $db->query("EXEC GetUserPermissions {$userId}");

        while($row = $db->fetch_assoc($perQuery))
        {
            array_push($permissions, $row["PermissionName"]);
        }

        $_SESSION["id"] = $userId;
        $_SESSION["email"] = $email;
        $_SESSION["name"] = $name;
        $_SESSION["permissions"] = $permissions;
        header("Location: dashboard.php");
        die;
    }
    else
    {
        $failed_login_attempt = true;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style\main.css">
    <style>
        body
        {
            background-color: #BABDFF;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .circle
        {
            width: 75px;
            position: absolute;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            background-color: var(--clr-white);
        }
        .card
        {
            --radius: 75px;
            background-color: var(--clr-white);
            position: relative;
            padding: 20vh 30px;
            border-radius: 15px;
            display: flex;
            width: 70%;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 20px;
            box-shadow: 0px 20px 50px 0px #0000004D;
            
            
            &::before,
            &::after {
                width: var(--radius);
                position: absolute;
                content: '';
                aspect-ratio: 1 / 1;
                border-radius: 50%;
                z-index: -1;
                filter: blur(5px);
            }
            &::before {
                top: calc(-1 * var(--radius) / 2);
                left: calc(-1 * var(--radius) / 2);
                background: radial-gradient(50% 50% at 50% 50%, #ACA0FA 0%, #8C7AFF 100%);
            }
            &::after {
                bottom: calc(-1 * var(--radius) / 2);
                right: calc(-1 * var(--radius) / 2);
                background-color: white;
            }
        }
        form
        {
            width: 50%;

            & > input {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <h6 class="error">
                <?php
                if($failed_login_attempt) echo "Invalid Email/Password combination";
                ?>
            </h6>
            <input type="email" name="email" placeholder="Email"/>
            <br/>
            <input type="password" name="password" placeholder="Password" />
            <br/>
            <input type="submit" name="submit" value="Login" />
        </form>

    </div>
</body>
</html>
