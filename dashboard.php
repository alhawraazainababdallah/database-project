<?php

$title = "Dashboard";
$description = "Choose your car";

require("./includes/header.inc.php");

forceLogin();

//Get all vehicle brands
function getVehicleBrands()
{
    global $db;
    $query = $db->query("SELECT DISTINCT [Brand] FROM Vehicle");
    $brands = array();
    while($row = $db->fetch_assoc($query))
    {
        array_push($brands, $row["Brand"]);
    }
    return $brands;
}
function getVehicleModels()
{
    global $db;
    $query = $db->query("SELECT DISTINCT [Model] FROM Vehicle");
    $models = array();
    while($row = $db->fetch_assoc($query))
    {
        array_push($models, $row["Model"]);
    }
    return $models;
}
//Default value to be filled in the form:
$showRented = isEmptyString(@$_GET["showRented"]) ? "0" : "1";
$type = isEmptyString(@$_GET["type"]) ? "Car" : $_GET["type"];

$brand = isEmptyString(@$_GET["brand"]) ? "Any" : $_GET["brand"];

$model = isEmptyString(@$_GET["model"]) ? "Any" : $_GET["model"];

$seats = isEmptyString(@$_GET["seats"]) ? "2" : $_GET["seats"];
$doors = isEmptyString(@$_GET["doors"]) ? "2" : $_GET["doors"];

$age = isEmptyString(@$_GET["age"]) ? 50 : $_GET["age"];

$from = isEmptyString(@$_GET["from"]) ? 0 : @$_GET["from"];
$to = isEmptyString(@$_GET["to"]) ? 100 : @$_GET["to"];

$wheels = isEmptyString(@$_GET["wheels"]) ? 2 : $_GET["wheels"];
$engineSize = isEmptyString(@$_GET["engineSize"]) ? "" : $_GET["engineSize"];

//SQL Conditions
$showRentedCondition = $showRented == "0" ? "AND Available=1 " : "";
$brandCondition = $brand == "Any" ? "" : " AND Brand = '{$brand}'";
$modelCondition = $model == "Any" ? "" : " AND Brand = '{$model}'";
$engineSizeCondition = $engineSize == "" ? "" : " AND EngineSize = '{$engineSize}'";

$vehiclesFound = array();

if($type == "Car")
{
    $query = $db->query("SELECT Id, Brand, Model, Age, Available, [Image], dbo.CarCost(Id) AS Cost, dbo.VehicleRating(Id) AS Rating FROM Car
    LEFT JOIN Vehicle
    ON Id = CarId
    WHERE NumOfSeats={$seats} AND NumOfDoors={$doors} {$showRentedCondition} {$brandCondition} {$modelCondition} AND age <= {$age} AND dbo.CarCost(Id) BETWEEN {$from} AND {$to}");
}
else
{
    //Bike
    $query = $db->query("SELECT Id, Brand, Model, Age, Available, [Image], dbo.BikeCost(Id) AS Cost, dbo.VehicleRating(Id) AS Rating FROM Bike
    LEFT JOIN Vehicle
    ON Id = BikeId
    WHERE NumOfWheels={$wheels} {$engineSizeCondition} {$showRentedCondition} {$brandCondition} {$modelCondition} AND age <= {$age} AND dbo.BikeCost(Id) BETWEEN {$from} AND {$to}");
}

while($row = $db->fetch_assoc($query))
{
    array_push($vehiclesFound, $row);
}

?>

<div style="display: flex;">
<!--Filters -->
<section style="width: 40%;">
<div style="margin:0 auto;width: 80%;">
    <br/>
    <h6>Filter By:</h6>
    <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
        <!-- <input style="width: 100%" type="text" name="search" placeholder="Search" /><br/> -->
        <div style="float:right;">
            <a href="<?= $_SERVER["PHP_SELF"] ?>" style="color: var(--clr-gray-text)">Reset All X</a>
        </div>
        <br style="clear:both" />
        <br/>
        <div style="float:right;">
            <label>
                <input type="checkbox" name="showRented" value="1" <?= $showRented == "1" ? "checked" : "" ?> /> 
                <span class="checkbox"></span><span style="font-weight: bold;">Show Rented Vehicles</span>
            </label>
        </div>
        <br style="clear:both" />
        <label class="shifted">
            <span>Vehicle Type</span>
            <select name="type" id="type">
                <option value="Car" <?= $type == "Car" ? "selected" : "" ?> >Car</option>
                <option value="Bike" <?= $type == "Bike" ? "selected" : "" ?> >Bike</option>
            </select>
        </label>
        <br/>
        <h6 style="font-weight: bold" class="vertical-space">Price Range</h6>
        <div style="display: flex; justify-content: space-around;align-items: center">
            From <input type="number" name="from" style="width: 100px;margin-bottom: 0px"  min="0" value="<?= $from ?>"/>
            To   <input type="number" name="to"   style="width: 100px;margin-bottom: 0px"  max="100" value="<?= $to ?>"/>
        </div>

        <h6 style="font-weight: bold" class="vertical-space">Vehicle Details</h6>
        <label class="shifted">
            <span>Vehicle Brand</span>
            <select name="brand">
                <option value="Any" <?= $brand == "Any" ? "selected" : "" ?>>Any</option>
                <?php
                    foreach(getVehicleBrands() as $vbrand)
                    {
                        $selected = $brand == $vbrand ? "selected" : "";
                        echo "<option value='{$vbrand}' {$selected}>{$vbrand}</option>";
                    }
                ?>
            </select>
        </label>
        <br/>
        <br/>
        <label class="shifted">
            <span>Vehicle Model</span>
            <select name="model">
                <option value="Any" <?= $brand == "Any" ? "selected" : "" ?>>Any</option>
                <?php
                    foreach(getVehicleModels() as $vmodel)
                    {
                        $selected = $model == $vmodel ? "selected" : "";
                        echo "<option value='{$vmodel}' {$selected}>{$vmodel}</option>";
                    }
                ?>
            </select>
        </label>
        <br/>
        <br/>
        <label class="shifted">
            <span>Vehicle Age < n years </span>
            <input type="number" name="age" min="0" value="<?= $age ?>" />
        </label>
        <br/>
        <br/>

        <hr />

        <div id="carProperties">
            <h6 style="font-weight: bold" class="vertical-space">Car Properties</h6>
            <label class="top">
                <span>Number of doors</span>
                <select name="doors">
                    <option value="2" <?= $doors == "2" ? "selected" : "" ?> >2</option>
                    <option value="4" <?= $doors == "4" ? "selected" : "" ?> >4</option>
                </select>
            </label>
            <label class="top">
                <span>Number of seats</span>
                <select name="seats">
                    <option value="2" <?= $seats == "2" ? "selected" : "" ?> >2</option>
                    <option value="4" <?= $seats == "4" ? "selected" : "" ?> >4</option>
                </select>
            </label>
        </div>
        <div id="bikeProperties">
            <h6 style="font-weight: bold" class="vertical-space">Bike Properties</h6>
            <label class="top">
                <span>Number of wheels</span>
                <select name="wheels">
                    <option value="2" <?= $wheels == "2" ? "selected" : "" ?> >2</option>
                    <option value="3" <?= $wheels == "3" ? "selected" : "" ?> >3</option>
                </select>
            </label>
            <label class="top">
                <span>Engine size</span>
                <input type="number" name="engineSize" value="<?= $engineSize ?>" />
            </label>
        </div>
        <br/>
        <input style="float:right" type="submit" name="submit" value="Search" />
        <br style="clear:both;" />
    </form>
</div>

</section>

<!--Cards -->

<section style="width: 60%;border-left: 2px solid var(--clr-primary);">
    <div style="width: 90%;margin: 0 auto;">
        <br/>
        <h6 style="font-weight: bold"><?= count($vehiclesFound) ?> Vehicles found</h6>
        <br/>
        <br/>
        <section class="vehicleCardContainer">
            <?php
            foreach($vehiclesFound as $v)
            {
                ?>
                <div class="vehicleCard">
                    <div class="cardHeader">
                        <span>Rating: <?= $v["Rating"] ?> / 5</span>
                        <div>
                            <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.25 18.3C11.25 17.9022 11.4146 17.5206 11.7076 17.2393C12.0007 16.958 12.3981 16.8 12.8125 16.8C13.2269 16.8 13.6243 16.958 13.9174 17.2393C14.2104 17.5206 14.375 17.9022 14.375 18.3C14.375 18.6978 14.2104 19.0793 13.9174 19.3607C13.6243 19.642 13.2269 19.8 12.8125 19.8C12.3981 19.8 12.0007 19.642 11.7076 19.3607C11.4146 19.0793 11.25 18.6978 11.25 18.3ZM11.25 12.3C11.25 11.9022 11.4146 11.5206 11.7076 11.2393C12.0007 10.958 12.3981 10.8 12.8125 10.8C13.2269 10.8 13.6243 10.958 13.9174 11.2393C14.2104 11.5206 14.375 11.9022 14.375 12.3C14.375 12.6978 14.2104 13.0793 13.9174 13.3606C13.6243 13.642 13.2269 13.8 12.8125 13.8C12.3981 13.8 12.0007 13.642 11.7076 13.3606C11.4146 13.0793 11.25 12.6978 11.25 12.3ZM11.25 6.29999C11.25 5.90216 11.4146 5.52063 11.7076 5.23933C12.0007 4.95802 12.3981 4.79999 12.8125 4.79999C13.2269 4.79999 13.6243 4.95802 13.9174 5.23933C14.2104 5.52063 14.375 5.90216 14.375 6.29999C14.375 6.69781 14.2104 7.07934 13.9174 7.36065C13.6243 7.64195 13.2269 7.79999 12.8125 7.79999C12.3981 7.79999 12.0007 7.64195 11.7076 7.36065C11.4146 7.07934 11.25 6.69781 11.25 6.29999Z" fill="#0007B2"/>
                            </svg>
                            <ul>
                                <?php
                                if($v["Available"] != 0 && hasPermission("BookVehicle"))
                                {
                                    ?>
                                    <li><a href="bookVehicle.php?id=<?=$v["Id"]?>">Book</a></li>
                                    <?php
                                }
                                if(hasPermission("EditVehicle"))
                                {
                                    ?>
                                    <li><a href="updateVehicle.php?id=<?=$v["Id"]?>">Edit</a></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                    if($v["Available"] == 1)
                    {
                        ?>
                        <div class="cardAvailable">
                            <span>Available Now</span>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="cardImage">
                        <!-- <a href="showVehicle.php?id=<?= $v["Id"] ?>"> -->
                            <img src="./images/<?= $v["Image"]?>" />
                        <!-- </a> -->
                    </div>
                    <hr/>
                    <div class="cardFooter">
                        <span>
                            <?php echo $v["Brand"]." - ".$v["Model"]." - ".(date("Y") - $v["Age"]); ?>
                        </span>
                        <span>
                            <?= $v["Cost"] ?>$/hour
                        </span>
                    </div>
                </div>
                <?php
            }
            ?>
        </section>
    </div>
<?php
?>
</section>

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
