<?php
include "connection.php";
header('Content-Type: application/json');

if (isset($_SESSION['username']) && isset($_POST['result'])) {
	$user = $_SESSION['username'];
	$result = $_POST['result']; // Riceve 'vittoria', 'sconfitta' o 'pareggio' dallo script.js

	// 1. Aggiorna il database in base al risultato ricevuto
	if ($result === 'vittoria') {
		$conn->query("UPDATE giocatori SET vittorie = vittorie + 1 WHERE username = '$user'");
	} elseif ($result === 'sconfitta') {
	  
		$conn->query("UPDATE giocatori SET sconfitte = sconfitte + 1 WHERE username = '$user'");
	} elseif ($result === 'pareggio') {
		
		$conn->query("UPDATE giocatori SET pareggi = pareggi + 1 WHERE username = '$user'");
	}

	// 2. Recupera i dati aggiornati dal database
	$res = $conn->query("SELECT vittorie, sconfitte, pareggi FROM giocatori WHERE username = '$user'");
	$data = $res->fetch_assoc();

	// 3. Risponde al JavaScript con i nuovi totali in formato JSON
	echo json_encode([
		'vittorie' => $data['vittorie'],
		'sconfitte' => $data['sconfitte'] ?? 0,
		'pareggi' => $data['pareggi'] ?? 0
	]);
	exit;
}
?>