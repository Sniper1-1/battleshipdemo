<?php
header("Content-Type: application/json");

$gridSize = 10;
$shipSizes = [2, 3, 5];
$gameFile = "game.json";

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

function loadGame($file) {
    if (!file_exists($file)) {
        return null;
    }
    $data = file_get_contents($file);
    return json_decode($data, true);
}
if (!isset($game["hits"])) {
    $game["hits"] = [];
}

if (!isset($game["misses"])) {
    $game["misses"] = [];
}

if (!isset($game["shots"])) {
    $game["shots"] = 0;
}


function saveGame($file, $data) {
    file_put_contents($file, json_encode($data));
}

function createNewGame($gridSize, $shipSizes) {
    $game = [
        "ships" => [],
        "hits" => [],
        "shots" => 0,
        "misses" => []

    ];

    foreach ($shipSizes as $size) {
        placeShip($game, $size, $gridSize);
    }

    return $game;
}

function placeShip(&$game, $size, $gridSize) {
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

        $flatShips = [];
        foreach ($game["ships"] as $ship) {
            $flatShips = array_merge($flatShips, $ship);
        }

        if (count(array_intersect($positions, $flatShips)) === 0) {
            $game["ships"][] = $positions;
            break;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Reset Game
|--------------------------------------------------------------------------
*/

if (isset($_GET["reset"])) {
    $newGame = createNewGame($gridSize, $shipSizes);
    saveGame($gameFile, $newGame);
    echo json_encode(["reset" => true]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Load or Create Game
|--------------------------------------------------------------------------
*/

$game = loadGame($gameFile);

if (!$game) {
    $game = createNewGame($gridSize, $shipSizes);
    saveGame($gameFile, $game);
}
// If GET request, return full game state
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $remainingShips = 0;
    foreach ($game["ships"] as $ship) {
        $sunk = true;
        foreach ($ship as $cell) {
            if (!in_array($cell, $game["hits"])) {
                $sunk = false;
                break;
            }
        }
        if (!$sunk) {
            $remainingShips++;
        }
    }

    echo json_encode([
        "shots" => $game["shots"],
        "hits" => $game["hits"],
        "misses" => $game["misses"],
        "remainingShips" => $remainingShips,
        "gameOver" => $remainingShips === 0
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Process Move
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $row = intval($_POST["row"]);
    $col = intval($_POST["col"]);
    $key = "$row,$col";

    $result = "miss";
    $alreadyShot = in_array($key, $game["hits"]) || in_array($key, $game["misses"]);

    // Only count new shots
    if (!$alreadyShot) {
        $game["shots"]++;

        foreach ($game["ships"] as $ship) {
            if (in_array($key, $ship)) {
                $game["hits"][] = $key;
                $result = "hit";
                break;
            }
        }

        if ($result === "miss") {
            $game["misses"][] = $key;
        }
    }

    // Count remaining ships
    $remainingShips = 0;
    foreach ($game["ships"] as $ship) {
        $sunk = true;
        foreach ($ship as $cell) {
            if (!in_array($cell, $game["hits"])) {
                $sunk = false;
                break;
            }
        }
        if (!$sunk) {
            $remainingShips++;
        }
    }

    saveGame($gameFile, $game);

    echo json_encode([
        "result" => $result,
        "remainingShips" => $remainingShips,
        "shots" => $game["shots"],
        "hits" => $game["hits"],
        "misses" => $game["misses"],
        "gameOver" => $remainingShips === 0
    ]);

    exit;
}

