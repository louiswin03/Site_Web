<?php
session_start();

// Vérifier si l'utilisateur est connecté et s'il a le droit d'accéder à cette page
if (!isset($_SESSION['id utilisateur'])) {
    header("Location: connexion.html");
    exit();
}

$user_id = $_SESSION['id utilisateur'];
$conn = new mysqli("localhost", "root", "", "G4D");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si l'utilisateur a le droit de gérer les utilisateurs
$sql = "SELECT type FROM utilisateur WHERE `id utilisateur` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row["type"] !== "admin" && $row["type"] !== "conf") {
        header("Location: accueil.php");
        exit();
    }
}

// Récupérer la liste des utilisateurs
$sql_utilisateurs = "SELECT `id utilisateur`, Nom, Prénom, type FROM utilisateur";
$result_utilisateurs = $conn->query($sql_utilisateurs);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./images/Favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/styleCO.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Gérer les Utilisateurs</title>
    <link rel="stylesheet" href="chemin/vers/votre/styleCO.css">
</head>
<body>
    <div class="wrapper">
        <a href="profil.php" class="back-link"><i class="fa-solid fa-arrow-left" style="color:#000000; font-size: 24px;"></i></a>
        <h2>Gérer les Utilisateurs</h2>
        
        <form method="post" action="gerer_utilisateurs.php" class="form">
            <label for="utilisateur" class="form-label">Choisir l'utilisateur :</label>
            <select name="utilisateur_id" id="utilisateur" class="form-select">
                <?php
                while ($row_utilisateur = $result_utilisateurs->fetch_assoc()) {
                    echo "<option value='" . $row_utilisateur['id utilisateur'] . "'>" . $row_utilisateur['Nom'] . " " . $row_utilisateur['Prénom'] . " - " . $row_utilisateur['type'] . "</option>";
                }
                ?>
            </select>

            <select name="action" id="action" class="input-box form-select">
                <option value="supprimer">Supprimer</option>
                <option value="modifier_statut_admin">Passer en Admin</option>
                <option value="modifier_statut_utilisateur">Passer en Utilisateur</option>
            </select>

            <button type="submit" class="btnco form-button">Appliquer</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $selectedUserId = $_POST['utilisateur_id'];
            $selectedAction = $_POST['action'];

            // Vérifier si l'utilisateur tente de se supprimer lui-même
            if ($selectedAction == 'supprimer' && $selectedUserId == $user_id) {
                echo "<div class='message error'>Vous ne pouvez pas vous supprimer vous-même.</div>";
            } else {
                // Récupérer les informations de l'utilisateur
                $sql_info_utilisateur = "SELECT `Nom`, `Prénom`, `type` FROM utilisateur WHERE `id utilisateur` = ?";
                $stmt_info_utilisateur = $conn->prepare($sql_info_utilisateur);
                $stmt_info_utilisateur->bind_param("i", $selectedUserId);
                $stmt_info_utilisateur->execute();
                $result_info_utilisateur = $stmt_info_utilisateur->get_result();

                if ($result_info_utilisateur->num_rows > 0) {
                    $row_info_utilisateur = $result_info_utilisateur->fetch_assoc();
                    $prenom_nom_utilisateur = $row_info_utilisateur['Prénom'] . " " . $row_info_utilisateur['Nom'];
                    $role_utilisateur = $row_info_utilisateur['type'];
                }

                if ($selectedAction == 'supprimer') {
                    // Traitement de la suppression de l'utilisateur
                    $sql_supprimer = "DELETE FROM utilisateur WHERE `id utilisateur` = ?";
                    $stmt_supprimer = $conn->prepare($sql_supprimer);
                    $stmt_supprimer->bind_param("i", $selectedUserId);

                    if ($stmt_supprimer->execute()) {
                        echo "<div class='message success'>Vous avez choisi de supprimer l'utilisateur : $prenom_nom_utilisateur</div>";
                    } else {
                        echo "<div class='message error'>Erreur lors de la suppression de l'utilisateur.</div>";
                    }
                } elseif ($selectedAction == 'modifier_statut_admin' && $role_utilisateur !== 'admin') {
                    // Traitement de la modification du statut à "admin"
                    $sql_modifier_statut = "UPDATE utilisateur SET type = 'admin' WHERE `id utilisateur` = ?";
                    $stmt_modifier_statut = $conn->prepare($sql_modifier_statut);
                    $stmt_modifier_statut->bind_param("i", $selectedUserId);

                    if ($stmt_modifier_statut->execute()) {
                        echo "<div class='message success'>Vous avez choisi de passer $prenom_nom_utilisateur en statut 'admin'</div>";
                    } else {
                        echo "<div class='message error'>Erreur lors de la modification du statut de l'utilisateur.</div>";
                    }
                } elseif ($selectedAction == 'modifier_statut_utilisateur' && $role_utilisateur !== 'utilisateur') {
                    // Traitement de la modification du statut à "utilisateur"
                    $sql_modifier_statut = "UPDATE utilisateur SET type = 'utilisateur' WHERE `id utilisateur` = ?";
                    $stmt_modifier_statut = $conn->prepare($sql_modifier_statut);
                    $stmt_modifier_statut->bind_param("i", $selectedUserId);

                    if ($stmt_modifier_statut->execute()) {
                        echo "<div class='message success'>Vous avez choisi de passer $prenom_nom_utilisateur en statut 'utilisateur'</div>";
                    } else {
                        echo "<div class='message error'>Erreur lors de la modification du statut de l'utilisateur.</div>";
                    }
                } elseif ($selectedAction == 'modifier_statut_admin' && $role_utilisateur === 'admin') {
                    echo "<div class='message error'>$prenom_nom_utilisateur est déjà un administrateur.</div>";
                } elseif ($selectedAction == 'modifier_statut_utilisateur' && $role_utilisateur === 'utilisateur') {
                    echo "<div class='message error'>$prenom_nom_utilisateur est déjà un utilisateur.</div>";
                }
            }
        }
        ?>
    </div>
</body>
</html>

<?php
// Fermer la connexion à la base
$conn->close();
?>