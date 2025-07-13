<?php

$title = "User stats";
$description = "Preview the monthly revenue generated/user";

require("./includes/header.inc.php");

forceLogin();
forcePermission("UserStats");

$query = $db->query("
SET NOCOUNT ON 
DECLARE @YEAR INT;
DECLARE @MONTH INT;
CREATE TABLE #MonthlyRentalReport (
    Dealer VARCHAR(32),
    RentedVehicleCount INT,
    [From] DATE,
    [To] DATE,
    [TotalIncome] DECIMAL(10,2)
)

DECLARE vehicleTypeCursor CURSOR FOR
SELECT DISTINCT YEAR(RentStartDate),  MONTH(RentStartDate)
FROM VehicleRental
ORDER BY YEAR(RentStartDate) DESC,  MONTH(RentStartDate) DESC

OPEN vehicleTypeCursor
FETCH NEXT FROM vehicleTypeCursor INTO @YEAR, @MONTH

WHILE @@FETCH_STATUS = 0
BEGIN
    DECLARE @StartMonth DATE
    DECLARE @EndMonth DATE
    SET @StartMonth = DATEFROMPARTS(@YEAR, @MONTH, 1)
    SET @EndMonth = EOMONTH(@StartMonth)

    INSERT INTO #MonthlyRentalReport
    SELECT [User].FullName AS Dealer,
        COUNT(VehicleId) AS RentedVehicleCount,
        @StartMonth AS [FROM],
        @EndMonth AS [TO],
        SUM(PenaltyFees + dbo.VehicleCost(VehicleId)) AS [TotalIncome]
    FROM VehicleRental
    LEFT JOIN [User]
    ON [User].Id = UserId
    WHERE RentReturnDate IS NOT NULL AND RentStartDate BETWEEN @StartMonth AND @EndMonth
    GROUP BY [User].FullName

    FETCH NEXT FROM vehicleTypeCursor INTO @YEAR, @MONTH
END
CLOSE vehicleTypeCursor
DEALLOCATE vehicleTypeCursor
SELECT * FROM #MonthlyRentalReport;
");
$rented = array();
while($row = $db->fetch_assoc($query))
{
    array_push($rented, $row);
}

?>

<br/>
<table>
    <tr>
        <th>Dealer Name</th>
        <th>From</th>
        <th>To</th>
        <th>Number of Rented Vehicles</th>
        <th>Total Income</th>
    </tr>
    <?php
    foreach($rented as $v)
    {
        ?>
        <tr>
            <td><?= $v["Dealer"] ?></td>
            <td><?= $v["From"]->format("Y-m-d") ?></td>
            <td><?= $v["To"]->format("Y-m-d") ?></td>
            <td><?= $v["RentedVehicleCount"] ?></td>
            <td><?= $v["TotalIncome"] ?></td>
        </tr>
        <?php
    }
    ?>
</table>

<?php
require("./includes/footer.inc.php");
?>
