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
    <input type="number" name="Longitude" step="any">
    
    <br></br>
    
       
    <lable>input the latitude: </lable>
    <input type="number" name="Latitude" step="any">
    
    <br></br>
           
    <lable>input the date: </lable>
    <input type="date" name="Date">
    
    <br></br>

    <lable>input the time: </lable>
    <input type="time" name="Time">
    
    <br></br>

    <button type="submit" name="operation" value="Conf">Conform Data </button> 
    
    
</form>

<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $latitude = $_POST["Latitude"];
    $longitude = $_POST["Longitude"];
    $date = $_POST["Date"];
    $time = $_POST["Time"];
    $operation = $_POST["operation"];
    
    try {
        if ($operation == "Conf") {

            $output = shell_exec(
                "../backend/calculate " .
                escapeshellarg($latitude) . " " .
                escapeshellarg($longitude) . " " .
                escapeshellarg($date) . " " .
                escapeshellarg($time)
            );

            echo "<pre>";
            echo htmlspecialchars($output);
            echo "</pre>";
        }
    } catch(Exception $e) {
            echo "ERROR: " . $e->getMessage();
    } 
}
?>
</body>
</html>
