<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Inclure le fichier de traduction approprié
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr');
$translations = include("lang/$lang.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Nom = $_POST['Nom'];
    $Prenom = $_POST['Prénom'];
    $email = $_POST['e-mail'];
    $password = $_POST['MotDePasse'];
    $confirmPassword = $_POST['Confirmer'];
    $userType = 'élève';

    // Validation des champs
    if (empty($Nom)) {
        $errors['Nom'] = $translations['required_field_message'];
    }

    if (empty($Prenom)) {
        $errors['Prénom'] = $translations['required_field_message'];
    }

    if (empty($email)) {
        $errors['e-mail'] = $translations['required_field_message'];
    }

    if (empty($password)) {
        $errors['MotDePasse'] = $translations['required_field_message'];
    }

    if (empty($confirmPassword)) {
        $errors['Confirmer'] = $translations['required_field_message'];
    }

    if ($password !== $confirmPassword) {
        $errors['Confirmer'] = $translations['password_mismatch_message'];
    }

    if (empty($errors)) {
        try {
            $servername = "localhost";
            $username = "root";
            $password_db = "";
            $dbname = "G4D";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_db);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Utilisation de password_hash pour hacher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO `utilisateur` (`Nom`, `Prénom`, `adresse mail`, `mot de passe`, `type`) 
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$Nom, $Prenom, $email, $hashedPassword, $userType]);

            echo $translations['registration_successful'];
            header("Location: accueil.php");
            exit;
        } catch(PDOException $e) {
            echo "Erreur: " . $e->getMessage();
        }

        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <title>Page d'inscription</title>
    <link rel="stylesheet" href="css/styleCO.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel="stylesheet">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                const password = document.querySelector('input[name="MotDePasse"]').value;
                const confirmPassword = document.querySelector('input[name="Confirmer"]').value;

                if (password !== confirmPassword) {
                    alert("<?php echo $translations['password_mismatch_alert']; ?>");
                    event.preventDefault(); // Empêcher la soumission du formulaire
                }
            });
        });
    </script>
    <style>
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="wrapper">
    <form method="POST" action="inscription.php">
            <h1><?php echo $translations['inscription_heading']; ?></h1>
            
            <?php if (isset($errors['Nom'])) : ?>
                <div class="error-message"><?php echo $errors['Nom']; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" placeholder="<?php echo $translations['nom_placeholder']; ?>" name="Nom" value="<?php echo $Nom ?? ''; ?>">
                <i class='bx bxs-user'></i>
            </div>

            <?php if (isset($errors['Prénom'])) : ?>
                <div class="error-message"><?php echo $errors['Prénom']; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" placeholder="<?php echo $translations['prenom_placeholder']; ?>" name="Prénom" value="<?php echo $Prenom ?? ''; ?>">
                <i class='bx bxs-user'></i>
            </div>

            <?php if (isset($errors['e-mail'])) : ?>
                <div class="error-message"><?php echo $errors['e-mail']; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" placeholder="<?php echo $translations['email_placeholder']; ?>" name="e-mail" value="<?php echo $email ?? ''; ?>">
                <i class='bx bxs-envelope'></i>
            </div>

            <?php if (isset($errors['MotDePasse'])) : ?>
                <div class="error-message"><?php echo $errors['MotDePasse']; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="password" placeholder="<?php echo $translations['password_placeholder']; ?>" name="MotDePasse" value="<?php echo $password ?? ''; ?>">
                <i class='bx bxs-lock-alt'></i>
            </div>

            <?php if (isset($errors['Confirmer'])) : ?>
                <div class="error-message"><?php echo $errors['Confirmer']; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="password" placeholder="<?php echo $translations['confirm_password_placeholder']; ?>" name="Confirmer" value="<?php echo $confirmPassword ?? ''; ?>">
                <i class='bx bxs-lock-alt'></i>
            </div>
            
            <button name='inscription' type="submit" class="btnco"><?php echo $translations['register_button']; ?></button>
            
            <div class="register-link">
                <p><a href="connexion.php" class="inscr"><?php echo $translations['already_have_account']; ?></a></p>
            </div>
        </form>
    </div>
</body>
</html>