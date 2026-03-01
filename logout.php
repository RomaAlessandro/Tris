<!-- Apprezzo l'ordine di mettere queste 6 linee a parte non e' sbagliato e anzi
 in contesti dove il logout e' piu' complicato si tiene da parte
 ma nel nostro caso aumenti solo la complessita' del sito senza ottenere alcun beneficio -->
<?php
session_start();
session_destroy(); // Distrugge la sessione attuale
header("Location:index.php"); // Ti riporta al login
exit();
?>
