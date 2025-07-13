<?php

require("./includes/header.inc.php");

forceLogin();
forcePermission("UpdateVehiclesAge");


$db->query("EXEC dbo.UpdateYears");

header("Location: dashboard.php");

