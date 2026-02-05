<?php
session_start();
header("Content-Type: application/json");

$gridSize = 10;
$shipSizes = [2, 3, 5];

// Reset game
if (isset($_GET["reset"])) {
    session_destroy();
    echo json_encode(["reset" => true]);
    exit;
}

// Initialize game
if (!isset($_SESSION["ships"])) {
    $_SESSION["ships"] = [];
    $_SESSION["hits"] = [];

    foreach ($shipSizes as $size) {
        placeShip($size);
    }
}

$row = intval($_POST["row"]);
$col = intval($_POST["col"]);
$key = "$row,$col";

$result = "miss";

// Check hit
foreach ($_SESSION["ships"] as $shipIndex => $ship) {
    if (in_array($key, $ship)) {
        $_SESSION["hits"][] = $key;
        $result = "hit";
        break;
    }
}

// Count remaining ships
$remainingShips = 0;
foreach ($_SESSION["ships"] as $ship) {
    $sunk = true;
    foreach ($ship as $cell) {
        if (!in_array($cell, $_SESSION["hits"])) {
            $sunk = false;
            break;
        }
    }
    if (!$sunk) {
        $remainingShips++;
    }
}

echo json_encode([
    "result" => $result,
    "remainingShips" => $remainingShips,
    "gameOver" => $remainingShips === 0
]);

function placeShip($size) {
    global $gridSize;

    while (true) {
        $horizontal = rand(0, 1) === 1;
        $positions = [];

        if ($horizontal) {
            $row = rand(0, $gridSize - 1);
            $col = rand(0, $gridSize - $size);
            for ($i = 0; $i < $size; $i++) {
                $positions[] = "$row," . ($col + $i);
            }
        } else {
            $row = rand(0, $gridSize - $size);
            $col = rand(0, $gridSize - 1);
            for ($i = 0; $i < $size; $i++) {
                $positions[] = ($row + $i) . ",$col";
            }
        }

        // Prevent overlap
        $flatShips = array_merge(...$_SESSION["ships"]);
        if (count(array_intersect($positions, $flatShips)) === 0) {
            $_SESSION["ships"][] = $positions;
            break;
        }
    }
}
