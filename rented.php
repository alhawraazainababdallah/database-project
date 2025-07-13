<?php

$title = "Rented";
$description = "Rent a car";

require("./includes/header.inc.php");

forceLogin();
forcePermission("Rented");

function getRentedVehicles()
{
    global $db;
    $todayDate = date("Y-m-d");
    $query = $db->query("EXEC dbo.GetRentedVehicles '{$todayDate}'");
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
        <th>Dealer Name</th>
        <th>Renter Name</th>
        <th>Phone Nb.</th>
        <th>Vehicle</th>
        <th>Price</th>
        <th>R.Date</th>
        <th>Del.Date</th>
        <th>Late</th>
        <th></th>
    </tr>
    <?php
    foreach(getRentedVehicles() as $v)
    {
        ?>
        <tr>
            <td><?= $v["Dealer"] ?></td>
            <td><?= $v["Name"] ?></td>
            <td><?= $v["PhoneNumber"] ?></td>
            <td><?= $v["Vehicle"] ?></td>
            <td><?= $v["Price"] ?></td>
            <td><?= $v["RentStartDate"]->format("Y-m-d  ") ?></td>
            <td><?= $v["RentEndDate"]->format("Y-m-d") ?></td>
            <td><?= $v["Late"] ?></td>
            <td><a class="btn" href="returnVehicle.php?id=<?= $v["Id"]?>">Return Vehicle</a></td>
        </tr>
        <?php
    }
    ?>
</table>

<?php
require("./includes/footer.inc.php");
?>
