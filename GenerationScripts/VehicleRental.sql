CREATE DATABASE VehicleRental;
GO
USE VehicleRental;
GO
--Table Generation START
CREATE TABLE Vehicle (
	Id INT PRIMARY KEY IDENTITY(1,1),
	RegistrationNumber VARCHAR(8) NOT NULL,
	Brand VARCHAR(8) NOT NULL,
	Model VARCHAR(8) NOT NULL,
	Age int NOT NULL,
	Available BIT NOT NULL,
	[Image] VARCHAR(255) NOT NULL,

	CONSTRAINT CheckRegistrationLength CHECK (LEN(RegistrationNumber) BETWEEN 2 AND 8) --The registration number can only be between 2 and 8 characters only
);


CREATE TABLE Car (
	CarId INT PRIMARY KEY,
	NumOfSeats INT NOT NULL,
	NumOfDoors INT NOT NULL,

	CONSTRAINT NumOfCarSeats CHECK(NumOfSeats IN (2, 4)),
	CONSTRAINT NumOfCarDoors CHECK(NumOfDoors IN (2, 4)),
	CONSTRAINT FK_Car_Vehicle FOREIGN KEY (CarId) REFERENCES Vehicle(Id)
);


CREATE TABLE Bike (
	BikeId INT PRIMARY KEY,
	EngineSize INT NOT NULL,
	NumOfWheels INT NOT NULL,

	CONSTRAINT NumOfBikeWheels CHECK(NumOfWheels BETWEEN 2 AND 4),
	CONSTRAINT BikeEngineSize CHECK(EngineSize BETWEEN 50 AND 2300),
	CONSTRAINT FK_Bike_Vehicle FOREIGN KEY (BikeId) REFERENCES Vehicle(Id)
);


CREATE TABLE [User] (
	Id INT PRIMARY KEY IDENTITY(1, 1),
	FullName VARCHAR(32) NOT NULL,
	Email VARCHAR(32) NOT NULL,
	[Password] VARCHAR(32) NOT NULL
)


CREATE TABLE [Permission] (
	[Id] INT PRIMARY KEY IDENTITY(1, 1),
	[Name] VARCHAR(20) NOT NULL
);


CREATE TABLE [UserPermissions] (
	[UserId] INT NOT NULL,
	[PermissionId] INT NOT NULL,
	
	CONSTRAINT PK_UserPermissions PRIMARY KEY (UserId, PermissionId),
	CONSTRAINT FK_User_UserPermission FOREIGN KEY (UserId) REFERENCES [User](Id),
	CONSTRAINT FK_Permission_UserPermission FOREIGN KEY (PermissionId) REFERENCES [Permission](Id)
);


CREATE TABLE VehicleRental (
	[Id] INT PRIMARY KEY IDENTITY(1, 1),
	[UserId] INT NOT NULL,
	[VehicleId] INT NOT NULL,
	[Name] VARCHAR(50),
	[PhoneNumber] VARCHAR(32),
	[RentStartDate] DATE NOT NULL,
	[RentEndDate] DATE NOT NULL,
	[RentReturnDate] DATE,
	[PenaltyFees] DECIMAL(10, 2),
	[Rate] INT
	

	CONSTRAINT FK_User_VehicleRental FOREIGN KEY (UserId) REFERENCES [User](Id),
	CONSTRAINT FK_Vehicle_VehicleRental FOREIGN KEY (VehicleId) REFERENCES [Vehicle](Id)
);


CREATE TABLE Reports (
	Id INT PRIMARY KEY IDENTITY(1,1),
	VehicleRentalId INT NOT NULL,
	Issue VARCHAR(50) NOT NULL,
	Explanation VARCHAR(200) NOT NULL,

	CONSTRAINT FK_Reports_VehicleRental FOREIGN KEY (VehicleRentalId) REFERENCES VehicleRental(Id)
);
GO

--Table Generation END


--FUNCTIONS + PROCEDURE START

CREATE PROCEDURE AddCar
@RegistrationNumber VARCHAR(8),
@Brand VARCHAR(8),
@Model VARCHAR(8),
@Age INT,
@NumOfSeats INT,
@NumOfDoors INT,
@Image VARCHAR(64)
AS
BEGIN
    DECLARE @InsertedVehicleId INT;

	INSERT INTO Vehicle (RegistrationNumber, Brand, Model, Age, Available, [Image]) VALUES
	(@RegistrationNumber, @Brand, @Model, @Age, 1, @Image);
	SET @InsertedVehicleId = @@IDENTITY;
	INSERT INTO Car (CarId, NumOfSeats, NumOfDoors) VALUES (@InsertedVehicleId, @NumOfSeats, @NumOfDoors);
END
GO


CREATE PROCEDURE AddBike
@RegistrationNumber VARCHAR(8),
@Brand VARCHAR(8),
@Model VARCHAR(8),
@Age INT,
@NumberOfWheels INT,
@EngineSize INT,
@Image VARCHAR(64)
AS
BEGIN
    DECLARE @InsertedVehicleId INT;

	INSERT INTO Vehicle (RegistrationNumber, Brand, Model, Age, Available, [Image]) VALUES
	(@RegistrationNumber, @Brand, @Model, @Age, 1, @Image);
	SET @InsertedVehicleId = @@IDENTITY;
	INSERT INTO Bike (BikeId, NumOfWheels, EngineSize) VALUES (@InsertedVehicleId, @NumberOfWheels, @EngineSize);
END
GO


CREATE FUNCTION CarCost(@CarId INT)
RETURNS DECIMAL(10, 2)
BEGIN
	DECLARE @cost DECIMAL(10, 2);
	DECLARE @Age INT;
	DECLARE @NumOfDoors INT;

	SELECT @Age = Age, @NumOfDoors = NumOfDoors
	FROM Vehicle
	INNER JOIN Car
	ON Car.CarId = CarId AND Vehicle.Id = CarId

	SET @Cost = 30 - @Age * 1.5 - @NumOfDoors * 2;

	RETURN @Cost;
END
GO


CREATE FUNCTION BikeCost(@BikeId INT)
RETURNS DECIMAL(10, 2)
BEGIN
	DECLARE @cost DECIMAL(10, 2);
	DECLARE @Age INT;
	DECLARE @EngineSize INT;

	SELECT @Age = Age, @EngineSize = EngineSize
	FROM Vehicle
	INNER JOIN Bike
	ON Bike.BikeId = BikeId AND Vehicle.Id = BikeId

	SET @Cost = 15 + @EngineSize / 100;

	RETURN @Cost;
END
GO

CREATE FUNCTION VehicleCost(@VehicleId INT)
RETURNS DECIMAL(10, 2)
BEGIN
	DECLARE @Count INT;
	SELECT @Count = COUNT(*) FROM Car WHERE CarId = @VehicleId;

	IF @Count != 0
	BEGIN
		RETURN dbo.CarCost(@VehicleId);
	END

	RETURN dbo.BikeCost(@VehicleId);
END
GO

CREATE FUNCTION VehicleRating(@Id INT)
RETURNS DECIMAL(10, 2)
BEGIN
	DECLARE @Rating DECIMAL(10, 2) = 0;

	SELECT @Rating = AVG(Rate) FROM VehicleRental
	WHERE VehicleId = @Id;

	SET @Rating = ISNULL(@Rating, 0);

	RETURN @Rating;
END
GO


CREATE PROCEDURE UpdateYears
AS
BEGIN
	DECLARE @VehicleId INT
	DECLARE @VehicleAge INT

	DECLARE VehicleCursor CURSOR FOR
	SELECT Id, Age FROM Vehicle

	OPEN VehicleCursor
	FETCH NEXT FROM VehicleCursor INTO @VehicleId, @VehicleAge

	WHILE @@FETCH_STATUS = 0
	BEGIN
		-- Perform operations on each row
		SET @VehicleAge = @VehicleAge + 1

		-- Update the Age for the current row
		UPDATE Vehicle
		SET Age = @VehicleAge
		WHERE Id = @VehicleId

		FETCH NEXT FROM VehicleCursor INTO @VehicleId, @VehicleAge
	END

	CLOSE vehicleCursor
	DEALLOCATE vehicleCursor
END
GO


CREATE PROCEDURE GetUserPermissions
@UserId INT
AS
BEGIN
	SELECT [Name] AS [PermissionName] FROM [UserPermissions]
	INNER JOIN [Permission]
	ON [UserPermissions].[PermissionId] = [Permission].[Id]
	WHERE UserId=@UserId
END
GO

CREATE PROCEDURE GetRentedVehicles
	@Today Date
AS
BEGIN
	SELECT VehicleRental.Id AS Id, [User].FullName AS Dealer, [Name], PhoneNumber, CONCAT(Vehicle.Brand, ' - ', Vehicle.Model) AS Vehicle, dbo.VehicleCost(VehicleId) * DATEDIFF(DAY, RentStartDate, RentEndDate) AS Price, RentStartDate, RentEndDate, CASE WHEN RentEndDate < @Today THEN 'Yes' ELSE 'No' END AS Late
	FROM VehicleRental
	LEFT JOIN Vehicle ON Vehicle.Id = VehicleId
	LEFT JOIN [User] ON [User].Id = UserId
	WHERE RentReturnDate IS NULL;
END
GO



CREATE PROCEDURE GetVehiclesReports
AS
BEGIN
	SELECT [Name] AS CustomerName, CONCAT(Vehicle.Brand, ' - ', Vehicle.Model) AS VehicleName, Issue, Explanation
	FROM Reports
	LEFT JOIN VehicleRental
	ON VehicleRental.Id = Reports.VehicleRentalId
	LEFT JOIN Vehicle
	ON Vehicle.Id = VehicleRental.VehicleId
END
GO


CREATE PROCEDURE GenerateRentalReport
AS
BEGIN
	SET NOCOUNT ON
	DECLARE @YEAR INT;
	DECLARE @MONTH INT;
	-- Create a temporary table to store the report data
    CREATE TABLE #MonthlyRentalReport (
        Dealer VARCHAR(32),
        RentedVehicleCount INT,
		[From] DATE,
		[To] DATE,
		[TotalIncome] DECIMAL(10,2)
    )

	--This cursor is to get the year and month, unique ones for all rents that have happened
	DECLARE vehicleTypeCursor CURSOR FOR
	SELECT DISTINCT YEAR(RentStartDate),  MONTH(RentStartDate)
	FROM VehicleRental
	ORDER BY YEAR(RentStartDate) DESC,  MONTH(RentStartDate) DESC

	OPEN vehicleTypeCursor
    FETCH NEXT FROM vehicleTypeCursor INTO @YEAR, @MONTH

    WHILE @@FETCH_STATUS = 0
    BEGIN
		--Generate the start and end of the month
		DECLARE @StartMonth DATE
		DECLARE @EndMonth DATE
		SET @StartMonth = DATEFROMPARTS(@YEAR, @MONTH, 1)
		SET @EndMonth = EOMONTH(@StartMonth)

		--Get all the profits/user that have occurred between these two timestamps
		--Where the vehicle has been returned
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
	SELECT * FROM #MonthlyRentalReport;
	CLOSE vehicleTypeCursor
    DEALLOCATE vehicleTypeCursor
END
GO

CREATE PROCEDURE RentVehicle
    @UserId INT,
	@VehicleId INT,
	@Name VARCHAR(50),
	@PhoneNumber VARCHAR(32),
	@RentStartDate DATE,
	@RentEndDate  DATE
AS
BEGIN
    DECLARE @Available BIT;
	SELECT
		@Available = [Available]
    FROM Vehicle
	WHERE [Id] = @VehicleId;

	IF @Available = 1
	BEGIN
		INSERT INTO VehicleRental (UserId, VehicleId, [Name], [PhoneNumber], RentStartDate, RentEndDate)
		VALUES (@UserId, @VehicleId, @Name, @PhoneNumber, @RentStartDate, @RentEndDate);
	END
END
GO

--FUNCTIONS + PROCEDURE END


--Triggers START

CREATE TRIGGER PreventVehicleDeletion
ON Vehicle
INSTEAD OF DELETE
AS
BEGIN
    IF EXISTS (
        SELECT 1
        FROM deleted d
        INNER JOIN VehicleRental vr ON d.Id = vr.VehicleId
        WHERE vr.RentReturnDate IS NULL
    )
    BEGIN
        RAISERROR ('Cannot delete vehicle with active rental', 16, 1)
        ROLLBACK TRANSACTION;
    END;
    ELSE
    BEGIN
        DELETE FROM Vehicle WHERE Id IN (SELECT Id FROM deleted);
    END;
END;
GO




CREATE TRIGGER LateReturnPenalty
ON VehicleRental
AFTER UPDATE
AS
BEGIN
	DECLARE @DaysLate INT;
    DECLARE @VehicleId INT;
    DECLARE @Id INT;
    SELECT
        @DaysLate = DATEDIFF(day, i.RentEndDate, i.RentReturnDate),
        @Id = Id,
        @VehicleId = VehicleId
    FROM inserted i;
	IF @DaysLate < 0
    BEGIN
		SET @DaysLate = 0;
    END
    UPDATE VehicleRental SET PenaltyFees = dbo.VehicleCost(@VehicleId) * @DaysLate * 2 WHERE Id = @Id;
END;
GO


CREATE TRIGGER UpdateVehicleAvailability
ON VehicleRental
AFTER INSERT, UPDATE
AS
BEGIN
    IF EXISTS (SELECT 1 FROM INSERTED WHERE [RentReturnDate] IS NULL)
    BEGIN
        UPDATE Vehicle 
        SET Available = 0
        FROM Vehicle v
        INNER JOIN inserted i ON v.Id = i.VehicleId;
    END
    ELSE
    BEGIN
        UPDATE Vehicle 
		SET Available = 1
        FROM Vehicle v
        INNER JOIN deleted d ON v.Id = d.VehicleId;
    END;
END;
GO



--Triggers END


