<?php

    session_start();

    ob_start();



    // Connexion à la base de données (à compléter avec vos informations de connexion)

    $conn = new mysqli("localhost", "root", "", "g4d");

    $host = 'localhost';
$db = 'g4d';
$user = 'root';
$password = '';




    if ($conn->connect_error) {

        die("La connexion a échoué : " . $conn->connect_error);

    }



    // Traitement du formulaire pour ajouter une nouvelle question et réponse

    if (isset($_POST['newQuestion']) && isset($_POST['newAnswer'])) {

        // Assurez-vous de nettoyer les données d'entrée pour éviter les injections SQL

        $newQuestion = $conn->real_escape_string($_POST['newQuestion']);

        $newAnswer = $conn->real_escape_string($_POST['newAnswer']);



        // Requête d'insertion dans la base de données

        $insertQuestionQuery = "INSERT INTO `question_forum` (`contenu`, `date de création`, `administrateur_id administrateur`) VALUES ('$newQuestion', NOW(), 1)";



        if ($conn->query($insertQuestionQuery) === TRUE) {

            // Redirection vers la page de la FAQ après l'ajout réussi

            header("Location: forum.php");

            exit();

        } else {

            die("Erreur lors de l'ajout de la question : " . $conn->error);

        }

    }



    // Traitement du formulaire pour ajouter une nouvelle question et réponse

    if (isset($_POST['newQuestion']) && isset($_POST['newAnswer'])) {

        // Assurez-vous de nettoyer les données d'entrée pour éviter les injections SQL

        $newQuestion = $conn->real_escape_string($_POST['newQuestion']);

        $newAnswer = $conn->real_escape_string($_POST['newAnswer']);



        // Requête d'insertion dans la base de données

        $insertQuery = "INSERT INTO `question de FAQ` (`réponse proposée`, `question proposée`) VALUES ('$newAnswer', '$newQuestion')";

        if ($conn->query($insertQuery) === TRUE) {

            // Redirection vers la page de la FAQ après l'ajout réussi

            header("Location: forum.php");

            exit();

        } else {

            echo "Erreur lors de l'ajout de la question : " . $conn->error;

        }

    }



    // Récupération des questions et réponses depuis la base de données

    $sql_select = "SELECT `question proposée`, `réponse proposée` FROM `question de FAQ`";

    $result_select = $conn->query($sql_select);


    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);

    // Déterminer la langue (par exemple, à partir de paramètres de l'URL ou d'une session)
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr');

// Enregistrer la langue dans la variable de session
$_SESSION['lang'] = $lang;

// Vérifier si l'utilisateur est administrateur
$is_admin = isset($_SESSION['type']) && $_SESSION['type'] == 'admin';

// Récupérer les données de la base de données
// Récupérer les données de la base de données
$query = $pdo->prepare("SELECT * FROM texts WHERE section IN ('section1', 'section2', 'section3', 'section4')");
$query->execute();
$texts = $query->fetchAll(PDO::FETCH_ASSOC);

// Créer un tableau associatif pour stocker les textes en fonction de leur section
$textsArray = [];
foreach ($texts as $text) {
    // Utiliser la variable $lang pour déterminer la langue appropriée
    $textKey = ($lang == 'en') ? 'content_en' : 'content';
    $textsArray[$text['section']] = $text[$textKey];
}

// Inclure le fichier de traduction correspondant
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

<html lang="fr">

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
            <a href="FAQ.php"><?php echo $translations['accueil_FAQ']; ?></a>
            <a class="navactif" href="forum.php">Forum</a>
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

      





        <div class="Forum">

    <p id="forum">Forum du site</p>



    <!-- Formulaire pour ajouter une nouvelle question -->




    <?php

    // Traitement du formulaire pour ajouter une nouvelle question

    if (isset($_POST['submit_question'])) {

        $utilisateur = $_SESSION['id_utilisateur'];

        $contenu_question = $conn->real_escape_string($_POST['contenu_question']);



        $insertQuestionQuery = "INSERT INTO question_forum (`contenu`, `administrateur_id administrateur`) VALUES ('$contenu_question', '$utilisateur')";



        if ($conn->query($insertQuestionQuery) === TRUE) {

            header("Location: forum.php");

            exit();

        } else {

            $errorMessage = "Erreur lors de l'ajout de la question : " . $conn->error;

            echo $errorMessage;

        }

    }



    // Traitement du formulaire pour ajouter une nouvelle réponse

    if (isset($_POST['submit_reponse'])) {

        $utilisateur = $_SESSION['id_utilisateur'];

        $id_question = $_POST['id_question'];  // Ajout du champ hidden dans le formulaire

        $contenu_reponse = $conn->real_escape_string($_POST['contenu_reponse']);



        $insertReponseQuery = "INSERT INTO reponse_forum (`question_forum_id_question`, `administrateur_id administrateur`, `contenu`) VALUES ('$id_question', '$utilisateur', '$contenu_reponse')";



        if ($conn->query($insertReponseQuery) === TRUE) {

            header("Location: forum.php");

            exit();

        } else {

            $errorMessage = "Erreur lors de l'ajout de la réponse : " . $conn->error;

            echo $errorMessage;

        }

    }



    // Récupération des questions depuis la base de données

    $sql_select_questions = "SELECT `id question`, `contenu`, `date de création`, `administrateur_id administrateur` FROM question_forum";

    $result_select_questions = $conn->query($sql_select_questions);

    ?>





        <!-- Affichage du formulaire pour ajouter une nouvelle question -->

        <?php

        if (isset($_SESSION['id utilisateur'])) {

            echo '<form method="post" action="">

                    <label for="contenu_question">Posez votre question :</label><br>

                    <textarea name="contenu_question" id="contenu_question" cols="30" rows="5" required></textarea><br>

                    <input type="submit" name="submit_question" value="Poser la question" class="buttonforum">

                </form>';

        }

        ?>



        <!-- Affichage des questions -->

        <br>

        <h2 id="questions">Questions :</h2>

        <br>

        <?php

        while ($row_question = $result_select_questions->fetch_assoc()) {

            echo "<div>";

            echo "<div class='question-container'>";

            echo "<p><strong>Nom utilisateur :</strong> " . $row_question['administrateur_id administrateur'] . "</p>";

            echo "<p><strong>Question:</strong> " . $row_question['contenu'] . "</p>";

            echo "<p><strong>Date de création:</strong> " . $row_question['date de création'] . "</p>";



            // Formulaire de réponse

            echo "<form method='post' action=''>";

            echo "<input type='hidden' name='id_question' value='"  . $row_question['id question'] . "'>";

            echo "<label for='contenu_reponse'>Répondre à la question :</label><br>";

            echo "<textarea name='contenu_reponse' id='contenu_reponse' cols='30' rows='2' required></textarea><br>";

            echo "<input type='submit' name='submit_reponse' value='Répondre' class='buttonanswer'>";

            echo "</form>";

            



            // Affichage des réponses associées à la question

            $id_question = $row_question['id question'];

            $sql_select_reponses = "SELECT * FROM reponse_forum WHERE question_forum_id_question = '$id_question'";

            $result_select_reponses = $conn->query($sql_select_reponses);



            if ($result_select_reponses->num_rows > 0) {

                echo "<p><strong>Réponses :</strong></p>";

                while ($row_reponse = $result_select_reponses->fetch_assoc()) {

                    echo "<p>" . $row_reponse['contenu'] . "</p>";

                }

            }

            echo "</div>";

        }

        ?>

</div>

























































    </section>

    <script src="script.js"></script>

    <footer class="footer">

        <a href="accueil.php"><img src="images/LogoC4Wsfond.png" alt="logo"></a>

    

        <div>

            <p class="titresection">Protection des données</p>

            <a href="mentions_legales.php">Mentions légales</a>

            <br> 

            <a href="conditions_utilisation.php">Conditions Générales d'Utilisation</a>

        </div>

        <div>

            <p class="titresection">Nous contacter</p>

            <p><i class="fas fa-envelope"></i> louiswinkelmuller@icloud.com</p>

            <p><i class="fas fa-phone"></i> 0674870757</p>

        </div>

        <div>

            <p class="titresection">Navigation</p>

            <a href="acceuil.php">Accueil</a>

            <br> 

            <a href="connexion.html">Connexion</a>

            <br> 

            <a href="forum.php">Forum</a>

        </div>

        <div class="copyright">

            <p>&copy; 2023 | Conferences4World - Tous droits réservés</p>

        </div>

    </footer>

</body>

</html>