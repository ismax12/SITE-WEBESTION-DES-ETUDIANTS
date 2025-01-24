<?php
session_start();

// Connexion à la base de données MySQL
$host = 'localhost';
$db = 'gestion_etudiants';
$user = 'root';
$pass = '';

// Message d'erreur
$error = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = trim(htmlspecialchars($_POST['nom_utilisateur']));
    $mot_de_passe = trim(htmlspecialchars($_POST['mot_de_passe']));

    if (empty($nom_utilisateur) || empty($mot_de_passe)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        try {
            // Vérifier les identifiants avec une requête sécurisée
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur = :nom_utilisateur AND mot_de_passe = :mot_de_passe");
            $stmt->execute([
                'nom_utilisateur' => $nom_utilisateur,
                'mot_de_passe' => $mot_de_passe, // Si hashé, comparez avec password_verify
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Authentification réussie
                $_SESSION['user'] = $user;

                // Redirection selon le rôle de l'utilisateur
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin.php");
                        break;
                    case 'professeur':
                        header("Location: pr.php");
                        break;
                    case 'etudiant':
                        header("Location: etudiant_page.php");
                        break;
                    default:
                        $error = "Rôle inconnu. Contactez l'administrateur.";
                        break;
                }
                exit();
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect";
            }
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #74ebd5;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #5fc2b7;
        }

        .links {
            text-align: center;
            margin-top: 10px;
        }

        .links a {
            color: #333;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Connexion</h1>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="nom_utilisateur">Nom d'utilisateur :</label>
            <input type="text" id="nom_utilisateur" name="nom_utilisateur" required
                   value="<?php echo isset($_POST['nom_utilisateur']) ? htmlspecialchars($_POST['nom_utilisateur']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <div class="form-group">
            <button type="submit">Se connecter</button>
        </div>
    </form>

    <div class="links">
        <a href="signup.php">Pas encore inscrit ? Créer un compte</a>
    </div>
</div>
</body>
</html>
