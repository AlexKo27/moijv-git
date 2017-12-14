<?php
require_once("connexion.dist.php");

//----------------------------------------
/*
if(!internauteEstConnecteEtEstAdmin())
{
    header("location:".URL."connexion.php");
    
}
*/
//-----------------------------------
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
    $resultat = $pdo ->prepare("DELETE FROM game WHERE id = :id");
    $resultat-> bindValue(':id_produit', $_GET['id_produit'],  PDO::PARAM_INT);
    $resultat->execute();

    $content .='<div class="alert alert-success col-md-8 col-md-offset-2 text-center">Le produit n°'.$_GET['id_produit'].' a bien été supprimer . </div>';

}
//-----------------------------------

if(!empty($_POST))
{
    $image_bdd = '';
    if(isset($_GET['action']) && $_GET['action'] == 'modification')
    {
        $image_bdd = $_POST['image_actuelle'];
    }


    if(!empty($_FILES['image']['name']))
    {
        $nom_image = $_POST['reference'] . '-' . $_FILES['image']['name'];
        //echo $nom_image;
        $image_bdd = URL . "image/$nom_image";
        //echo $image_bdd;
        $image_dossier = RACINE_SITE . "image/$nom_image";
        echo $image_dossier;
        copy($_FILES['image']['tmp_name'], $image_dossier);
        // Realiser le script permettant d'inserer un produit dans la table produit
    }    
        if(isset($_GET['action']) && $_GET['action'] == 'ajout')
        {
            $resultat = $pdo->prepare("INSERT INTO game (title,description,category,image,available) VALUES (:reference, :description, :category, :image, :available)");
            $content .=  '<div class="alert alert-success col-md-8 col-md-offset-2 text-center">Le produit est bien entré. </div>';        
        }
        else
        {
            $resultat = $pdo->prepare("UPDATE game SET title = :title, description = :description, category = :category, image = :image, available = :available WHERE id_produit = '$_POST[id]'");
            $_GET['action'] = 'affichage';
            $content .='<div class="alert alert-success col-md-8 col-md-offset-2 text-center">Le jeux n°'.$_GET['id'].' a bien été modifié . </div>';
        }
        $resultat->bindValue(':title', $_POST['title'], PDO::PARAM_STR);
		$resultat->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
		$resultat->bindValue(':category', $_POST['category'], PDO::PARAM_STR);
		$resultat->bindValue(':image', $image_bdd, PDO::PARAM_STR);
		$resultat->bindValue(':available', $_POST['available'], PDO::PARAM_STR);

		
        $resultat->execute();
    
}

//--------Lien produits------------
$content .= '<div class="list-group col-md-6 col-md-offset-3">';
$content .= '<h3 class="list-group-item active text-center" >BACK OFFICE</h3>';
$content .= '<a href="?action=affichage" class="list-group-item text-center ">Affichage de produit</a>';
$content .= '<a href="?action=ajout" class="list-group-item text-center ">Ajout de produit</a>';
$content .= '</div>';

//--------- AFFICHAGE PRODUIT---------
if(isset($_GET['action']) && $_GET['action'] == 'affichage')
{
    $resultat = $pdo ->query("SELECT * FROM game");
    $content .='<div class="col-md-10 col-md-offset-1 text-center" ><h3 class="alert-success">Affichage produit</h3>';
    $content .='Nombre de jeux(s) dans la boutique <span>' .$resultat->rowCount().' </span></div>' ;
    $content .='<table class="col-md-10 col-md-offset-1 table text-center" ><tr>';
    for($i = 0; $i < $resultat->columnCount(); $i++ )
    {
        $colonne = $resultat->getColumnMeta($i);
        $content .='<th>'. $colonne['name'].'</th>';
    }
    $content .='<th>Modification</th>';
    $content .='<th>Supression</th>';
    $content .='</tr>';
    while($ligne = $resultat->fetch(PDO::FETCH_ASSOC))
    {
        //debug($ligne);
        $content .= '<tr>';
        foreach($ligne as $indice => $valeur)
        {
            if($indice == "image")
            {
                $content .='<td><img src="'.$ligne['image'].'" alt="" width="165" height="90"></td>'; 
            }
            else
            {
                $content .='<td>'.$valeur.'</td>';
            }
        }
        $content .= '<td><a href="?action=modification&id_produit='.$ligne['id_produit'].'"><span class="glyphicon glyphicon-pencil"></span></a></td>';
        $content .= '<td><a href="?action=suppression&id_produit='.$ligne['id_produit'].'"><span class="glyphicon glyphicon-trash"></span></a></td>';
        $content .= '</tr>';
    }
    $content .='</table>';
}



require_once("../inc/haut.inc.php");
echo $content;
//debug($_POST);
//debug($_FILES);
//debug($_GET);

if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification'))
{
    if(isset($_GET['id']))
    {
        $resultat = $pdo->prepare("SELECT * FROM game WHERE id = :id");
        $resultat->bindValue('idt', $_GET['id'], PDO::PARAM_INT);
        $resultat->execute();
        //debug($resultat);
        $produit_actuel = $resultat->fetch(PDO::FETCH_ASSOC);
        //debug($produit_actuel);
    }

    $categorie = (isset($produit_actuel['categorie'])) ? $produit_actuel['categorie'] : '';
    $titre = (isset($produit_actuel['titre'])) ? $produit_actuel['titre'] : '';
    $description = (isset($produit_actuel['description'])) ? $produit_actuel['description'] : '';
    $couleur = (isset($produit_actuel['couleur'])) ? $produit_actuel['couleur'] : '';
    $taille = (isset($produit_actuel['taille'])) ? $produit_actuel['taille'] : '';
    $public = (isset($produit_actuel['public'])) ? $produit_actuel['public'] : '';
    $image = (isset($produit_actuel['image'])) ? $produit_actuel['image'] : '';
    $prix = (isset($produit_actuel['prix'])) ? $produit_actuel['prix'] : '';
    $stock = (isset($produit_actuel['stock'])) ? $produit_actuel['stock'] : '';


echo '<form method="post" action="" enctype="multipart/form-data" class="col-md-8 col-md-offset-2">
    <h2 class="alert alert-info text-center">';echo ucfirst($_GET['action']); echo 'Produit</h2>

    <input type="hidden" id="id" name="id" value="'.$id.'"">
    
    <div class="form-group">
    <label for="reference">Reference
    </label>
    <input type="text" class="form-control" id="reference" name="reference" value="'.$reference.'" placeholder="reference">
    </div>

    <div class="form-group">
    <label for="categorie">Categorie
    </label>
    <input type="text" class="form-control" id="categorie" name="categorie" value="'.$categorie.'" placeholder="categorie">
    </div>

    <div class="form-group">
    <label for="titre">titre
    </label>
    <input type="text" class="form-control" id="titre" name="titre" value="'.$titre.'" placeholder="titre">
    </div>

    <div class="form-group">
    <label for="description">Description
    </label>
    <textarea class="form-control" rows="3" id="description" name="description">'.$description.'</textarea>
    </div>

    <div class="form-group">
    <label for="couleur">Couleur
    </label>
    <input type="text" class="form-control" id="couleur" name="couleur" value="'.$couleur.'" placeholder="couleur">
    </div>

    <div class="form-group">
    <label for="taille">Taille
    </label>
    <input type="text" class="form-control" id="taille" name="taille" value="'.$taille.'" placeholder="taille">
    </div>

    <div class="form-group">
    <label for="public">Public
    </label>
    <select class="form-control"  name="public">
      <option value="mixte"';if( $public == 'mixte' ) echo 'selected'; echo'> mixte
      </option>;
      <option value="m"';if( $public == 'h' ) echo 'selected'; echo'> homme
      </option>;
      <option value="f"';if( $public == 'f' ) echo 'selected'; echo'> femme
      </option>;
    </select>
  </div>

    <div class="form-group">
    <label for="image">image
    </label>
    <input type="file" class="form-control" id="image"name="image"  ><br>';
    if(!empty($image))
    {
        echo '<i>Vous pouvez uploader une nouvelle image</i> <br>';
        echo '<img src="'.$image.'" width="165" height="90"><br>';
    }
    echo '<input type="hidden" name="image_actuelle" value="'.$image.'">';
    echo'</div>

    <div class="form-group">
    <label for="prix">prix
    </label>
    <input type="text" class="form-control" id="prix" name="prix" value="'.$prix.'" placeholder="prix">
    </div>
    <div class="form-group">
    <label for="stock">stock
    </label>
    <input type="text" class="form-control" id="stock" name="stock" value="'.$stock.'" placeholder="stock">
    </div>

    <button type="submit" class="col-md-12 btn btn-primary">'; echo ucfirst($_GET['action']); echo ' du produit</button>
    <br><br>
    <!--<a href="?action=ajout" class="text-center "><button type="submit" class="col-md-12 btn btn-primary">Ajout de produit</button></a>-->

</form>';
}


require_once("../inc/bas.inc.php");



