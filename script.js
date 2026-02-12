const grid = document.getElementById("grid");
const statusText = document.getElementById("status");
const shotsText = document.getElementById("shots");
const resetBtn = document.getElementById("resetBtn");
const winMessage = document.getElementById("winMessage");

let gameOver = false;

// Create board
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

function loadGameState() {
    fetch("game.php")
        .then(res => res.json())
        .then(data => {

            shotsText.textContent = `Shots fired: ${data.shots}`;
            statusText.textContent = `Remaining ships: ${data.remainingShips}`;

            document.querySelectorAll(".cell").forEach(cell => {
                cell.classList.remove("hit", "miss");
            });

            data.hits.forEach(hit => {
                const [row, col] = hit.split(",");
                const cell = document.querySelector(
                    `.cell[data-row='${row}'][data-col='${col}']`
                );
                if (cell) cell.classList.add("hit");
            });

            data.misses.forEach(miss => {
                const [row, col] = miss.split(",");
                const cell = document.querySelector(
                    `.cell[data-row='${row}'][data-col='${col}']`
                );
                if (cell) cell.classList.add("miss");
            });

            if (data.gameOver) {
                gameOver = true;
                winMessage.style.display = "flex";
            } else {
                winMessage.style.display = "none";
            }
        });
}

function makeMove(cell) {
    if (cell.classList.contains("hit") || 
        cell.classList.contains("miss") || 
        gameOver) return;

    fetch("game.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `row=${cell.dataset.row}&col=${cell.dataset.col}`
    })
    .then(res => res.json())
    .then(data => {

        if (data.result === "hit") {
            cell.classList.add("hit");
        } else {
            cell.classList.add("miss");
        }

        statusText.textContent = `Remaining ships: ${data.remainingShips}`;
        shotsText.textContent = `Shots fired: ${data.shots}`;

        if (data.gameOver) {
            gameOver = true;
            winMessage.style.display = "flex";
        }
    });
}

resetBtn.addEventListener("click", restartGame);

function restartGame() {
    fetch("game.php?reset=1")
        .then(res => res.json())
        .then(() => {
            gameOver = false;
            winMessage.style.display = "none";
            loadGameState();
        });
}

loadGameState();
