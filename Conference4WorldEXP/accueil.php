


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
<html lang="<?php echo $lang; ?>">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="script.js"></script> <!-- Déplacez cette ligne ici -->
    <title>Conference 4 World</title>


    <style>
    /* Ajoutez du style pour les boutons de changement de langue */
    .lang-switcher {
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 16px;
    }

    .lang-switcher a {
        padding: 5px 10px;
        text-decoration: none;
        color: #333;
        border: 1px solid #ccc;
        border-radius: 5px;
        transition: background-color 0.3s, color 0.3s; /* Ajout de transition pour le hover */
        display: flex;
        align-items: center;
    }

    .lang-switcher a img {
        margin-right: 5px;
    }

    .lang-switcher a:hover {
        background-color: #ff7f50; /* Couleur orange pour le hover */
        color: #fff; /* Texte en blanc pour le hover */
    }

    .lang-switcher a.active {
        background-color: #555;
        color: #fff;
    }

    /* Style amélioré pour les boutons "Modifier" */
    .admin-buttons {
        margin-top: 10px;
    }

    .admin-buttons a {
        padding: 8px 12px;
        text-decoration: none;
        color: #fff;
        
        border-radius: 5px;
        margin-right: 10px;
        transition: background-color 0.3s; /* Ajout de transition pour le hover */
    }

    .admin-buttons a:hover {
        background-color: #FF7004; 
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

        <div class="lang-switcher">
            <a href="?lang=fr" <?php echo ($lang == 'fr') ? 'class="active"' : ''; ?>>
                <img src="images/drapeau-france.jpg" alt="French Flag" style="max-width: 30px; max-height: 20px;">
                Fr
            </a>
            <a href="?lang=en" <?php echo ($lang == 'en') ? 'class="active"' : ''; ?>>
                <img src="images/drapeau-royaume.jpg" alt="English Flag" style="max-width: 30px; max-height: 20px;">
                Eng
            </a>
            <!-- Ajoutez des liens pour d'autres langues au besoin -->
        </div>

        <label for="check">
        <i class="fas fa-bars menu-btn"></i>
        <i class="fas fa-times close-btn"></i>
        </label>

        <input type="checkbox" id="check">

       
</header>



<section class="hero">
        <input type="checkbox" id="check">
        <div class="content">
            <div class="info slide-up">
                <h2><?php echo $textsArray['section1']; // Afficher le bouton "Modifier" pour la section 1 si l'utilisateur est administrateur
    if ($is_admin) {
        echo '<div class="admin-buttons">';
        $section = 'section1';
        echo '<a href="modifier.php?section=' . $section . '">Modifier ' . $section . '</a>';
        echo '</div>';
    }?><?php
    
    ?></h2>
                
                <p><?php echo $textsArray['section2'];
                if ($is_admin) {
                    echo '<div class="admin-buttons">';
                    $section = 'section2';
                    echo '<a href="modifier.php?section=' . $section . '">Modifier ' . $section . '</a>';
                    echo '</div>';
                } ?> </p>

              
                


<a href="<?php echo isset($_SESSION['Nom']) ? 'profil.php' : 'connexion.php'; ?>" class="CoBouton"><?php echo $translations['accueil_login']; ?></a>

            </div>
        </div>
    </section>

    <section class="demo">
        <div class="content-demo">
            <div class="image slide-up">
                <img src="images/Image2.png" class="imagedemo">
            </div>
            <div class="text slide-up">
                <h2><?php echo $textsArray['section3']; 
                // Afficher le bouton "Modifier" pour la section 1 si l'utilisateur est administrateur
    if ($is_admin) {
        echo '<div class="admin-buttons">';
        $section = 'section3';
        echo '<a href="modifier.php?section=' . $section . '">Modifier ' . $section . '</a>';
        echo '</div>';
    }?></h2>
                
                <p><?php echo $textsArray['section4']; 
                // Afficher le bouton "Modifier" pour la section 1 si l'utilisateur est administrateur
    if ($is_admin) {
        echo '<div class="admin-buttons">';
        $section = 'section4';
        echo '<a href="modifier.php?section=' . $section . '">Modifier ' . $section . '</a>';
        echo '</div>';
    }?></p>
            </div>
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

<a href="<?php echo isset($_SESSION['Nom']) ? 'profil.php' : 'connexion.php'; ?>" <?php echo $translations['accueil_login'];