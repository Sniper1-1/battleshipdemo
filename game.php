<?php
session_start();
header("Content-Type: application/json");

$gridSize = 10;
$shipSizes = [2, 3, 5];

// Initialize game once
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

if (in_array($key, $_SESSION["ships"])) {
    $_SESSION["hits"][] = $key;
    echo json_encode(["result" => "hit"]);
} else {
    echo json_encode(["result" => "miss"]);
}

function placeShip($size) {
    global $gridSize;

    while (true) {
        $horizontal = rand(0, 1) === 1;

        if ($horizontal) {
            $row = rand(0, $gridSize - 1);
            $col = rand(0, $gridSize - $size);
        } else {
            $row = rand(0, $gridSize - $size);
            $col = rand(0, $gridSize - 1);
        }

        $positions = [];

        for ($i = 0; $i < $size; $i++) {
            $r = $horizontal ? $row : $row + $i;
            $c = $horizontal ? $col + $i : $col;
            $positions[] = "$r,$c";
        }

        // Check overlap
        if (count(array_intersect($positions, $_SESSION["ships"])) === 0) {
            $_SESSION["ships"] = array_merge($_SESSION["ships"], $positions);
            break;
        }
    }
}
