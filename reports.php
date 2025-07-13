<?php

$title = "Reports";
$description = "Reports for a vehicle";

require("./includes/header.inc.php");

forceLogin();
forcePermission("Reports");

function getVehiclesReports()
{
    global $db;
    $todayDate = date("Y-m-d");
    $query = $db->query("EXEC dbo.GetVehiclesReports");
    $rented = array();
    while($row = $db->fetch_assoc($query))
    {
        array_push($rented, $row);
    }
    return $rented;
}

?>

<br/>
<table>
    <tr>
        <th>Customer Name</th>
        <th>Vehicle</th>
        <th>Issue</th>
        <th>Explanation</th>
    </tr>
    <?php
    foreach(getVehiclesReports() as $v)
    {
        ?>
        <tr>
            <td><?= $v["CustomerName"] ?></td>
            <td><?= $v["VehicleName"] ?></td>
            <td><?= $v["Issue"] ?></td>
            <td><?= $v["Explanation"] ?></td>
        </tr>
        <?php
    }
    ?>
</table>

<?php
require("./includes/footer.inc.php");
?>
