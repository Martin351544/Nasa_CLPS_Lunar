<?php

function extractTable($result) {
    preg_match('/\$\$SOE(.*?)\$\$EOE/s', $result, $matches);

    if (!isset($matches[1])) {
        return [];
    }

    $lines = explode("\n", trim($matches[1]));
    $rows = [];

    foreach ($lines as $line) {
        if (preg_match(
            '/(\d{4}-[A-Za-z]{3}-\d{2})\s+(\d{2}:\d{2})\s+([* ])([i ]?)\s*([\d.-]+)\s+([\d.-]+)/',
            $line,
            $matches
        )) {
            $rows[] = [
                "date" => $matches[1],
                "time" => $matches[2],
                "visible" => trim($matches[3]) === "*",
                "interferer_above_horizon" => trim($matches[4]) === "i",
                "azimuth" => (float)$matches[5],
                "elevation" => (float)$matches[6]
            ];
        }
    }

    return $rows;
}


// Default values
$name = "";
$latitude = "";
$longitude = "";
$date = "";
$time = "";

$sunTable = [];
$earthTable = [];
$error = "";


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["Mname"] ?? "");
    $latitude = $_POST["Latitude"] ?? "";
    $longitude = $_POST["Longitude"] ?? "";
    $date = $_POST["Date"] ?? "";
    $time = $_POST["Time"] ?? "";

    // Validate input
    if (
        !is_numeric($latitude) ||
        !is_numeric($longitude) ||
        $latitude < -90 || $latitude > 90 ||
        $longitude < -180 || $longitude > 180 ||
        empty($date) ||
        empty($time)
    ) {
        $error = "Please enter valid coordinates, date and time.";
    } else {

        // Run C++ backend
        $binary = __DIR__ . "/../backend/calculate";

        $command =
            escapeshellarg($binary) . " " .
            escapeshellarg($latitude) . " " .
            escapeshellarg($longitude) . " " .
            escapeshellarg($date) . " " .
            escapeshellarg($time);

        $outputLines = [];
        $exitCode = 0;

        exec($command, $outputLines, $exitCode);

        $output = implode("\n", $outputLines);

        if ($exitCode !== 0) {
            $error = "The C++ backend returned an error.";
        } else {

            // Decode JSON
            $data = json_decode($output, true);

            if (!is_array($data)) {
                $error = "Failed to decode the C++ JSON output.";
            } elseif (isset($data["error"])) {
                $error = "Backend error: " . $data["error"];
            } elseif (!isset($data["sun"], $data["earth"])) {
                $error = "Missing Sun or Earth results in backend response.";
            } else {

                // Extract Sun and Earth data
                $sunTable = extractTable($data["sun"]);
                $earthTable = extractTable($data["earth"]);

                if (empty($sunTable) || empty($earthTable)) {
                    $error = "No usable observation data was returned.";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Moon landing planner</title>

    <style>

        body {
            background-color: #090D14;
            color: #E8EDF5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
        }

        h1 {
            color: #E8EDF5;
        }

        h2 {
            margin-top: 0;
        }

        .sidebars {
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        .input-sidebar {
            flex: 1;
            min-width: 250px;
        }

        .output-sidebar {
            flex: 3;
            min-width: 0;
        }

        .input-sidebar,
        .output-sidebar {
            background-color: #141B26;
            color: white;
            border: 1px solid #263244;
            border-radius: 12px;
            padding: 24px;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 9px;
            margin: 8px 0 16px;
            background: #090D14;
            color: white;
            border: 1px solid #263244;
            border-radius: 6px;
        }

        button {
            padding: 10px 16px;
            background: #2563EB;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #1D4ED8;
        }

        .error {
            color: #F87171;
            background: #351B22;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .results {
            background: #182333;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .results h3 {
            margin-top: 0;
        }

        #moon-container {
            width: 100%;
            height: 400px;
            background: #090D14;
            border-radius: 12px;
            overflow: hidden;
        }

        @media (max-width: 750px) {
            .sidebars {
                flex-direction: column;
            }

            .input-sidebar,
            .output-sidebar {
                box-sizing: border-box;
                width: 100%;
            }
        }

    </style>

</head>

<body>

<h1>Moon landing planner</h1>

<div class="sidebars">

    <!-- INPUT SIDEBAR -->

    <aside class="input-sidebar">

        <form method="POST">

            <label>Mission name:</label>

            <input
                type="text"
                name="Mname"
                value="<?= htmlspecialchars($name) ?>"
                required
            >

            <label>Longitude:</label>

            <input
                type="number"
                name="Longitude"
                id="Longitude"
                step="any"
                min="-180"
                max="180"
                value="<?= htmlspecialchars($longitude) ?>"
                required
            >

            <label>Latitude:</label>

            <input
                type="number"
                name="Latitude"
                id="Latitude"
                step="any"
                min="-90"
                max="90"
                value="<?= htmlspecialchars($latitude) ?>"
                required
            >

            <label>Date:</label>

            <input
                type="date"
                name="Date"
                value="<?= htmlspecialchars($date) ?>"
                required
            >

            <label>Time:</label>

            <input
                type="time"
                name="Time"
                value="<?= htmlspecialchars($time) ?>"
                required
            >

            <button type="submit" name="operation" value="Conf">
                Confirm data
            </button>

        </form>

    </aside>


    <!-- OUTPUT SIDEBAR -->

    <aside class="output-sidebar">

        <h2>Mission overview</h2>

        <?php if ($error !== ""): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>

            <p>
                Mission name:
                <?= htmlspecialchars($name) ?>
            </p>

            <p>
                Coordinates:
                <?= htmlspecialchars($latitude) ?>°,
                <?= htmlspecialchars($longitude) ?>°
            </p>

            <h2>Current conditions</h2>

            <!-- SUN RESULTS -->

            <div class="results">

                <h3>Sun conditions</h3>

                <?php foreach ($sunTable as $row): ?>

                    <p>
                        Time: <?= htmlspecialchars($row["time"]) ?><br>

                        Azimuth: <?= $row["azimuth"] ?>°<br>

                        Elevation: <?= $row["elevation"] ?>°<br>

                        Sun above horizon:
                        <?= $row["visible"] ? "Yes" : "No" ?><br>

                        Interfering body above horizon:
                        <?= $row["interferer_above_horizon"] ? "Yes" : "No" ?>
                    </p>

                <?php endforeach; ?>

            </div>


            <!-- EARTH RESULTS -->

            <div class="results">

                <h3>Earth conditions</h3>

                <?php foreach ($earthTable as $row): ?>

                    <p>
                        Time: <?= htmlspecialchars($row["time"]) ?><br>

                        Azimuth: <?= $row["azimuth"] ?>°<br>

                        Elevation: <?= $row["elevation"] ?>°<br>

                        Earth above horizon:
                        <?= $row["visible"] ? "Yes" : "No" ?><br>

                        Interfering body above horizon:
                        <?= $row["interferer_above_horizon"] ? "Yes" : "No" ?>
                    </p>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <p>Enter your mission details to calculate landing conditions.</p>

        <?php endif; ?>


        <!-- 3D MOON -->

        <h2>Landing site visualisation</h2>

        <div id="moon-container"></div>

    </aside>

</div>



<script type="importmap">
{
    "imports": {
        "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js"
    }
}
</script>


<script type="module">

import * as THREE from 'three';

const container = document.getElementById('moon-container');

const scene = new THREE.Scene();

const camera = new THREE.PerspectiveCamera(
    45,
    container.clientWidth / container.clientHeight,
    0.1,
    1000
);

camera.position.z = 3;


const renderer = new THREE.WebGLRenderer({
    antialias: true
});

renderer.setSize(
    container.clientWidth,
    container.clientHeight
);

renderer.setPixelRatio(
    Math.min(window.devicePixelRatio, 2)
);

container.appendChild(renderer.domElement);


// MOON TEXTURE

const texture = new THREE.TextureLoader().load(
    'https://threejs.org/examples/textures/planets/moon_1024.jpg'
);


// MOON

const geometry = new THREE.SphereGeometry(1, 64, 64);

const material = new THREE.MeshStandardMaterial({
    map: texture
});


const moon = new THREE.Mesh(geometry, material);

scene.add(moon);


// LIGHTING

const light = new THREE.DirectionalLight(0xffffff, 3);

light.position.set(5, 3, 5);

scene.add(light);

scene.add(new THREE.AmbientLight(0xffffff, 0.3));


// LANDING SITE MARKER
const markerGeometry = new THREE.SphereGeometry(
    0.025,
    16,
    16
);

const markerMaterial = new THREE.MeshBasicMaterial({
    color: 0xff3333,
    depthTest: false
});

const marker = new THREE.Mesh(
    markerGeometry,
    markerMaterial
);

moon.add(marker);
updateMarker(0, 0);

// UPDATE MARKER POSITION
function updateMarker(lat, lon) {
    const phi = lat * Math.PI / 180;
    const theta = lon * Math.PI / 180;

    const radius = 1.02;

    marker.position.set(
        radius * Math.cos(phi) * Math.sin(theta),
        radius * Math.sin(phi),
        radius * Math.cos(phi) * Math.cos(theta)
    );
}

// READ FORM INPUTS

function updateFromForm() {

    const lat = parseFloat(
        document.getElementById('Latitude').value
    );

    const lon = parseFloat(
        document.getElementById('Longitude').value
    );

    if (
        Number.isFinite(lat) &&
        Number.isFinite(lon) &&
        lat >= -90 &&
        lat <= 90 &&
        lon >= -180 &&
        lon <= 180
    ) {

        updateMarker(lat, lon);

    }

}


// LISTEN FOR INPUT CHANGES

document.getElementById('Latitude').addEventListener(
    'input',
    updateFromForm
);

document.getElementById('Longitude').addEventListener(
    'input',
    updateFromForm
);


// INITIAL MARKER POSITION

updateFromForm();


// RESIZE RENDERER

window.addEventListener('resize', () => {

    camera.aspect =
        container.clientWidth / container.clientHeight;

    camera.updateProjectionMatrix();

    renderer.setSize(
        container.clientWidth,
        container.clientHeight
    );

});


// ANIMATION
function animate() {
    requestAnimationFrame(animate);

    renderer.render(scene, camera);
}

animate();


</script>

</body>
</html>
