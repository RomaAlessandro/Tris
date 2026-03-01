let board = Array(9).fill("");
let turn = "X";
let over = false;
let scoreX = 0;
let scoreO = 0;
let p1Name = "";
let p2Name = "";
let mode = "pvp";

const combos = [
  [0,1,2],[3,4,5],[6,7,8],
  [0,3,6],[1,4,7],[2,5,8],
  [0,4,8],[2,4,6]
];

const boardEl = document.getElementById("board");
const turnEl = document.getElementById("turn");
const statusEl = document.getElementById("status");
const scoreEl = document.getElementById("score");
const vsEl = document.getElementById("vs");

function drawBoard() {
  boardEl.innerHTML = "";
  board.forEach((val, i) => {
    let c = document.createElement("div");
    c.className = "cell";
    c.innerHTML = val;
    c.onclick = () => play(i);
    boardEl.appendChild(c);
  });
}

function play(i) {
  if (over || board[i]) return;
  board[i] = turn;
  drawBoard();

  let winCombo = checkWin();
  if (winCombo) {
    winRound(winCombo);
  } else if (board.every(cell => cell !== "")) {
    tieRound();
  } else {
    switchTurn();
    if (mode === "cpu" && turn === "O") {
        setTimeout(cpuMove, 600);
    }
  }
}

function cpuMove() {
  if (over) return;
  // 1. Vincere
  for (let combo of combos) {
    let a = board[combo[0]], b = board[combo[1]], c = board[combo[2]];
    if (a === "O" && b === "O" && c === "") { play(combo[2]); return; }
    if (a === "O" && c === "O" && b === "") { play(combo[1]); return; }
    if (b === "O" && c === "O" && a === "") { play(combo[0]); return; }
  }
  // 2. Bloccare
  for (let combo of combos) {
    let a = board[combo[0]], b = board[combo[1]], c = board[combo[2]];
    if (a === "X" && b === "X" && c === "") { play(combo[2]); return; }
    if (a === "X" && c === "X" && b === "") { play(combo[1]); return; }
    if (b === "X" && c === "X" && a === "") { play(combo[0]); return; }
  }
  // 3. Casuale
  let emptyCells = board.map((val, idx) => val === "" ? idx : null).filter(val => val !== null);
  if (emptyCells.length > 0) {
    let randomIdx = emptyCells[Math.floor(Math.random() * emptyCells.length)];
    play(randomIdx);
  }
}

function checkWin() {
  for (let combo of combos) {
    if (board[combo[0]] && board[combo[0]] === board[combo[1]] && board[combo[0]] === board[combo[2]]) return combo;
  }
  return null;
}

function winRound(combo) {
  combo.forEach(idx => boardEl.children[idx].classList.add("win"));
  over = true;
  statusEl.textContent = turn + " ha vinto!";
  if (turn === "X") {
      scoreX++;
      fetch('update_score.php', { method: 'POST' });
  } else {
      scoreO++;
  }
  updateScore();
}

function tieRound() {
  over = true;
  statusEl.textContent = "Pareggio!";
}

function switchTurn() {
  turn = turn === "X" ? "O" : "X";
  if (turnEl) turnEl.textContent = turn;
}

function resetRound() {
  board = Array(9).fill("");
  over = false;
  turn = "X";
  drawBoard();
  statusEl.textContent = "Turno: X";
}

function updateScore() {
  scoreEl.innerHTML = `${p1Name} (X): ${scoreX} | ${p2Name} (O): ${scoreO}`;
}

// FIX: Rimosso il riferimento a "err" che bloccava tutto
document.getElementById("startBtn").onclick = () => {
  mode = document.getElementById("mode").value;
  p1Name = document.getElementById("p1").value;
  p2Name = document.getElementById("p2").value.trim() || (mode === "cpu" ? "CPU" : "O");

  document.getElementById("startScreen").classList.add("hidden");
  document.getElementById("actualGame").classList.remove("hidden");

  vsEl.textContent = `${p1Name} vs ${p2Name}`;
  updateScore();
  drawBoard();
};

document.getElementById("restartRound").onclick = resetRound;
document.getElementById("resetAll").onclick = () => location.reload();