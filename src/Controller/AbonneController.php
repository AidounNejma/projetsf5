<?php

namespace App\Controller;

use App\Entity\Abonne;
use App\Form\AbonneType;
use App\Repository\AbonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/abonne")
 */
class AbonneController extends AbstractController
{
    /**
     * @Route("/", name="abonne_index", methods={"GET"})
     */
    public function index(AbonneRepository $abonneRepository): Response
    {
        return $this->render('abonne/index.html.twig', [
            'abonnes' => $abonneRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="abonne_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $abonne = new Abonne();
        $form = $this->createForm(AbonneType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $mdp =$form->get("password")->getData();//il faut utiliser getdata pr avoir la value de l'input password , ensuite on va encoreder le mdp
            //On utiliser userpasswordhasherinterfce pr hasher le mdp
            $mdp=$hasher->hashPassword($abonne, $mdp); //permet d'encoder (et non le cryptage, c'est different) le mdp
            $abonne->setPassword($mdp);

            $entityManager->persist($abonne); //on l'utilise que qd on fait un insert into
            $entityManager->flush();
            $this->addFlash("success", "L'abonné a été ajouté");

            return $this->redirectToRoute('abonne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonne/new.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="abonne_show", methods={"GET"})
     */
    public function show(Abonne $abonne): Response
    {
        return $this->render('abonne/show.html.twig', [
            'abonne' => $abonne,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="abonne_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,  UserPasswordHasherInterface $hasher, Abonne $abonne): Response 
    {
        $form = $this->createForm(AbonneType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //cette partie permet de modifier le mdp de l'abonné et on peut voir la modification
            $entityManager = $this->getDoctrine()->getManager();
            $mdp =$form->get("password")->getData();//il faut utiliser getdata pr avoir la value de l'input password , ensuite on va encoreder le mdp
            
            if($mdp){//Si le mdp n'est pas vide:
            //On utilise userpasswordhasherinterfce pr hasher le mdp
            $mdp=$hasher->hashPassword($abonne, $mdp); //permet d'encoder (et non le cryptage, c'est different) le mdp
            $abonne->setPassword($mdp);
            }
           

            $this->getDoctrine()->getManager()->flush(); //flush execute les requetes en attnete qui étaient en attente : enregistrement de la modification
            $this->addFlash("success", "L'abonné a été modifié");

            return $this->redirectToRoute('abonne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonne/edit.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="abonne_delete", methods={"POST"})
     */
    public function delete(Request $request, Abonne $abonne): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonne->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($abonne);
            $entityManager->flush();
        }

        return $this->redirectToRoute('abonne_index', [], Response::HTTP_SEE_OTHER);
    }
}
