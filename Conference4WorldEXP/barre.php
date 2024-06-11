
<?php

// Connexion à la base de données
$bdd = new PDO("mysql:host=localhost;dbname=g4d", "root", "");

// Obtenir le mot clé saisi
$motCle = $_POST["mot-cle"];

// Exécuter la recherche
$sql = "SELECT * FROM conférence WHERE titre LIKE '%$motCle%'";
$results = $bdd->query($sql);


// Afficher les résultats
if ($results->rowCount() > 0) {
  while ($article = $results->fetch()) {
    echo $article["titre"];
  }
} else {
  echo "Aucun résultat trouvé.";
}



?>





