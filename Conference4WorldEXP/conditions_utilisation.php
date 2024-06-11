<?php
session_start();
$conn = new mysqli("localhost", "root", "", "G4D");

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Vérifier la langue choisie (par exemple, à partir de paramètres de l'URL ou d'une session)
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr');

// Charger le fichier de traduction approprié
$translations = include("lang/$lang.php");

$sql_cgu = "SELECT titre, contenu, titre_en, contenu_en, `id_CGU_mentions_legales` FROM `cgu_mentions_legales` WHERE type = 'CGU'";
$result_cgu = $conn->query($sql_cgu);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta charset="UTF-8">
    <title>Conditions Générales d'Utilisation (CGU)</title>
    <style>
        body {
            color: #333;
            background-color: #D9D9D9;
            margin: 0;
            padding: 20px;
            position: relative; 
        }

        h1, h2 {
            color: #FF7004;
        }

        p {
            margin-bottom: 15px;
        }

        h2 {
            margin-top: 30px;
        } 
    </style>
</head>
<body>
    <input type="checkbox" id="check">
    <header>
        <a href="accueil.php"><img src="images/LogoC4Wsfond.png" class="logo"></a>
        <div class="navigation">
        <a class="navactif" href="accueil.php"><?php echo $translations['accueil_home']; ?></a>
            <a href="conferences.php"><?php echo $translations['accueil_conferences']; ?></a>
            <a href="calendrier.php"><?php echo $translations['calendrier']; ?></a>
            <a href="FAQ.php"><?php echo $translations['accueil_FAQ']; ?></a>
            <a href="forum.php">Forum</a>
            <?php
            if (isset($_SESSION['Nom'])) {
                echo '<a href="profil.php"><i class="fa-regular fa-user"></i> ' . $_SESSION['Nom'] . '</a>';
            } else {
                echo '<a href="connexion.php" class="connexion-header"><i class="fa-regular fa-user"></i>' . $translations['accueil_login'] . '</a>';
            }
            ?>
        </div>
        <label for="check">
            <i class="fas fa-bars menu-btn"></i>
            <i class="fas fa-times close-btn"></i>
        </label>
    </header>
    <main>
        <div class="CGU-container">
        <h1><?php echo $translations['terms_of_use_header']; ?></h1>

            <p><?php echo $translations['welcome_message']; ?></p>

            <?php
            if ($result_cgu->num_rows > 0) {
                while ($row_cgu = $result_cgu->fetch_assoc()) {
                    // Utiliser la variable $lang pour déterminer la langue appropriée
                    $titre = ($lang == 'en') ? $row_cgu['titre_en'] : $row_cgu['titre'];
                    $contenu = ($lang == 'en') ? $row_cgu['contenu_en'] : $row_cgu['contenu'];

                    echo "<h2>" . $titre . "</h2>";
                    echo "<p>" . $contenu . "</p>";
                    //Formulaire de suppression
                    
                    if (isset($_SESSION['id utilisateur']) && $_SESSION['type'] === "admin") {
                        echo "
                            <form method='post' class='delete-form'>
                                <input type='hidden' name='delete_id' value='" . $row_cgu['id_CGU_mentions_legales'] . "'>
                                <button type='submit' name='delete_submit' class='delete-btn'><i class='fas fa-trash'></i></button>
                            </form>
                        ";
                    }
                }
            } else {
                echo "Aucun contenu pour les mentions légales n'a été trouvé.";
            }

            if (isset($_SESSION['id utilisateur'])) {
                $user_id = $_SESSION['id utilisateur'];
                $sql = "SELECT type FROM utilisateur WHERE `id utilisateur` = $user_id";
                $result = $conn->query($sql);

                //Fonctionnalité ajout
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row["type"] === "admin") {
                        if (isset($_POST['submit']) && isset($_POST['new_title']) && isset($_POST['new_content'])) {
                            $new_title = $conn->real_escape_string($_POST['new_title']);
                            $new_content = $conn->real_escape_string($_POST['new_content']);

                            $insertQuery = "INSERT INTO `CGU_mentions_legales` (titre, contenu, type) VALUES ('$new_title', '$new_content', 'CGU')";
                            if ($conn->query($insertQuery) === TRUE) {
                                header("Location: conditions_utilisation.php");
                                exit();
                            } else {
                                echo "Erreur lors de l'ajout du titre et contenu : " . $conn->error;
                            }
                        }

                        echo "
                            <form method='post' class='CGU-form'>
                                <h2>Bonjour " . $_SESSION['Prénom'] . ", ajoutez ici vos Conditions Générales d'Utilisation: </h2>
                                <input class='CGU-titre' type='text' name='new_title' placeholder='Nouveau titre' required><br>
                                <textarea class='CGU-text' name='new_content' placeholder='Nouveau contenu' required></textarea><br>
                                <button type='submit' name='submit'>Ajouter</button>
                            </form>
                        ";
                    }
                    } else {
                        echo $translations['utilisateur_non_trouve'];
                    }
                } else {
                    echo $translations['conditions_mises_a_jour'];
                }
                ?>
        </div>
    </main>
    <footer class="footer">
        <a href="accueil.php"><img src="images/LogoC4Wsfond.png" alt="logo"></a>
        <div>
            <p class="titresection"><?php echo $translations['accueil_data_protection']; ?></p>
            <a href="mentions_legales.php"><?php echo $translations['accueil_legal_mentions']; ?></a>
            <br> 
            <a href="conditions_utilisation.php"><?php echo $translations['accueil_general_conditions']; ?></a>
        </div>
        <div>
            <p class="titresection"><?php echo $translations['accueil_contact_us']; ?></p>
            <p><i class="fas fa-envelope"></i> louiswinkelmuller@icloud.com</p>
            <p><i class="fas fa-phone"></i> 067487077</p>
        </div>
        <div>
            <p class="titresection"><?php echo $translations['accueil_navigation']; ?></p>
            <a href="accueil.php"><?php echo $translations['accueil_home']; ?></a>
            <br> 
            <a href="connexion.php"><?php echo $translations['accueil_login']; ?></a>
            <br> 
            <a href="forum.php"><?php echo $translations['accueil_forum']; ?></a>
        </div>
        <div class="copyright">
            <p>&copy; 2023 | Conferences4World - <?php echo $translations['accueil_all_rights_reserved']; ?></p>
        </div>
    </footer>
    
</body>
</html>