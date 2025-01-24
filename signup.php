<?php
session_start();

// Connexion à la base de données MySQL
$host = 'localhost';
$db = 'gestion_etudiants';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Initialiser les variables
$nom_utilisateur = $mot_de_passe = $confirm_password = $role = "";
$nomErr = $passwordErr = $confirmErr = $roleErr = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validation du nom d'utilisateur
    if (empty($_POST["nom_utilisateur"])) {
        $nomErr = "Le nom d'utilisateur est requis";
    } else {
        $nom_utilisateur = trim(htmlspecialchars($_POST["nom_utilisateur"]));
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $nom_utilisateur)) {
            $nomErr = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores";
        }
    }

    // Validation du mot de passe
    if (empty($_POST["mot_de_passe"])) {
        $passwordErr = "Le mot de passe est requis";
    } else {
        $mot_de_passe = trim(htmlspecialchars($_POST["mot_de_passe"]));
        if (strlen($mot_de_passe) < 6) {
            $passwordErr = "Le mot de passe doit contenir au moins 6 caractères";
        }
    }

    // Validation de la confirmation du mot de passe
    if (empty($_POST["confirm_password"])) {
        $confirmErr = "Veuillez confirmer votre mot de passe";
    } else {
        $confirm_password = trim(htmlspecialchars($_POST["confirm_password"]));
        if ($confirm_password !== $mot_de_passe) {
            $confirmErr = "Les mots de passe ne correspondent pas";
        }
    }

    // Validation du rôle
    if (empty($_POST["role"])) {
        $roleErr = "Veuillez sélectionner votre rôle";
    } else {
        $role = trim(htmlspecialchars($_POST["role"]));
        if (!in_array($role, ['etudiant', 'professeur'])) {
            $roleErr = "Rôle invalide";
        }
    }

    // Si pas d'erreurs, procéder à l'inscription
    if (empty($nomErr) && empty($passwordErr) && empty($confirmErr) && empty($roleErr)) {
        try {
            // Vérifier si le nom d'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE nom_utilisateur = ?");
            $stmt->execute([$nom_utilisateur]);

            if ($stmt->rowCount() > 0) {
                $nomErr = "Ce nom d'utilisateur existe déjà";
            } else {
                // Insérer le nouvel utilisateur
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, role, date_creation) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$nom_utilisateur, $mot_de_passe, $role]);

                // Rediriger vers la page de connexion
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px 30px;
            width: 100%;
            max-width: 400px;
        }

        h1 {
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form .form-group {
            margin-bottom: 15px;
        }

        form label {
            display: block;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        form input[type="text"]:focus,
        form input[type="password"]:focus {
            border-color: #74ebd5;
            outline: none;
        }

        .role-selector {
            display: flex;
            justify-content: space-between;
        }

        .role-option {
            display: flex;
            align-items: center;
        }

        .role-option input[type="radio"] {
            margin-right: 5px;
        }

        .error {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        form input[type="submit"] {
            background-color: #acb6e5;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
        }

        form input[type="submit"]:hover {
            background-color: #74ebd5;
        }

        p {
            margin-top: 15px;
            text-align: center;
            font-size: 0.9rem;
        }

        p a {
            color: #74ebd5;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
<div class="container">
    <h1>Inscription</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="nom_utilisateur">Nom d'utilisateur :</label>
            <input type="text" name="nom_utilisateur" id="nom_utilisateur" value="<?php echo $nom_utilisateur; ?>">
            <span class="error"><?php echo $nomErr; ?></span>
        </div>

        <div class="form-group">
            <label>Je suis :</label>
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" name="role" id="etudiant" value="etudiant" <?php if($role === 'etudiant') echo 'checked'; ?>>
                    <label for="etudiant">Étudiant</label>
                </div>
                <div class="role-option">
                    <input type="radio" name="role" id="professeur" value="professeur" <?php if($role === 'professeur') echo 'checked'; ?>>
                    <label for="professeur">Professeur</label>
                </div>
            </div>
            <span class="error"><?php echo $roleErr; ?></span>
        </div>

        <div class="form-group">
            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe">
            <span class="error"><?php echo $passwordErr; ?></span>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe :</label>
            <input type="password" name="confirm_password" id="confirm_password">
            <span class="error"><?php echo $confirmErr; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" value="S'inscrire">
        </div>
    </form>

    <p style="text-align: center">
        <a href="login.php">Déjà inscrit ? Se connecter</a>
    </p>
</div>
</body>
</html>