//// Cours MVC




/*

Le modele signifie

    - Modeles
    - Vues
    - Controleurs

    MVC est un model qui permet de créer des sites web de manière organisée en organisée en POO. Le but de ce modele est de séparer les differentes parties des pages d'un site web :

        * Le modele est la partie qui va gerer la persisitance des donées (DTO + DAO)
        * La vue est la partie qui va gerer l'affichage de la page (html + php d'affichage)
        * Le controleur est la partie logique de la page qui va gerer les vues en fonction ou pas des modèles
        
 
        Il y a deux choses suppplementaires utilisées en general sur les sites web MVC :

        * toutes les pages du site qui sont appelés par une seule page physique : index.php (on l'aplle le controlleur frontal)
        * Meme si toutes les pages sont appelés par le controleu frontal, chaque page du site aura son url à elle ( ce qu'on appelle une route). C'est le contoleur frontal qui sera chargé d'aller chercherle controleur correspondant à la route demandée.
        * 
        * 

.htaccess______________________
   #desactivation du listing des dossiers
Options -Indexes

# Verification que le module de réecriture d'URL est bien activé
<IfModule mod_rewrite.c>

    #desactivation de la ndegociation des contenus
    Options -Multiviews

    #Activationd e la récriture d'URL
    RewriteEngine On


    #Condition qui va empecher la redirection sur l'index.php si l'url demandée pointe sur un dossier ou un fichier réeelment existant
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    
    #Redirection de  toutes les requetes sur index.php
    RewriteRule ^ index.php [QSA,L]

</IfModule>

index.php__________________________________
<?php

//Inclusion des vendors composer
require __DIR__ . '/vendor/autoload.php';

//Inclusion des controleuirs
require __DIR__ . '/controllers/Controllers.php';

require 'models/DTO/User.php';
require 'models/DAO/UserManager.php';

//Recuperation e la route demandée (l'url)
$route = $_SERVER['REQUEST_URI'];


//instanciation es controleurs
$controllers = new App\Controllers\Controllers();


// Pas beau mais pour simplifier on stocke le nom du dossier du site pour evioter de devoir reecrire à chaque fois
$prefix = '/mvc';
//Si l'URL demandée est "/", alors on charge le controleur de la page d'accueil
if($route == $prefix . '/'){

    $controllers->home();

    //Si l'url demandée est "/contactez nous/" alors on charge le controleur de la page contact
} elseif($route == $prefix . '/contactez-nous/'){
    $controllers->contact();

} elseif($route == $prefix . '/creer-un-compte/'){

    $controllers->register();

    // Si on arrive ici dans le else, c'est que l'URL demandée ne correspond à aucune page du site, donc on appelle le controleur de la page 404
} else{
    $controllers->page404();
}


user.php___________________________
 
<?php
namespace App\Models\DTO;

/**
 * Classe DTO des utilisateurs
 */
/*Class User{
    private $email;
    private $password;

    public function getEmail(){
        return $this->email;
    }

    public function getPassword(){
        return $this->password;
    }

    public function setEmail(string $email){
        $this->email = $email;
    }

    public function setPassword(string $password){
        $this->password = $password;
    }
}
*/

/*home.php______________________
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>
<body>
<h1>Page accueil</h1>
    
</body>
</html>


register.php____________________________
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>

    <div>
        <?php
        
        if(isset($success)){
            echo '<p style="color:green;">' . $success . '</p>';
        } else {
            
            if(isset($errors)){
                foreach($errors as $error){
                    echo '<p style="color:red;">' . $error . '</p>';
                }
            }

            ?>
            <form action="" method="POST">
                
                <input type="text" placeholder="email" name="email">
                <input type="password" placeholder="password" name="password">
                <input type="submit">

            </form>
            <?php
        }
        
        ?>
    </div>
</body>
</html>

controlers.php____________________________________

<?php

namespace App\Controllers;

use \App\Models\DTO\User;
use \App\Models\DAO\UserManager;

/**
 * Liste des controlers du site
 

class Controllers{

    /*
      Controleur de la page d'acceuil
     
    
     public function home(){
         //Appel de la vue home.php
         require 'views/home.php';
     }
    
     /**
      * Controleur page contact
      
    
      public function contact(){
          echo 'Page contact!';
      }
    
      /**
       * Controleur page inscription
       
      public function register(){
    
            //Appel des variables
    
            if(
                isset($_POST['email']) &&
                isset($_POST['password'])
            ){
    
            //Bloc des verifs 
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $errors[] = 'Email pas bon';
            }
            if(!preg_match('/^.{8,1000}$/', $_POST['password'])){
                $errors[] = 'mot de passe  pas bon';
            }
    
            // Si pas d'erreurs
            if(!isset($errors)){
    
                $newUser = new User();
    
                $newUser->setEmail($_POST['email']);
                $newUser->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT));
    
               // Instanciation du manager des utilisateurs
               $userManager = new UserManager();
    
               //Sauvegarde en bdd de l'utilisiateur
               $statut = $userManager->save($newUser);
    
               if($statut){
                   $success = 'Compte créer !';
               } else {
                   $errors[] = 'Problème avec la bdd';
               }
            }
        }
    // Appel de la vue register.php
    require 'views/register.php';
      }
    
      /**
       * controleurs page 404
       
    public function page404(){
    
        header('HTTP/1.0 404 Not Found');
        echo 'Erreur 404 !';
        }
} 
*/

/*
UserManager.php______________________________________
/*
<?php

namespace App\Models\DAO;

use\App\Models\DTO\User;
use\PDO;

/**
 * Classe DAO des utilisateurs
 
class UserManager{

    public function save(User $userToSave){

        $bdd = new PDO('mysql:host=localhost;dbname=mvc;charset=utf8', 'root', '');

        $insertUser = $bdd->prepare('INSERT INTO user(email, password) VALUES(?,?)');

        return $insertUser->execute([
            $userToSave->getEmail(),
            $userToSave->getPassword()
        ]);
    }

}
   */ 
