<?php

$title = "Update vehicle";
$description = "Update vehicle information";

require("./includes/header.inc.php");

forceLogin();
forcePermission("EditVehicle");


$id = $_GET["id"];
//Update vehicle information
if(isset($_POST["submit"]))
{
    $registrationNumber = $_POST["registrationNumber"];
    $brand = $_POST["brand"];
    $model = $_POST["model"];
    $type = $_POST["type"];
    $age = $_POST["age"];
    $doors = @$_POST["doors"];
    $seats = @$_POST["seats"];
    $wheels = @$_POST["wheels"];
    $engineSize = @$_POST["engineSize"];
    
    $db->query("UPDATE Vehicle SET RegistrationNumber='$registrationNumber', Brand='$brand', Model='$model', Age=$age WHERE Id=$id");
    if($type == "Car")
    {
        $db->query("UPDATE Car SET NumOfSeats=$seats, NumOfDoors=$doors WHERE CarId=$id");
    }
    else
    {
        $db->query("UPDATE Bike SET NumOfWheels=$wheels, EngineSize=$engineSize WHERE CarId=$id");
    }

    if($_FILES["image"]["size"])
    {
        $randomName = basename(uniqid() . '_' . time() . '_' . $_FILES["image"]["name"]); // Generate a random name for the image
        $targetFile = "./images/" . $randomName; // Create a target file path
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
        $db->query("UPDATE Vehicle SET [Image]='$randomName' WHERE Id=$id");
    }

    header("Location: dashboard.php");

}
//Get Vehicle Information
$getVehicle = $db->query("SELECT * FROM Vehicle LEFT JOIN Car ON Vehicle.Id = Car.CarId LEFT JOIN Bike ON Vehicle.Id = Bike.BikeId WHERE Vehicle.Id = $id");
$row = $db->fetch_assoc($getVehicle);


?>
<div style="width: 80%;margin: 0 auto;margin-top:100px;">
    <form method="POST" action="<?= $_SERVER["PHP_SELF"] ?>?id=<?= $id ?>"  enctype="multipart/form-data">
        <h4>Vehicle Properties:</h4>
        <div class="spacedOptions">
            <label class="top">
                <span>Vehicle Brand</span>
                <input type="text" name="brand" value="<?= $row["Brand"]?>" />
            </label>
            <label class="top">
                <span>Vehicle Model</span>
                <input type="text" name="model" value="<?= $row["Model"]?>" />
            </label>
        </div>
        <div class="spacedOptions">
            <label class="top">
                <span>Vehicle Type</span>
                <select id="type" name="type" >
                    <option value="Car" <?= $row["CarId"] != NULL ? "selected" : "" ?>>Car</option>
                    <option value="Bike" <?= $row["BikeId"] != NULL ? "selected" : "" ?>>Bike</option>
                </select>
            </label>
            <label class="top">
                <span>Vehicle Age</span>
                <input type="number" name="age" min="0" max="50" value="<?= $row["Age"]?>" />
            </label>
        </div>
        <div class="spacedOptions">
            <label class="top">
                <span>Registration Number</span>
                <input type="text" name="registrationNumber" value="<?= $row["RegistrationNumber"]?>" />
            </label>
            <label class="top">
                <span>Image</span>
                <input type="file" name="image" accept="image/*" />
            </label>
        </div>
        <label class="top">
        </label>
        <div id="carProperties">
            <h4 style="font-weight: bold" class="vertical-space">Car Properties</h4>
            <div class="spacedOptions">
                <label class="top">
                    <span>Number of doors</span>
                    <select name="doors">
                        <option value="2" <?= $row["NumOfDoors"] != NULL && $row["NumOfDoors"] == 2 ? "selected" : "" ?> >2</option>
                        <option value="4" <?= $row["NumOfDoors"] != NULL && $row["NumOfDoors"] == 4 ? "selected" : "" ?> >4</option>
                    </select>
                </label>
                <label class="top">
                    <span>Number of seats</span>
                    <select name="seats">
                        <option value="2" <?= $row["NumOfSeats"] != NULL && $row["NumOfSeats"] == 2 ? "selected" : "" ?> >2</option>
                        <option value="4" <?= $row["NumOfSeats"] != NULL && $row["NumOfSeats"] == 4 ? "selected" : "" ?> >4</option>
                    </select>
                </label>
            </div>
        </div>
        <div id="bikeProperties">
            <h4 style="font-weight: bold" class="vertical-space">Bike Properties</h4>
            <div class="spacedOptions">
                <label class="top">
                    <span>Number of wheels</span>
                    <select name="wheels">
                        <option value="2" <?= $row["NumOfWheels"] != NULL && $row["NumOfWheels"] == 2 ? "selected" : "" ?> >2</option>
                        <option value="3" <?= $row["NumOfWheels"] != NULL && $row["NumOfWheels"] == 4 ? "selected" : "" ?> >3</option>
                    </select>
                </label>
                <label class="top">
                    <span>Engine size</span>
                    <input type="number" name="engineSize" min="50" max="2300" value="<?= $row["EngineSize"] ?>" />
                </label>
            </div>
        </div>
        <br/>
        <div class="centered">
            <input type="submit" name="submit" value="Update Vehicle" />
        </div>
    </form>
</div>


<script>
    const carProperties = document.getElementById("carProperties");
    const bikeProperties = document.getElementById("bikeProperties");
    const vehicleType = document.getElementById("type");

    function vehicleTypeChangeCallback()
    {
        if(vehicleType.value == "Car")
        {
            carProperties.style.display = "block";
            bikeProperties.style.display = "none";
        }
        else
        {
            carProperties.style.display = "none";
            bikeProperties.style.display = "block";
        }
    }
    vehicleType.onchange = vehicleTypeChangeCallback;
    window.onload = function()
    {
        vehicleTypeChangeCallback();
    }
</script>


<?php
require("./includes/footer.inc.php");
?>
