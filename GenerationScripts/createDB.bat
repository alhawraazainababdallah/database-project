set SERV= -S DESKTOP-D2ER5Q2 -E -i

osql %SERV% VehicleRental.sql -o createDB.log
osql %SERV% AddDummyData.sql -o addDummyData.log
