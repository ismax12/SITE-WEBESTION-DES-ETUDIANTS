<?php
// Connexion à la base de données
require 'data.php';

// Vérification que la connexion à la base de données est établie
if (!isset($pdo)) {
    die("Erreur : Connexion à la base de données non établie.");
}

// Insertion de l'établissement dans la base de données si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && !empty($_POST['nom'])) {
    $nomEtablissement = trim($_POST['nom']);

    try {
        // Préparation de la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO etablissement (nom) VALUES (:nom)");
        $stmt->bindParam(':nom', $nomEtablissement);
        $stmt->execute();
        $message = "L'établissement a été ajouté avec succès.";
    } catch (PDOException $e) {
        $message = "Erreur lors de l'ajout de l'établissement : " . $e->getMessage();
    }
}

// Récupération des établissements existants
$etablissement = [];
try {
    $stmt = $pdo->query("SELECT nom FROM etablissement");
    $etablissement = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie des établissements</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fafafa;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #74ebd5;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
        }
        button:hover {
            background-color: #4db8cc;
        }
        .message {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Ajouter ou sélectionner un établissement</h1>
    <form method="post" action="">
        <!-- Liste déroulante pour sélectionner un établissement existant -->
        <select name="nom">
            <option value="">Sélectionner un établissement</option>
            <?php foreach ($etablissement as $etablissement): ?>
                <option value="<?= htmlspecialchars($etablissement['nom']); ?>">
                    <?= htmlspecialchars($etablissement['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Champ de texte pour saisir un nouvel établissement -->
        <input type="text" name="nom" placeholder="Nom de l'établissement" required>

        <!-- Bouton pour soumettre le formulaire -->
        <button type="submit">Valider</button>
    </form>

    <!-- Affichage du message de confirmation ou d'erreur -->
    <?php if (isset($message)): ?>
        <p class="message"> <?= htmlspecialchars($message); ?> </p>
    <?php endif; ?>
</div>
</body>
</html>
