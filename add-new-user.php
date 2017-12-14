<?php
require_once("connexion.dist.php");
//echo '<pre>'; print_r($_POST);echo '</pre>';

/* Contrôlé les champs suivants :
	contrôler la disponibilité du username,
	contrôler la taille des champs : username, lastname, firstname : entre 4 et 20 caractères
*/

if($_POST)
{
	
	$erreur = '';
	$resultat = $pdo->query("SELECT * FROM user WHERE username = '$_POST[username]'");
	if($resultat->rowCount() >= 1)
	{
		$erreur .= '<div class="alert alert-danger col-md-8 col-md-offset-2">username indisponible</div>';
	}
	if(strlen($_POST['username']) < 4 || strlen($_POST['username']) > 20)
	{
		$erreur .= '<div class="alert alert-danger col-md-8 col-md-offset-2">Erreur de taille username</div>';
	}
	if(strlen($_POST['lastname']) < 4 || strlen($_POST['lastname']) > 20)
	{
		$erreur .= '<div class="alert alert-danger col-md-8 col-md-offset-2">Erreur de taille lastname</div>';
	}
	if(strlen($_POST['firstname']) < 4 || strlen($_POST['firstname']) > 20)
	{
		$erreur .= '<div class="alert alert-danger col-md-8 col-md-offset-2">Erreur de taille prélastname</div>';
	}
	if(!preg_match('#^[a-zA-Z0-9._-]+$#',$_POST['username']))
	{
		$erreur .= '<div class="alert alert-danger col-md-8 col-md-offset-2">Erreur format/caractère username</div>';
	}
	// preg_match() : une expression régulière est toujours entouré de # pour préciser les options:
	// ^ indique le début de la chaine
	// $ indique la fin de la chaine
	// + est la pour dire que les lettres autorisés peuvent apparaitre plausieurs fois
	foreach($_POST as $indice => $valeur)
	{
		$_POST[$indice] = strip_tags($valeur);
	}
	
	$content .= $erreur;
	
	if(empty($erreur))
	{
		//$_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT); // cryptage du password.
		
		$resultat = $pdo->prepare("INSERT INTO user(username,password,email,lastname,firstname)VALUES(:username,:password,:email,:lastname,:firstname)");
		
		$resultat->bindValue(':username',$_POST['username'],PDO::PARAM_STR);
		$resultat->bindValue(':password',$_POST['password'],PDO::PARAM_STR);
		$resultat->bindValue(':email',$_POST['email'],PDO::PARAM_STR);
		$resultat->bindValue(':firstname',$_POST['firstname'],PDO::PARAM_STR);
		$resultat->bindValue(':lastname',$_POST['lastname'],PDO::PARAM_STR);

		
		$resultat->execute();
		
		$content .= '<div class="alert alert-success col-md-8 col-md-offset-2">Vous êtes inscrit à notre site WEB. <a href="connexion.php">Cliquez ici pour vous connecter</a></div>';
		
		
	}
}

echo $content;

?>


<!-- Réaliser un formulaire d'inscription correspondant à la table membre de la BDD -->

<form method="post" action="" class="col-md-8 col-md-offset-2">
	<h1 class="alert alert-info text-center">Inscription</h1>

  <div class="form-group">
    <label for="username">username</label>
    <input type="text" class="form-control" id="username" name="username" placeholder="username" pattern="[a-zA-Z0-9-_.]{4,20}" title="caractères acceptés : a-zA-Z0-9-_.">
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="text" class="form-control" id="password" name="password" placeholder="Password">
  </div>
  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email"  name="email" placeholder="Email">
  </div>
  <div class="form-group">
    <label for="firstname">firstname</label>
    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="firstname">
  </div>
  <div class="form-group">
      <label for="lastname">lastname</label>
    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="lastname">
  </div>
  
  <input type="submit" class="col-md-12 btn btn-primary" value="inscription"><br><br><br>
</form>


