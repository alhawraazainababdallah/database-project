<?php

$title = "Add vehicles";
$description = "Add a vehicle";

require("./includes/header.inc.php");

forceLogin();
forcePermission("AddVehicle");

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
    
    $randomName = basename(uniqid() . '_' . time() . '_' . $_FILES["image"]["name"]);
    $targetFile = "./images/" . $randomName;
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    if($type == "Car")
    {
        $db->query("EXEC dbo.AddCar '{$registrationNumber}', '{$brand}', '{$model}', {$age}, {$doors}, {$seats}, '{$randomName}'");
    }
    else
    {
        $db->query("EXEC dbo.AddBike '{$registrationNumber}', '{$brand}', '{$model}', {$age}, {$wheels}, {$engineSize}, '{$randomName}'");
    }

    header("Location: dashboard.php");
}

?>
<div style="width: 80%;margin: 0 auto;margin-top:100px;">
    <form method="POST" action="<?= $_SERVER["PHP_SELF"] ?>"  enctype="multipart/form-data">
        <h4>Vehicle Properties:</h4>
        <div class="spacedOptions">
            <label class="top">
                <span>Vehicle Brand</span>
                <input type="text" name="brand" />
            </label>
            <label class="top">
                <span>Vehicle Model</span>
                <input type="text" name="model" />
            </label>
        </div>
        <div class="spacedOptions">
            <label class="top">
                <span>Vehicle Type</span>
                <select id="type" name="type" >
                    <option value="Car">Car</option>
                    <option value="Bike">Bike</option>
                </select>
            </label>
            <label class="top">
                <span>Vehicle Age</span>
                <input type="number" name="age" min="0" max="50" />
            </label>
        </div>
        <div class="spacedOptions">
            <label class="top">
                <span>Registration Number</span>
                <input type="text" name="registrationNumber" />
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
                        <option value="2">2</option>
                        <option value="4">4</option>
                    </select>
                </label>
                <label class="top">
                    <span>Number of seats</span>
                    <select name="seats">
                        <option value="2">2</option>
                        <option value="4">4</option>
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
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </label>
                <label class="top">
                    <span>Engine size</span>
                    <input type="number" name="engineSize" min="50" max="2300" />
                </label>
            </div>
        </div>
        <br/>
        <div class="centered">
            <input type="submit" name="submit" value="Add Vehicle" />
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
