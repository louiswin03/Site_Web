<?php
session_start();

// Connexion à la base de données (assurez-vous d'avoir vos propres identifiants de connexion)
$host = 'localhost';
$db = 'g4d';
$user = 'root';
$password = '';

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
    <section>
        <section class="hero">
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
                        echo '<a href="connexion.php"><i class="fa-regular fa-user"></i>Connexion</a>';
                    }
                    ?>
                </div>
                <label for="check">
                    <i class="fas fa-bars menu-btn"></i>
                    <i class="fas fa-times close-btn"></i>
                </label>
            </header>
            <div class="container-conferences">
                <a href="conferences.php" class="back-link" ><i class="fa-solid fa-arrow-left" style="color:#000000; font-size: 24px;"></i></a>
            <?php
            // Obtention du nom du fichier actuel sans extension pour déduire le sujet
            $nom_fichier = basename($_SERVER['PHP_SELF'], '.php');
            $sujet = ucfirst($nom_fichier); // Convertir le nom du fichier en sujet
            // Connexion à la base de données
            $conn = new mysqli("localhost", "root", "root", "G4D");

            // Vérification de la connexion
            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            // Requête pour récupérer les informations des conférences en mathématiques par exemple
            $sql = "SELECT `sujet`,`titre`, `date`, `heure de début`, `heure de fin`, `salle_capacité`, `salle_numéro de salle` FROM conférence WHERE sujet = ?";

            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $sujet);
            $stmt->execute();
            $result = $stmt->get_result();  
            // Vérification du nombre de résultats
            if ($result->num_rows > 0) {
                echo '<h1 class="titre-conference">Conférences en ' . $sujet . '</h1>';
            // Affichage des détails pour chaque conférence
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="conference">';
                    echo '<h2 class="conference-nom">Titre : ' . $row["titre"] . '</h2>';
                    echo '<p class="conference-horaire">De : ' . $row["heure de début"] . '</p>';
                    echo '<p class="conference-horaire">À : ' . $row["heure de fin"] . '</p>';
                    echo '<p class="conference-date">Le : ' . $row["date"] . '</p>';
                    echo '<p class="conference-capacite">Capacité de la salle : ' . $row["salle_capacité"] . '</p>';
                    echo '<p class="conference-salle">Conférence en salle: ' . $row["salle_numéro de salle"] . '</p>';
                    echo '</div>';
                }
            } else {
            echo '<p>Aucune conférence trouvée.</p>';
            }

            // Fermeture de la connexion
            $conn->close();
            ?>

            </div>
            
        </section>
        <footer class="footer">
            <a href="acceuil.html"><img src="images/LogoC4Wsfond.png" alt="logo"></a>
        
            <div>
                <p class="titresection">Protection des données</p>
                <a href="Politique_confidentialité.php">Politique de confidentialité</a>
                <br> 
                <a href="conditions_utilisation.php">Conditions d'utilisation</a>
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
                <a href="connexion.php">Connexion</a>
                <br> 
                <a href="forum.php">Forum</a>
            </div>
            <div class="copyright">
                <p>&copy; 2023 | Conferences4World - Tous droits réservés</p>
            </div>
        </footer>
    </section>
</body>