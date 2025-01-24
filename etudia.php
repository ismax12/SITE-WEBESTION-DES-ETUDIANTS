<?php
// Connexion à la base de données MySQL
$host = 'localhost';
$db = 'gestion_etudiants';
$user = 'root';
$pass = '';

// Créer une connexion PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Message de statut
$message = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Récupérer les données soumises
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $etablissement = htmlspecialchars($_POST['niv']);
        $date_naissance = htmlspecialchars($_POST['date_naissance']);

        // Préparer la requête SQL
        $sql = "INSERT INTO etudiant (nom, prenom, etablissement, date_de_naissance, date_creation) 
                VALUES (:nom, :prenom, :etablissement, :date_naissance, NOW())";
        $stmt = $pdo->prepare($sql);

        // Exécuter la requête avec les paramètres
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':etablissement' => $etablissement,
            ':date_naissance' => $date_naissance
        ]);

        // Rediriger vers la page d'affichage après l'ajout
        header('Location: etaff.php');
        exit;
    } catch (PDOException $e) {
        $message = "Erreur lors de l'ajout : " . $e->getMessage();
    }
}

// Récupérer la dernière valeur sélectionnée pour l'établissement
$selectedNiv = isset($_POST['niv']) ? $_POST['niv'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un étudiant</title>
    <style>

    </style>
</head>
<link rel="stylesheet" href="style.css">

<body>

<div class="container">
    <h1>Ajouter un étudiant</h1>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Formulaire pour ajouter un étudiant -->
    <form method="POST">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>

        <div class="form-group">
            <label for="date_naissance">Date de naissance :</label>
            <input type="date" id="date_naissance" name="date_naissance" required>
        </div>

        <div class="form-group">
            <label for="niv">Choisissez une option :</label>
            <select name="niv" id="niv">
                <option value="EIDIA" <?php if ($selectedNiv == "EIDIA") echo "selected"; ?>>EIDIA</option>
                <option value="EMADU" <?php if ($selectedNiv == "EMADU") echo "selected"; ?>>EMADU</option>
                <option value="EBS" <?php if ($selectedNiv == "EBS") echo "selected"; ?>>EBS</option>
                <option value="EPS" <?php if ($selectedNiv == "EPS") echo "selected"; ?>>EPS</option>
                <option value="EPS" <?php if ($selectedNiv == "MED") echo "selected"; ?>>MED</option>
            </select>
        </div>

        <button type="submit">Ajouter l'étudiant</button>
    </form>

    <p><a href="etaff.php">Voir les étudiants ajoutés</a></p>
    <div class="card">
        <a href="pr.php" class="logout-btn">Retour à l'accueil</a>
    </div>
</div>
</body>
</html>