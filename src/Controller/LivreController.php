<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Livre; //pour que l'on puisse l'utiliser sans avoir à écrire à chaque fois
use App\Form\LivreType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use App\Repository\LivreRepository;
use Doctrine\ORM\Query\Expr\From;

/**
     * @Route("/admin")
     */

class LivreController extends AbstractController
{
    /**
     * @Route("/livre", name="livre")
     */
    public function index(LivreRepository $lr): Response
    {
        return $this->render('livre/index.html.twig', [
            //findAll() est une méthode qui retourne tous les livres
            "livres"=> $lr->findAll(),
            "livres_empruntes"=>$lr->livresEmpruntes()
        ]);
    }

    /**
     * @Route("/mes-livres", name="livre_mes_livres")
     */
    public function mesLivres(): Response
    {
        $mesLivres = [
            [ "titre"=> "Dune", "auteur"=> "Frank Herbert"],
            [ "titre"=> "1984", "auteur"=> "George Orwell"],
            [ "titre"=> "Le Seigneur des anneaux", "auteur"=> "J.R.R Tolkien"]
        ];

        return $this->render('livre/meslivres.html.twig', ["livres"=> $mesLivres]);
    }

    /**
     * @Route("/livre/ajouter", name="livre_ajouter")
     * Pour instancier un objet de la classe Request, on va utiliser l'injection de dépendance.
     * On définit un paramètre dans une méthode d'un controller de la classe Request et dans cette méthode, on pourra utiliser l'objet, qui contiendra des propriétés avec toutes les valeurs des superglobales de PHP
     * Ex: 
     * $request->query : cette propriété est l'objet qui a les valeurs $_GET
     * $request->request : cette propriété qui a les valeurs de $_POST
     */

    public function ajouter(Request $request, EntityManager $em, CategorieRepository $cr){
        if($request->isMethod("POST")){
            $titre = $request->request->get("titre") ;// la méthode 'get' permet de récuprer les valeurs des inputs du formulaire
            $auteur = $request->request->get("auteur");// le paramètre passé à 'get' est le name de l'input
            $categorie_id = $request->request->get("categorie");

            if($titre && $auteur){ //si $titre et $auteur ne sont pas vides alors:
                $nouveauLivre = new Livre;// on a utilisé use tout en haut pour éviter de l'écrire en entier à chaque fois
                $nouveauLivre->setTitre($titre);
                $nouveauLivre->setAuteur($auteur);
                $nouveauLivre->setCategorie($cr->find($categorie_id));
                /* On va utiliser l'objet $em de la classe EntityManager pour enregistrer en BDD
                * La méthode persist permet de préparer une requête INSERT INTO. 
                * Le paramètre DOIT être un objet d'une classe Entity.
                */
                $em->persist($nouveauLivre);
                $em->flush();
                /* La méthode 'flush' exécute toutes les requêtes en attente. La BDD est modifiée quand cette méthode est lancée (et pas avant) */

                return $this->redirectToRoute("livre");//permet de retourner à la page livre après l'ajout d'un nouveau livre
            }

        }
        
        
        //EXO:
        //La route doit afficher un formulaire pour pouvoir ajouter un livre
        //Ajouter un lien dans le menu pour accéder à cette route
        //dump($request); comme un var dump mais en mieux
        return $this->render("livre/formulaire.html.twig",["categories" => $cr->findAll()] );
    }

    /**
     * @Route("/livre/modifier/{id}", name="livre_modifier")
      */
    public function modifier(EntityManager $em, Request $request,LivreRepository $lr, $id){
        $livre = $lr->find($id);//find retourne l'objet Livre dont l'id vaut $id en BDD
        $form = $this->createForm(LivreType::class, $livre);
        /* 'createForm' va créer un objet représentant le formulaire créé à partir de la classe LivreType
        Le 2ème paramètre est l'objet Entity qui sera lié au formulaire
        */
    
        $form->handleRequest($request);
        /* La méthode 'handleRequest' permet à $form de gérer les informations venant de la requete HTTP
        Ex: est-ce que le formulaire a été soumis ?...
        */
        if($form->isSubmitted()&& $form->isValid()){// Si le formulaire a été soumis et s'il est valide...

           if($fichier = $form->get("couverture")->getData()){
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);// on récupère le nom du fichier qui a été téléversé

                //On remplace les espaces par des _ :
                $nomFichier = str_replace(" ", "_", $nomFichier);

                //on ajout un string unique au nom du fichier pour éviter les doublons et l'eextension du fichier:
                $nomFichier .= uniqid(). "." . $fichier->guessExtension();

                //on copie le fichier uploadé dans un dossier du dossier "public" avec le nouveau nom de fichier:
                $fichier->move($this->getParameter("dossier_images"), $nomFichier);
                $livre->setCouverture($nomFichier);
           } 

            $em->flush();
            /*Toutes les modifications des objets Entity qui ont été instanciés à partir de la bdd vont être enregistrées en bdd quand on va utiliser $em->flush()*/

            return $this->redirectToRoute("livre");

        }
        return $this->render("livre/form.html.twig", ["formLivre"=> $form->createView()]);
    }

    /**
     * @Route("/livre/supprimer/{id}", name="livre_supprimer")
      */
    public function supprimer(Request $request, EntityManager $em, Livre $livre ){
        /* Si le paramètre placé dans le chemin est une propriété d'une classe Entity on peut récupérer directement l'objet dont la propriété vaut ce qui sera passé dans l'URL ($livre contiendra le livre dont l'id sera passé dans l'URL) */

        //dd($livre); //dump & die: var_dump et l'exécution du code est arrêté

        if($request->isMethod("POST")){
            $em->remove($livre); //la requête DELETE est en attente
            $em->flush();//toutes les requêtes en attente sont executées
            return $this->redirectToRoute("livre");
        }
        return$this->render("livre/supprimer.html.twig", ["livre" => $livre ]);
    }
    
    
    /**
     * @Route("/livre/fiche/{id}", name="livre_fiche")
      */

    public function fiche(Livre $livre){

        return $this->render("livre/fiche.html.twig", compact("livre"));
        //La fonction compact() de PHP retourne un array associatif à partir des variables qui ont les mêmes noms que les paramètres passés à compact()
        //Par exemple, si j'ai 2 variables
        //$nom = "Ateur";
        //$prenom = "Nordine";
        //$personne = compact("nom", "prenom");
        // est équivalent à :
        //$personne = ["nom"=> "Ateur", "prenom"=> "Nordine"];
    }

    /**
     * @Route("/livre/nouveau", name="livre_nouveau")
      */
    public function nouveau(EntityManager $em, Request $request, LivreRepository $lr){
        
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($fichier = $form->get("couverture")->getData()){
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nomFichier = str_replace(" ", "_", $nomFichier);
                $nomFichier .= uniqid(). "." . $fichier->guessExtension();
                $fichier->move($this->getParameter("dossier_images"), $nomFichier);
                $livre->setCouverture($nomFichier);
            } 
            $em->persist($livre);
            $em->flush();
            $this->addFlash("success", "Le nouveau livre a été enregistré");
            return $this->redirectToRoute("livre");
        }
        return $this->render("livre/form.html.twig", ["formLivre"=> $form->createView()]);
    }
}

