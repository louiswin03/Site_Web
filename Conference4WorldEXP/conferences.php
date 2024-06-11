<?php
session_start();

// Récupérer la langue depuis la variable de session
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr';

// Inclure le fichier de traduction correspondant
$translations = include("lang/$lang.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../Conference4WorldEXP/css/styles.css">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title><?php echo $translations['accueil_title']; ?></title>
</head>
<body>
    <section class="hero">
        <input type="checkbox" id="check">
        <header>
            <a href="accueil.php"><img src="images/LogoC4Wsfond.png" class="logo"></a>
            <div class="navigation">
			<a  href="accueil.php"><?php echo $translations['accueil_home']; ?></a>
            <a class="navactif" href="conferences.php"><?php echo $translations['accueil_conferences']; ?></a>
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
        <div class="sujets-container">
            <?php
            // Connexion à la base de données
            $conn = new mysqli("localhost", "root", "", "G4D");

            // Vérification de la connexion
            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            // Récupération de tous les sujets existants depuis la base de données
            $sql = "SELECT * FROM sujets_conference";
            $result = $conn->query($sql);

            // Vérification du nombre de résultats
            if ($result->num_rows > 0) {
                // Affichage des sujets existants
                while ($row = $result->fetch_assoc()) {
					// Choix de la colonne de nom_sujet en fonction de la langue
					$nom_sujet_affichage = ($lang === 'en') ? ucfirst($row["nom_sujet_en"]) : ucfirst($row["nom_sujet"]);
				
					// Afficher chaque sujet comme un lien vers sa page respective
					echo '<a class="link" href="' . strtolower($row["nom_sujet"]) . '.php">';
					echo '<div id="' . ucfirst($row["nom_sujet"]) . '" class="sujet">';
					echo '<img class="logosujet" src="' . $row["chemin_image"] . '">';
					echo '<a class="titre">' . $nom_sujet_affichage . '</a>';

                    if (isset($_SESSION['id utilisateur']) && $_SESSION['type'] === "admin") {
                        echo "
                            <form method='post' class='delete-form'>
                                <input type='hidden' name='delete_sujet_id' value='" . $row['id_sujets'] . "'>
                                <button type='submit' name='delete_sujet_submit' class='delete-btn'><i class='fas fa-trash'></i></button>
                            </form>
                        ";
                    }

                    echo '</div>';
                    echo '</a>';
                }

                // Vérification de la soumission du formulaire de suppression de thème
                if (isset($_POST['delete_sujet_submit']) && isset($_POST['delete_sujet_id'])) {
                    $delete_sujet_id = $conn->real_escape_string($_POST['delete_sujet_id']);

                    $deleteSujetQuery = "DELETE FROM sujets_conference WHERE id_sujets = '$delete_sujet_id'";

                    if ($conn->query($deleteSujetQuery) === TRUE) {
                        // Redirection vers la même page après la suppression réussie
                        echo"<script>
                                if(confirm('Voulez-vous vraiment supprimer ce sujet ?')) {
                                    window.location.href = 'conferences.php?confirm_delete=true&sujet_id=$delete_sujet_id';
                                }
                              </script>";
                    } else {
                        echo "Erreur lors de la suppression du sujet : " . $conn->error;
                    }
                }
            }

            // Vérifier si l'utilisateur est administrateur pour afficher le formulaire d'ajout de sujets
            if (isset($_SESSION['id utilisateur'])) {
                $user_id = $_SESSION['id utilisateur'];
                $sql = "SELECT type FROM utilisateur WHERE `id utilisateur` = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row["type"] === "admin") {
            ?>
                        <!-- Formulaire d'ajout de sujet pour l'administrateur -->
                        <a class="link">
                            <div class="sujet ajout-sujet">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <input type="text" name="nouveau_sujet" placeholder="<?php echo $translations['conferences_new_subject']; ?>" class="input-sujet">
                                    <input type="file" name="image_sujet" accept="image/*" class="input-sujet">
                                    <input type="submit" value="<?php echo $translations['conferences_add']; ?>" class="ajouter-btn">
                                </form>
                            </div>
                        </a>
            <?php
                    }
                }
            }

            if (isset($_POST['nouveau_sujet'])) {
                $nouveau_sujet = $_POST['nouveau_sujet'];
                $image = $_FILES['image_sujet'];

                // Vérifier si une image a été téléchargée
                if ($image['size'] > 0) {
                    $valid_types = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($image['type'], $valid_types) && getimagesize($image['tmp_name'])[0] === 300 && getimagesize($image['tmp_name'])[1] === 300) {
                        // Déplacer l'image vers un répertoire de stockage
                        $destination = 'images/' . $image['name'];
                        move_uploaded_file($image['tmp_name'], $destination);

                        // Insertion dans la base de données après traitement
                        $sql_insert = "INSERT INTO sujets_conference (nom_sujet, chemin_image) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql_insert);
                        $stmt->bind_param("ss", $nouveau_sujet, $destination);

                        if ($stmt->execute()) {
                            // Création de la nouvelle page
                            $modele_file = 'modele.php';
                            $base_content = file_get_contents($modele_file);
                            $file_name = strtolower($nouveau_sujet) . '.php';
                            $path = __DIR__ . '/' . $file_name;

                            if (!file_exists($path)) {
                                file_put_contents($path, $base_content);
                                header("Refresh: 1; URL=conferences.php"); 
                                exit();
                            } else {
                                echo "<script>alert(La page ".$nouveau_sujet." existe déjà!)</script>";
                            }
                        } else {
                            echo "<script>alert('Erreur lors de l\'insertion dans la base de données : " . $conn->error . "')</script>";
                        }
                    } else {
                        echo "<script>alert('L\'image doit être au format 300x300 pixels et être de type JPEG, PNG ou GIF.')</script>";
                    }
                } else {
                    echo "<script>alert('Veuillez sélectionner une image.')</script>";
                }
                header("Refresh: 2; URL=conferences.php");
				exit();

            }
            ?>
        </div>
        <?php
        // Si l'utilisateur est admin ou conférencier, il peut ajouter des conférences :
        if (isset($_SESSION['id utilisateur'])) {
            $user_id = $_SESSION['id utilisateur'];
            $sql = "SELECT type FROM utilisateur WHERE `id utilisateur` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row["type"] === "admin" or $row["type"] === "conf") {
        ?>
                        <div class="section-ajout-conf">
                            <a href="ajoutconferences.html" class="addconfbtn"><i class="fa-solid fa-plus"></i> <?php echo $translations['conferences_add_conference']; ?></a>
                        </div>
        <?php 
                    }
                }
        }
        ?>
    </section>
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
            <p><i class="fas fa-phone"></i> 0674870757</p>
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