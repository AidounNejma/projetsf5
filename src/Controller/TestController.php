<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Response as FlexResponse;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     * C'est la première route de notre projet
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'you shall not pass !',
            'path' => 'src/Controller/TestController.php',
        ]);
    }

    /**
     * @Route("/test/nouvelle-route", name="test_nouvelle")
     * 
     * attention : il ne peut pas y avoir 2 routes avec le meme name 
     */

    public function nouvelleRoute(): Response
    {
        return $this->render( "base.html.twig", ["prenom" => "Didier"]);
        /*la methode render permet de generer l'affichage d'un fichier vue qui se trouve dans le dossier template - render est issu de la class 
        abstractcontroller. 
        Le 1er parametre est le nom du fichier 
        le 2eme parametre n'est pas obligatoire  il doit etre de type array et contiendra ttes les variables que lon veut trnasmettre à la vue */
    }

    /**
     * @Route("/test/tableau", name="test_tableau") 
     */
    public function tableau()
    {
        $tableau = ["un", 2, true];
        $tableau2 = ["nom" => "Aidoun", "prenom" => "Nejma", "age" => 26 ];
        return $this->render("test/tableau.html.twig", ["tableau" =>$tableau, "personne" =>$tableau2]);

        //exo : je veux transmettre la valeur de la varibale $tableau2 à ma vue  ds une variable nommée "personne", ensuite afficher : je m'appelle suivi du prenom nom age. 
    }

    /**
     * @Route("/test/objet") 
     */
    public function objet()
    {
        $objet = new \stdClass();
        $objet -> nom = "Mentor";
        $objet -> prenom = "Gérard";
        $objet -> age = "54";

        return $this->render("test/tableau/html.twig", ["personne" => $objet]);
    }

    /**
     * @Route("/test/salut/{prenom}") 
     * 
     * dans le chemin les {} signifient que cette partie du chemin est variable. Ca peut etre n'importe quel chaine de caracteres. le nom mis entre {} est le nom de la variable passé en parametre
     */

    public function prenom($prenom)
    {
        return $this->render("base.html.twig", ["prenom" => $prenom]);
    }

    /*
    exo1 : vous allez ajouter une route , "/test/liste/{nombre}"
    Le nombre passé en parametre devra etre envoyé à une vue qui etend base.html.twig , 
    Cette vue va afficher la liste des nombres de 1 jusqu'au nombre passé dans le chemin ds une table html
    Dans la 1ere colonne , le nombre 
    Dans la 2eme colonne : le nombre multiplié par 2
    */

    /**
     * @Route("/test/liste/{nombre}") 
     */
    public function nombre($nombre)
    {
        return $this->render("test/liste.html.twig", ["nombre" => $nombre]);
    }
    ###########################################################

    /*
    exo2 : creer une nvelle route qui prend un nb ds l'url et qui affiche le resultat de ce nb au carré

    exo  3 : Créer un contrôleur Livre

Dans ce contrôleur, créer une route "/mes-livres"

Dans la méthode (nommée mesLivres par exemple), créer une variable array qui va
contenir des arrays
titre = "Dune", auteur = "Frank Herbert"
titre = "1984", auteur = "George Orwell"
titre = "Le Seigneur des Anneaux", auteur = "J.R.R. Tolkien"

Donc dans la variable $liste, il aura 3 valeurs, 
	chaque valeur est un tableau avec un indice "titre" et un indice "auteur"
    
Afficher la liste des livres dans une table HTML

    */

   
}

