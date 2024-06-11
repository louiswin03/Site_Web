<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr');
$translations = include("lang/$lang.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Your existing code for processing form submission

    // Example translation usage
    $passwordMismatchAlert = $translations['password_mismatch_alert'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer l'id de l'utilisateur de la session
    $id_utilisateur = $_SESSION["id utilisateur"];
    $ancienMotDePasse = $_POST["AncienMotDePasse"];
    $nouveauMotDePasse = $_POST["NouveauMotDePasse"];
    $confirmNouveauMotDePasse = $_POST["ConfirmerNouveauMotDePasse"];

    // Vos informations de connexion à la base de données
    $serveur = 'localhost'; 
    $utilisateur_db = 'root'; 
    $mot_de_passe_db = ''; 
    $nom_base_de_donnees = 'G4D'; 

    try {
        $connexion = new PDO("mysql:host=$serveur;dbname=$nom_base_de_donnees", $utilisateur_db, $mot_de_passe_db);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer le mot de passe haché de l'utilisateur
        $requete = $connexion->prepare("SELECT `mot de passe` FROM `utilisateur` WHERE `id utilisateur` = :id_utilisateur");
        $requete->bindParam(':id_utilisateur', $id_utilisateur);
        $requete->execute();

        $resultat = $requete->fetch(PDO::FETCH_ASSOC);

        if ($resultat) {
            $motDePasseStocke = $resultat['mot de passe'];

            // Vérifier si l'ancien mot de passe est correct en utilisant password_verify
            if (password_verify($ancienMotDePasse, $motDePasseStocke)) {
                // Mettre à jour le mot de passe avec le nouveau mot de passe haché
                $nouveauMotDePasseHache = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);
                $updateMotDePasse = $connexion->prepare("UPDATE `utilisateur` SET `mot de passe` = :nouveauMotDePasse WHERE `id utilisateur` = :id_utilisateur");
                $updateMotDePasse->bindParam(':nouveauMotDePasse', $nouveauMotDePasseHache);
                $updateMotDePasse->bindParam(':id_utilisateur', $id_utilisateur);
                $updateMotDePasse->execute();
                
                // Afficher une notification et rediriger vers la page d'accueil
                echo '<script>alert("Mot de passe modifié avec succès."); window.location.href = "accueil.php";</script>';
            } else {
                echo '<script>alert("L\'ancien mot de passe est incorrect."); history.back();</script>';
            }
        } else {
            echo '<script>alert("Erreur lors de la récupération du mot de passe stocké."); history.back();</script>';
        }
        
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>