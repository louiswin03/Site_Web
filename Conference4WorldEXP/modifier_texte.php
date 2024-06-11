<?php
session_start();

// Vérifiez si l'utilisateur est administrateur
if (!isset($_SESSION['id utilisateur']) || $_SESSION['type'] !== "admin") {
    // Redirigez vers une page d'erreur ou accueil.php
    header("Location: accueil.php");
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$db = 'g4d';
$user = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);

// Récupérez la section depuis les paramètres de l'URL
$section = isset($_GET['section']) ? $_GET['section'] : '';

// Vérifiez si la section est valide (vous pouvez ajouter des vérifications supplémentaires ici)
if (!in_array($section, ['section1', 'section2', 'section3', 'section4'])) {
    // Redirigez vers une page d'erreur ou accueil.php
    header("Location: accueil.php");
    exit();
}

// Récupérez le texte de la base de données pour la section spécifiée
$query = $pdo->prepare("SELECT * FROM texts WHERE section = ?");
$query->execute([$section]);
$text = $query->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Texte</title>
</head>
<body>
    <h1>Modifier le Texte - <?php echo $section; ?></h1>

    <form action="modifier_texte_action.php" method="post">
        <input type="hidden" name="section" value="<?php echo $section; ?>">
        <label for="content">Nouveau contenu :</label>
        <textarea name="content" id="content" rows="4" cols="50"><?php echo $text['content']; ?></textarea>
        <br>
        <input type="submit" value="Enregistrer les modifications">
    </form>

    <a href="accueil.php">Retour à l'accueil</a>
</body>
</html>