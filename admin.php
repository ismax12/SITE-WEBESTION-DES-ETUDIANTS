<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur MySQL
$password = ""; // Remplacez par votre mot de passe MySQL
$dbname = "gestion_etudiants";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ajouter un utilisateur
    if (isset($_POST['ajouter'])) {
        $nom_utilisateur = $_POST['nom_utilisateur'];
        $mot_de_passe = $_POST['mot_de_passe'];
        $role = $_POST['role'];
        $stmt = $conn->prepare("INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, role, date_creation) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$nom_utilisateur, $mot_de_passe, $role]);
        header("Location: " . $_SERVER['PHP_SELF']);
    }

    // Modifier un utilisateur
    if (isset($_POST['modifier'])) {
        $id = $_POST['id'];
        $nom_utilisateur = $_POST['nom_utilisateur'];
        $mot_de_passe = $_POST['mot_de_passe'];
        $role = $_POST['role'];
        $stmt = $conn->prepare("UPDATE utilisateurs SET nom_utilisateur = ?, mot_de_passe = ?, role = ? WHERE id = ?");
        $stmt->execute([$nom_utilisateur, $mot_de_passe, $role, $id]);
        header("Location: " . $_SERVER['PHP_SELF']);
    }

    // Supprimer un utilisateur
    if (isset($_GET['supprimer'])) {
        $id = $_GET['supprimer'];
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: " . $_SERVER['PHP_SELF']);
    }

    // Récupérer tous les utilisateurs
    $stmt = $conn->query("SELECT * FROM utilisateurs");
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            min-height: 100vh;
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
            max-width: 900px;
            position: relative;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-supprimer {
            background-color: #f44336;
            color: white;
        }

        .btn-supprimer:hover {
            background-color: #d32f2f;
        }

        form {
            margin: 20px 0;
        }

        input[type="text"],
        select {
            padding: 10px;
            width: calc(33.33% - 20px);
            margin: 10px 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button[type="submit"] {
            width: calc(33.33% - 20px);
            margin: 10px 5px;
            background-color: #74ebd5;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #4db8cc;
        }

        .btn-deconnexion {
            width: calc(33.33% - 20px);
            margin: 10px 5px;
            background-color: red;
            color: white;
        }

        .btn-deconnexion:hover {
            background-color: #d32f2f;
        }

    </style>
</head>
<body>
<div class="container">
    <h1>Gestion des utilisateurs</h1>

    <!-- Tableau des utilisateurs -->
    <table>
        <thead>
        <tr>
            <th>Nom d'utilisateur</th>
            <th>Mot de passe</th>
            <th>Rôle</th>
            <th>Date de création</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($utilisateurs)): ?>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <tr>
                    <td><?= htmlspecialchars($utilisateur['nom_utilisateur']) ?></td>
                    <td><?= htmlspecialchars($utilisateur['mot_de_passe']) ?></td>
                    <td><?= htmlspecialchars($utilisateur['role']) ?></td>
                    <td><?= htmlspecialchars($utilisateur['date_creation']) ?></td>
                    <td>
                        <a href="?supprimer=<?= $utilisateur['id'] ?>"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                            <button class="btn-supprimer">Supprimer</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Aucun utilisateur trouvé.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>


    <h2>Ajouter un utilisateur</h2>
    <form action="" method="post">
        <input type="text" name="nom_utilisateur" placeholder="Nom d'utilisateur" required>
        <input type="text" name="mot_de_passe" placeholder="Mot de passe" required>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="professeur">Professeur</option>
            <option value="etudiant">Étudiant</option>
        </select>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>


    <form action="logout.php" method="post">
        <button type="submit" class="btn-deconnexion">Se déconnecter</button>
    </form>
</div>
</body>
</html>