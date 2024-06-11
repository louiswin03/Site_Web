<?php
session_start();

// Connexion à la base de données (à compléter avec vos informations de connexion)
$conn = new mysqli("localhost", "root", "", "G4D");

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Déterminer la langue (par exemple, à partir de paramètres de l'URL ou d'une session)
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr');

// Enregistrer la langue dans la variable de session
$_SESSION['lang'] = $lang;

$translations = include("lang/$lang.php");

// Traitement du formulaire pour ajouter une nouvelle question et réponse
if (isset($_POST['newQuestion']) && isset($_POST['newAnswer'])) {
    // Assurez-vous de nettoyer les données d'entrée pour éviter les injections SQL
    $newQuestion = $conn->real_escape_string($_POST['newQuestion']);
    $newAnswer = $conn->real_escape_string($_POST['newAnswer']);

    // Requête d'insertion dans la base de données
    $insertQuery = "INSERT INTO `question de FAQ` (`réponse proposée`, `question proposée`) VALUES ('$newAnswer', '$newQuestion')";
    if ($conn->query($insertQuery) === TRUE) {
        // Redirection vers la page de la FAQ après l'ajout réussi
        header("Location: FAQ.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout de la question : " . $conn->error;
    }
}

// Traitement de la suppression
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['question_id'])) {
    $question_id = $conn->real_escape_string($_POST['question_id']);

    // Ajoutez une vérification ici pour s'assurer que l'utilisateur est un administrateur
    if (isset($_SESSION['id utilisateur']) && $_SESSION['type'] === "admin") {
        $deleteQuery = "DELETE FROM `question de FAQ` WHERE `id_question` = $question_id";
        if ($conn->query($deleteQuery) === TRUE) {
            // Redirection vers la page de la FAQ après la suppression réussie
            header("Location: FAQ.php");
            exit();
        } else {
            echo "Erreur lors de la suppression de la question : " . $conn->error;
        }
    } else {
        echo "Vous n'avez pas les autorisations nécessaires pour supprimer cette question.";
    }
}

// Récupération des questions et réponses depuis la base de données
$sql_select = "SELECT `id_question`, 
                     CASE 
                         WHEN '$lang' = 'en' THEN `question proposée_en` 
                         ELSE `question proposée` 
                     END AS `question`, 
                     CASE 
                         WHEN '$lang' = 'en' THEN `réponse proposée_en` 
                         ELSE `réponse proposée` 
                     END AS `réponse` 
              FROM `question de FAQ`";
$result_select = $conn->query($sql_select);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <title>Conference 4 World</title>
</head>
<body>
    <section class="hero">
        <input type="checkbox" id="check">
        <header>
            <a href="accueil.php"><img src="images/LogoC4Wsfond.png" class="logo"></a>
            <div class="navigation">
            <a href="accueil.php"><?php echo $translations['accueil_home']; ?></a>
            <a href="conferences.php"><?php echo $translations['accueil_conferences']; ?></a>
            <a href="calendrier.php"><?php echo $translations['calendrier']; ?></a>
            <a class="navactif" href="FAQ.php"><?php echo $translations['accueil_FAQ']; ?></a>
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
        <div class="FAQwrapper slide-up">
            <h1><?php echo $translations['forum_common_questions']; ?></h1>
           
            <?php
            if ($result_select->num_rows > 0) {
                while ($row_select = $result_select->fetch_assoc()) {
                    // Structure HTML pour afficher chaque question et réponse
            ?>
                    <div class="faq">
                        <?php if (isset($_SESSION['id utilisateur']) && $_SESSION['type'] === "admin") { ?>
                            <!-- Formulaire de suppression -->
                            <form method="post" class="delete-form">
                                <input type="hidden" name="question_id" value="<?php echo $row_select['id_question']; ?>">
                                <button class="delete-btn"  onclick="return confirm('Voulez-vous vraiment supprimer cette question?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        <?php } ?>
                        <button class="question">
                            <?php echo $row_select['question']; ?>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="reponse">
                            <p>
                                <?php echo $row_select['réponse']; ?>
                            </p>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
            <?php
            // Vérifier si l'utilisateur est administrateur pour afficher le formulaire d'ajout de questions/réponses
            if (isset($_SESSION['id utilisateur'])) {
                $user_id = $_SESSION['id utilisateur'];
                $sql = "SELECT type FROM utilisateur WHERE `id utilisateur` = $user_id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row["type"] === "admin") {
            ?>
                            <form id="addQuestionForm" method="post">
                                <input type="text" name="newQuestion" id="newQuestion" placeholder="<?php echo $translations['forum_new_question']; ?>" required>
                                <textarea name="newAnswer" id="newAnswer" placeholder="<?php echo $translations['forum_new_answer']; ?>" required></textarea>
                                <button type="submit"><?php echo $translations['forum_add']; ?></button>
                            </form>
            <?php
                    }
                }
            }
            ?>
        </div>
    </section>
    <script src="script.js"></script>
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
        <a href="<?php echo isset($_SESSION['Nom']) ? 'profil.php' : 'connexion.php'; ?>">
        <?php echo $translations['accueil_login']; ?>
        </a>
        <br> 
        <a href="forum.php"><?php echo $translations['accueil_forum']; ?></a>
    </div>
    <div class="copyright">
        <p>&copy; 2023 | Conferences4World - <?php echo $translations['accueil_all_rights_reserved']; ?></p>
    </div>
</footer>
</body>
</html>