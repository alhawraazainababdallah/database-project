<?php

function getCurrentFile()
{
    $requestUri = $_SERVER["PHP_SELF"];

    $parts = explode("/", $requestUri);
    
    return $parts[count($parts) -1];
}


function updateNav(&$arr)
{
	for($i = 0; $i < count($arr); $i++)
	{
		if($arr[$i]["href"] == getCurrentFile())
		{
			$arr[$i]["class"] = "selected";
		}
		if(!isset($arr[$i]["permissionName"])) continue;
		$permissionName = $arr[$i]["permissionName"];
		if(hasPermission($permissionName))
		{
			$arr[$i]["hasPermission"] = true;
		}
	}
}

function getFirstNav()
{
    $nav = array(
        array("name" => "Dashboard", "class" => "", "href" => "dashboard.php", "hasPermission" => true),
        array("name" => "Rented", "class" => "", "href" => "rented.php", "hasPermission" => false, "permissionName" => "Rented"),
        array("name" => "Add Vehicles", "class" => "", "href" => "addvehicle.php", "hasPermission" => false, "permissionName" => "AddVehicle"),
        array("name" => "User Stats", "class" => "", "href" => "userstats.php", "hasPermission" => false, "permissionName" => "UserStats"),
        array("name" => "Reports", "class" => "", "href" => "reports.php", "hasPermission" => false, "permissionName" => "Reports"),
    );
    updateNav($nav);
    return $nav;
}

function getSecondNav()
{
    $nav = array(
        array("name" => "Update V. age", "class" => "", "href" => "updatevehicleage.php", "hasPermission" => false, "permissionName" => "UpdateVehiclesAge"),
        array("name" => "Logout", "class" => "", "href" => "logout.php", "hasPermission" => true),
    );
    updateNav($nav);
    return $nav;
}
function isEmptyString($a)
{
    return !isset($a) || strlen($a) == 0;
}
?>