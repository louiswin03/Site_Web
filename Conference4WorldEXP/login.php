<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['e-mail'];
    $password = $_POST['MotDePasse'];

    try {
        $servername = "localhost";
        $username = "root";
        $password_db = "";
        $dbname = "G4D";

        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_db);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT `id utilisateur`, `Nom`, `Prénom`, `type`, `mot de passe` FROM `utilisateur` WHERE `adresse mail` = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row && password_verify($password, $row['mot de passe'])) {
            // Stocker les informations de session
            $_SESSION['id utilisateur'] = $row['id utilisateur'];
            $_SESSION['Nom'] = $row['Nom'];
            $_SESSION['Prénom'] = $row['Prénom'];
            $_SESSION['type'] = $row['type'];

            echo "Connexion réussie!";
           
            // Rediriger vers la page d'accueil après la connexion réussie
            header("Location: accueil.php");
            exit(); // Assurez-vous de terminer l'exécution après la redirection
        } else {
            
            header("Location: connexion.php?error=invalid_credentials");
            
            
            exit();
        }

    } catch(PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }

    $conn = null;
}
?>