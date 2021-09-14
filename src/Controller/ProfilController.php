<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Repository\LivreRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil_index")
     * 
     */
    public function index(): Response
    {
        /* Pour avoir les informations de l'utilisateur connecté: 
            dans twig: app.user
            dans le controller: $this->getUser()*/
        return $this->render('profil/index.html.twig');
    }


    /**
     * @Route("/profil/emprunter/{id}", name="profil_emprunter")
     * 
     */

    public function emprunter(EntityManagerInterface $em, LivreRepository $lr, Livre $livre){
        $livresEmpruntes= $lr->livresEmpruntes();
        if(in_array($livre, $livresEmpruntes)){
            $this->addFlash("danger", "Le livre <strong>" . $livre->getTitre(). "</strong> n'est pas disponible !");
            return $this->redirectToRoute("accueil");
        }
        $emprunt = new Emprunt;
        $emprunt-> setDateEmprunt(new DateTime());
        $emprunt->setLivre($livre);
        $emprunt->setAbonne($this->getUser());
    
        $em->persist($emprunt);
        $em->flush();
        return $this->redirectToRoute("profil_index");
        
}


}