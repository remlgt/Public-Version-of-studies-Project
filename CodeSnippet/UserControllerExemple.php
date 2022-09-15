<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\ImageUploader;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController



/**     Route pour l'affichage / gestion / suppresion du profil     */
/**     Road to show / update / delete an user     */

{/**
     * @Route("profil", name="user_profil", methods={"GET"})
     */
    public function show(UserRepository $userRepository): Response
    {
        $user=$this->getUser();
       
        // $FiveBeers=$userRepository->findFiveBeersBydatewithUser();
        $beers=$user->getBeers();
        $events=$user->getActualities();
        $region=$user->getRegiontolive();

        //dd($user->getBeers());

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'beers' => $beers,
            'events'=> $events,
            'region'=>$region,
            // 'FiveBeers'=>$FiveBeers
        ]);
    }

    /**
     * @Route("profil/{id}/delete", name="user_delete", methods={"GET|POST"})
     */
    public function delete(Request $request, UserRepository $userRepository, User $user): Response
    {
        
        $user=$this->getUser();
        
        $this->denyAccessUnlessGranted('USER_DELETE', $user, "Err403.  Vous n'avez pas les droits pour être içi.     ");
        $submitedToken = $request->query->get('_token') ?? $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $submitedToken)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('home');

    }

   
    /**
     * @Route("profil/update", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher,ImageUploader $imageUploader): Response
    {
        $user=$this->getUser();

        $this->denyAccessUnlessGranted('USER_EDIT', $user, "Err403.  Vous n'avez pas les droits pour être içi.     ");
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // On appelle l'image uploader afin de récup la pp

            $newFileName = $imageUploader->upload($form, 'image');
            $user->setPictureProfil($newFileName);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On récupère le mot de passe en clair
            $plainPassword = $form->get('password')->getData();

            // On hash le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plainPassword
            );
            // on met à jour la propriété 'password' avec le nouveau
            // mot de passe hashé
            $user->setPassword($hashedPassword);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profil') ;
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

/**     Route pour l'affichage / listage des brasseur     */
/**     Road to show / listing brewers     */
/**
     * @Route("/brewers", name="brewers_list", methods={"GET"})
     */
    public function indexBrewers(UserRepository $userRepository): Response
    {
        return $this->render('brewers/index.html.twig', [
            'brewers' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("brewers/{id}", name="brewers_profil", methods={"GET"})
     */
    public function showBrewers(User $user,UserRepository $userRepository): Response
    {
        // $FiveBeers=$userRepository->findFiveBeersBydatewithUser();
        $beers=$user->getBeers();
        $events=$user->getActualities();

        return $this->render('brewers/details.html.twig', [
            'user' => $user,
            'beers' => $beers,
            'events'=> $events,
            // 'FiveBeers'=>$FiveBeers
        ]);
    }

    
/** */
/** Route pour la gestion des actualités pour un User */
/** */


/**
     * @Route("/user/actu/list", name="user_actu_list", methods={"GET"})
     */
    public function indexUserActu(UserRepository $userRepository): Response
    {

        $user=$this->getUser();

        $events=$user->getActualities();


        return $this->render('user/actulist.html.twig', [
            'events'=> $events,
            
        ]);
    }

}



