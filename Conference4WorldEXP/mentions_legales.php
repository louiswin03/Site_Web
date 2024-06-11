<?php
session_start();

$conn = new mysqli("localhost", "root", "", "G4D");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement du formulaire de suppression ici
if (isset($_POST['delete_submit']) && isset($_POST['delete_id'])) {
    $delete_id = $conn->real_escape_string($_POST['delete_id']);
    $deleteQuery = "DELETE FROM `CGU_mentions_legales` WHERE `id_CGU_mentions_legales` = $delete_id";

    if ($conn->query($deleteQuery) === TRUE) {
        // Redirigez après la suppression réussie
        header("Location: mentions_legales.php");
        exit();
    } else {
        echo "Erreur de suppression : " . $conn->error;
    }
}

// Traitement du formulaire d'ajout ici
if (isset($_POST['submit']) && isset($_POST['new_title']) && isset($_POST['new_content'])) {
    $new_title = $conn->real_escape_string($_POST['new_title']);
    $new_content = $conn->real_escape_string($_POST['new_content']);

    $insertQuery = "INSERT INTO `CGU_mentions_legales` (titre, contenu, type) VALUES ('$new_title', '$new_content', 'mentions légales')";
    if ($conn->query($insertQuery) === TRUE) {
        // Redirigez après l'ajout réussi
        header("Location: mentions_legales.php");
        exit();
    } else {
        echo "Erreur d'ajout : " . $conn->error;
    }
}

// Récupérer la langue depuis la variable de session
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr';

// Inclure le fichier de traduction correspondant
$translations = include("lang/$lang.php");
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title><?php echo $translations['titre_mentions_legales']; ?></title>
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
            <a  href="accueil.php"><?php echo $translations['accueil_home']; ?></a>
            <a href="conferences.php" class="navactif"><?php echo $translations['conferences_conferences']; ?></a>
            <a href="forum.php"><?php echo $translations['accueil_forum']; ?></a>
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
            <h1><?php echo $translations['titre_mentions_legales']; ?></h1>
            <p><?php echo $translations['lire_attentivement']; ?></p>

            <?php
            $sql = "SELECT * FROM CGU_mentions_legales";
            $result_cgu = $conn->query($sql);

            if ($result_cgu->num_rows > 0) {
                while ($row_cgu = $result_cgu->fetch_assoc()) {
                    $titre_affichage = ($lang === 'en') ? ucfirst($row_cgu["titre_en"]) : ucfirst($row_cgu["titre"]);
                    $contenu_affichage = ($lang === 'en') ? ucfirst($row_cgu["contenu_en"]) : ucfirst($row_cgu["contenu"]);

                    echo "<h2>" . $titre_affichage . "</h2>";
                    echo "<p>" . $contenu_affichage . "</p>";

                    // Formulaire de suppression
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
                echo $translations['aucun_contenu_trouve'];
            }

            if (isset($_SESSION['id utilisateur'])) {
                $user_id = $_SESSION['id utilisateur'];
                $sql = "SELECT type FROM utilisateur WHERE `id utilisateur` = $user_id";
                $result = $conn->query($sql);

                // Fonctionnalité ajout
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row["type"] === "admin") {
                        echo "
                            <form method='post' class='CGU-form'>
                                <h2>" . sprintf($translations['ajoutez_mentions_legales'], $_SESSION['Prénom']) . "</h2>
                                <input class='CGU-titre' type='text' name='new_title' placeholder='" . $translations['nouveau_titre'] . "' required><br>
                                <textarea class='CGU-text' name='new_content' placeholder='" . $translations['nouveau_contenu'] . "' required></textarea><br>
                                <button type='submit' name='submit'>" . $translations['ajouter'] . "</button>
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