<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    // Rediriger vers la page d'accueil ou une autre page appropriée si l'utilisateur n'est pas un administrateur
    header('Location: accueil.php');
    exit();
}

// Connexion à la base de données (assurez-vous d'avoir vos propres identifiants de connexion)
$host = 'localhost';
$db = 'g4d';
$user = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);

// Récupérer la section à modifier depuis la requête
$sectionToModify = isset($_GET['section']) ? $_GET['section'] : '';

// Vérifier si la section à modifier est valide
if (empty($sectionToModify)) {
    // Rediriger vers la page d'accueil ou une autre page appropriée si la section n'est pas spécifiée
    header('Location: accueil.php');
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer le nouveau contenu de la section depuis le formulaire
    $newContent = isset($_POST['new_content']) ? $_POST['new_content'] : '';

    // Mettre à jour le contenu de la section dans la base de données
    $updateQuery = $pdo->prepare("UPDATE texts SET content = :content WHERE section = :section");
    $updateQuery->bindParam(':content', $newContent, PDO::PARAM_STR);
    $updateQuery->bindParam(':section', $sectionToModify, PDO::PARAM_STR);
    
    if ($updateQuery->execute()) {
        // Rediriger vers la page d'accueil ou une autre page appropriée après la modification
        header('Location: accueil.php');
        exit();
    } else {
        // Gérer l'erreur de mise à jour (par exemple, afficher un message d'erreur)
        $errorMessage = 'Une erreur est survenue lors de la modification du contenu.';
    }
}

// Récupérer le contenu actuel de la section depuis la base de données
$query = $pdo->prepare("SELECT * FROM texts WHERE section = :section");
$query->bindParam(':section', $sectionToModify, PDO::PARAM_STR);
$query->execute();
$text = $query->fetch(PDO::FETCH_ASSOC);

// Vérifier si la section existe
if (!$text) {
    // Rediriger vers la page d'accueil ou une autre page appropriée si la section n'existe pas
    header('Location: accueil.php');
    exit();
}

// Fermer la connexion à la base de données
$pdo = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Modifier <?php echo $sectionToModify; ?></title>
</head>
<body>
    <h1>Modifier <?php echo $sectionToModify; ?></h1>

    <?php
    if (isset($errorMessage)) {
        echo '<p style="color: red;">' . $errorMessage . '</p>';
    }
    ?>

    <form method="post" action="modifier.php?section=<?php echo $sectionToModify; ?>">
        <label for="new_content">Nouveau contenu :</label>
        <textarea id="new_content" name="new_content" rows="4" cols="50"><?php echo $text['content']; ?></textarea>
        <br>
        <input type="submit" value="Modifier">
    </form>
</body>
</html>