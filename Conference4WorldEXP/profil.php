<?php
session_start();

// Vérifier si la langue est définie dans la session
if (isset($_SESSION['lang'])) {
    // Utiliser la langue stockée dans la session
    $lang = $_SESSION['lang'];
} else {
    // Par défaut, utiliser la langue française
    $lang = 'fr';
}

// Inclure les fichiers de traduction correspondants à la langue choisie
$translations = include("lang/$lang.php");

$conn = new mysqli("localhost", "root", "", "G4D");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupération des informations de l'utilisateur
$user_info = array(); // Initialisation des informations de l'utilisateur

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['id utilisateur'])) {
    $user_id = $_SESSION['id utilisateur'];

    // Requête pour récupérer les informations de l'utilisateur
    $sql_user_info = "SELECT * FROM utilisateur WHERE `id utilisateur` = $user_id";
    $result_user_info = $conn->query($sql_user_info);

    // Vérifier s'il y a des résultats
    if ($result_user_info->num_rows > 0) {
        $user_info = $result_user_info->fetch_assoc();
    }
}

?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/styleCO.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title><?php echo $translations['profile_title']; ?></title>
</head>
<body>
    <div class="wrapper">
        <a href="accueil.php" class="back-link"><i class="fa-solid fa-arrow-left" style="color:#000000; font-size: 24px;"></i></a>
        <img src="./images/user1.png" class="user">
        <div class="userinfo">
            <?php
            if (isset($user_info['Nom'])) {
                echo '<h2>' . $user_info['Prénom'] . ' ' . $user_info['Nom'] . '</h2>';
            } else {
                echo '<a href="connexion.php">' . $translations['login_link'] . '</a>';
            }
            ?>
            <?php
            // Vérifier si l'utilisateur est un administrateur
            if (isset($user_info['type']) && $user_info['type'] == 'admin') {
                echo '<a href="gerer_utilisateurs.php" class="btnco">' . $translations['gerer_uti'] . '</a>';
            }
            ?>
            <a href="changementmdp.html" class="btnco"><?php echo $translations['change_password']; ?></a>
            <a href="deconnexion.php" class="btnco"><?php echo $translations['logout']; ?></a>
        </div>
    </div>
</body>
</html>