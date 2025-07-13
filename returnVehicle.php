<?php

$title = "Return vehicle";
// $description = "Return the vehicle";

require("./includes/header.inc.php");

forceLogin();
forcePermission("ReturnVehicle");

$id = $_GET["id"];

$query = $db->query("SELECT dbo.VehicleCost(VehicleId) * DATEDIFF(DAY, RentStartDate, RentEndDate) AS Price FROM VehicleRental WHERE Id={$id}");
$row = $db->fetch_assoc($query);
$price = $row["Price"];

//Here we add the query which gets the real price and checks if there is any fees and so on

if(isset($_POST["submit"]))
{
    $rating = @$_POST["rating"] ?? 1;
    $issue = $_POST["issue"];
    $explanation = $_POST["explanation"];

    $todayDate = date("Y-m-d");
    if(isset($_POST["fileIssue"]))
    {
        $db->query("INSERT INTO Reports (VehicleRentalId, Issue, Explanation) VALUES ($id, '$issue', '$explanation')");
    }
    $db->query("UPDATE VehicleRental SET RentReturnDate = '{$todayDate}', Rate={$rating} WHERE Id={$id}");

    $query = $db->query("SELECT ISNULL(PenaltyFees, 0) AS PenaltyFees, DATEDIFF(day, RentEndDate, RentReturnDate) AS LateDays FROM VehicleRental WHERE Id={$id}");
    if($db->num_rows($query) != 0)
    {
        $row = $db->fetch_assoc($query);
        $penalty = number_format((float)$row["PenaltyFees"], 2, '.', '');
        if($penalty <= 0)
        {
            header("Location: dashboard.php");
            die;
        }
        echo "<h1 style='display:flex; 	align-items: center;height: 100vh;width: 100%;justify-content: center;'>Due to being late for {$row["LateDays"]}, you have been charged $ {$row["PenaltyFees"]}</h1>";
        die;
    }

    die;

}

//Here we post this to the server

?>
<div style="margin-inline: 20px;margin-top: 50px;">
    <form method="POST" action="<?= $_SERVER["PHP_SELF"] ?>?id=<?=$id?>"  enctype="multipart/form-data">
        <h4>Total Price <span style="margin-left: 10px;"><?= $price ?>$</span></h4>
        <div class="spacedOptions" style="width: 50%;margin-left: 100px;">
            <label class="top">
                <span>Rating</span>
                <input type="number" min="1" max="5" name="rating" value="5" />
            </label>
            <label>
                <input type="checkbox" name="fileIssue" id="fileIssue" /> 
                <span class="checkbox"></span><span>File an issue with the vehicle</span>
            </label>
        </div>
        <div id="issueSection" style="margin-top: 50px">
            <h4 style="text-decoration: underline;font-weight: 300">Please Fill Out the Below Form</h4>
            <div style="margin-top: 45px;margin-left: 50px;">
                <h6 style="font-weight: bold;">Define the issue</h6>
                <div style="margin-top: 40px;display: flex;gap: 50px;">
                    <label>
                        <input type="radio" name="issue" value="Delay" checked /> 
                        <span class="radio">Delay</span>
                    </label>
                    <label>
                        <input type="radio" name="issue" value="False Car" /> 
                        <span class="radio">False Car</span>
                    </label>
                    <label>
                        <input type="radio" name="issue" value="Engine Issue" /> 
                        <span class="radio">Engine Issue</span>
                    </label>
                    <label>
                        <input type="radio" name="issue" value="Other" /> 
                        <span class="radio">Other</span>
                    </label>
                </div>
            </div>
            <div style="margin-left: 50px;margin-top: 50px">
                <h6 style="font-weight: bold;">Explain</h6>
                <textarea name="explanation"></textarea>
            </div>
        </div>
        <input style="float:right;margin-top: 20px;margin-right: 61px;" type="submit" name="submit" value="Submit" />
    </form>
</div>

<script>
    const fileIssue = document.getElementById("fileIssue");
    const issueSection = document.getElementById("issueSection");

    function fileIssueCallback(e)
    {
        const checked = e.target.checked;
        if(checked)
        {
            issueSection.style.display = "block";
        }
        else
        {
            issueSection.style.display = "none";
        }
    }
    fileIssue.onchange = fileIssueCallback;
    window.onload = fileIssueCallback;
</script>

<?php
require("./includes/footer.inc.php");
?>
