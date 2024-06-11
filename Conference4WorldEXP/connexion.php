<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Inclure le fichier de traduction approprié
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr');
$translations = include("lang/$lang.php");

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
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }

    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <title><?php echo $translations['connexion_title']; ?></title>
    <link rel="stylesheet" href="css/styleCO.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <form method="POST" action="connexion.php">
            <a href="accueil.php" class="back-link"><i class='bx bx-arrow-back' style='color:#000000; font-size: 24px;'></i></a>
            <h1><?php echo $translations['connexion_heading']; ?></h1>
            
            <div class="input-box">
                <input type="text" placeholder="<?php echo $translations['email_placeholder']; ?>" name="e-mail">
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="<?php echo $translations['password_placeholder']; ?>" name="MotDePasse">
                <i class='bx bxs-lock-alt' ></i>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox"><?php echo $translations['remember_me']; ?></label>
                <a href="#"><?php echo $translations['forgot_password']; ?></a>
            </div>
            <button name='submit' type="submit" class="btnco"><?php echo $translations['login_button']; ?></button>
            <div class="register-link">
                <p><a href="inscription.php" class="inscr"><?php echo $translations['register_link']; ?></a></p>
            </div>
        </form>
        
        <!-- Déplacez cette partie en dehors du formulaire -->
        <div class="register-link">
            <?php
            // Affiche le message d'erreur si présent dans l'URL
            if(isset($_GET['error']) && $_GET['error'] == 'invalid_credentials') {
                echo '<p style="color: red;">'.$translations['error_message'].'</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>