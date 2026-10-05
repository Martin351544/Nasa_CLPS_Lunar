<html>
<body>
<h1>Lunar Location</h1>
<head>
    <!--defile the style-->
    <style>
        body {
            background-color: grey;
            background-size: cover;
        }
    </style>
</head>

<!--take the inputs-->
<form method="POST"> 
    <lable>input the longitude: </lable>
    <input type="number" name="Longitude">
    
    <br></br>
    
       
    <lable>input the latitude: </lable>
    <input type="number" name="Latitude">
    
    <br></br>
           
    <lable>input the date: </lable>
    <input type="number" name="Date">
    
    <br></br>

    <lable>input the time: </lable>
    <input type="number" name="Time">
    
    <br></br>

    <button type="submit" name="operation" value="Conf">Conform Data </button> 
    
    
</form>

<?php
// handle input the longitude, latitude, date and time
$latitude = $_POST["Latitude"];
$longitude = $_POST["Longitude"];
$date = $_POST["Date"];
$time = $_POST["Time"];
$conf = $_POST["operation"];

if ($operation == "Conf") {
    // to be implimented call the c++ calculate function 
    // the calculate function will also use the API
} 
?>

</body>
</html>
