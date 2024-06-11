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
                    session_start();
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
                <a href="javascript:history.go(-1)" class="back-link" ><i class="fa-solid fa-arrow-left" style="color:#000000; font-size: 24px;"></i></a>
            <?php
            // Connexion à la base de données
            $conn = new mysqli("localhost", "root", "", "G4D");

            // Vérification de la connexion
            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            // Requête pour récupérer les informations des conférences en mathématiques par exemple
            $sql = "SELECT `sujet`,`titre`, `date`, `heure de début`, `heure de fin`, `salle_capacité`, `salle_numéro de salle` FROM conférence WHERE sujet = 'Mathématiques'";
            $result = $conn->query($sql);

            // Vérification du nombre de résultats
            if ($result->num_rows > 0) {
            // Affichage des détails pour chaque conférence
                while ($row = $result->fetch_assoc()) {
                    echo '<form method="POST" action="barre.php">
                    <div class="barre-recherche">
                      <input type="text" name="mot-cle" placeholder="Tapez votre mot clé">
                      <button onclick="search()">Rechercher</button>
                      
                    </div>';
                    echo '<h1 class="titre-conference">Conférences en ' . $row["sujet"] . '</h1>';
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