<?php

$title = "Book vehicle";
$description = "Book the selected vehicle";

require("./includes/header.inc.php");

forceLogin();
forcePermission("BookVehicle");

$id = $_GET["id"];

$query = $db->query("SELECT [dbo].[CarCost]($id) AS Cost");
$price = $db->fetch_assoc($query)["Cost"];

if(isset($_POST["submit"]))
{
    $name = $_POST["name"];
    $phoneNumber = $_POST["phoneNumber"];
    $from = $_POST["from"];
    $to = $_POST["to"];
    $userId = $_SESSION["id"];

    $db->query("EXEC [dbo].[RentVehicle] $userId, $id, '$name', '$phoneNumber', '$from', '$to'");

    header("Location: dashboard.php");
}

?>
<div style="width: 80%;margin-inline: 50px;margin-top:100px;">
    <form method="POST" action="<?= $_SERVER["PHP_SELF"] ?>?id=<?=$id?>">
        <h3 style="font-weight: bold";>Client's details:</h3>
        <br/>
        <div class="spacedOptions">
            <label class="top">
                <span>Full name</span>
                <input type="text" name="name" />
            </label>
            <label class="top">
                <span>Phone number</span>
                <input type="text" name="phoneNumber" />
            </label>
        </div>
        <div class="spacedOptions">
            <label class="top">
                <span>From</span>
                <input type="date" name="from" id="from" />
            </label>
            <label class="top">
                <span>To</span>
                <input type="date" name="to" id="to" />
            </label>
        </div>
        <br/>
        <span>Note: Vehicle is billed at $<span><?= $price ?></span> per day, which amounts to $<span id="total"></span></span><br/>
        <input style="float: right;" type="submit" name="submit" value="Book" />
    </form>
</div>

<script>
    const from = document.getElementById("from");
    const to = document.getElementById("to");
    const total = document.getElementById("total");

    function update()
    {
        const startDate = new Date(from.value);
        const endDate = new Date(to.value);

        if (!isNaN(startDate) && !isNaN(endDate)) {
            const difference = endDate - startDate;
            const daysDifference = Math.floor(difference / (1000 * 60 * 60 * 24));

            total.textContent = daysDifference * <?= $price ?>;
        }
    }
    from.addEventListener('change', update);
    to.addEventListener('change', update);

</script>


<?php
require("./includes/footer.inc.php");
?>
