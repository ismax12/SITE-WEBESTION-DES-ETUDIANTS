<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Button Page</title>
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
        .button-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            text-align: center;
        }
        .button-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #74ebd5;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            margin: 10px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #4db8cc;
        }
        .btn-deconnexion {
            background-color: #e74c3c;
            color: white;
            font-size: 14px;
            padding: 8px;
            width: 80%; /* Plus petit que les autres boutons */
            margin: 15px auto 0 auto; /* Centré avec un écart plus grand */
        }
        .btn-deconnexion:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
<div class="button-container">
    <button class="btn" onclick="location.href='etudia.php'">Ajouter un Etudiant</button>
    <button class="btn" onclick="location.href='notabs.php'">Ajouter Les Notes et Les absences</button>
    <button class="btn" onclick="location.href='etaff.php'">Afficher Les Etudiants</button>
    <button class="btn" onclick="location.href='affichprof.php'">Afficher Les Notes et Les Absences</button>
    <button class="btn-deconnexion" onclick="location.href='login.php'">Se Déconnecter</button>
</div>
</body>
</html>
