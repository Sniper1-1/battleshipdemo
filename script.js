const grid = document.getElementById("grid");
const statusText = document.getElementById("status");

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
    if (cell.classList.contains("hit") || cell.classList.contains("miss")) {
        return;
    }

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

        if (data.gameOver) {
            setTimeout(() => {
                alert("All ships destroyed! Restarting game...");
                restartGame();
            }, 300);
        }
    });
}

function restartGame() {
    fetch("game.php?reset=1")
        .then(() => {
            document.querySelectorAll(".cell").forEach(cell => {
                cell.classList.remove("hit", "miss");
            });
            statusText.textContent = "Remaining ships: 3";
        });
}
window.onload = () => {
    fetch("game.php?reset=1");
};
