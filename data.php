<?php
global $etablissement;
$servername = "localhost"; // ou votre serveur de base de données
$username = "root";        // votre nom d'utilisateur MySQL
$password = "";            // votre mot de passe MySQL (par défaut vide sur XAMPP)
$dbname = "gestion_etudiants";  // nom de la base de données

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];

    // Préparer la requête d'insertion
    $sql = "INSERT INTO etudiant (nom, prenom , etablissement) 
            VALUES ('$nom', '$prenom' ,'$etablissement')";

    // Exécuter la requête
    if ($conn->query($sql) === TRUE) {
        echo "Les données ont été enregistrées avec succès.";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>