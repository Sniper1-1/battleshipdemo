const grid = document.getElementById("grid");

for (let row = 0; row < 10; row++) {
    for (let col = 0; col < 10; col++) {
        const cell = document.createElement("div");
        cell.classList.add("cell");
        cell.dataset.row = row;
        cell.dataset.col = col;

        cell.addEventListener("click", () => makeMove(cell));

        grid.appendChild(cell);
    }
}

function makeMove(cell) {
    // Prevent clicking same cell twice
    if (cell.classList.contains("hit") || cell.classList.contains("miss")) {
        return;
    }

    const row = cell.dataset.row;
    const col = cell.dataset.col;

    fetch("game.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `row=${row}&col=${col}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === "hit") {
            cell.classList.add("hit");
        } else {
            cell.classList.add("miss");
        }
    });
}
