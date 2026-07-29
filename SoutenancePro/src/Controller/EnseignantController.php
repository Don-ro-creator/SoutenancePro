<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\User;
use App\Form\EnseignantType;
use App\Repository\EnseignantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/enseignants')]
#[IsGranted('ROLE_ADMIN')]
class EnseignantController extends AbstractController
{
    #[Route('', name: 'app_enseignant_index', methods: ['GET'])]
    public function index(EnseignantRepository $repo): Response
    {
        return $this->render('enseignant/index.html.twig', [
            'enseignants' => $repo->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_enseignant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $enseignant = new Enseignant();
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user = new User();
                $user->setEmail($enseignant->getEmail());
                $user->setNom($enseignant->getNom());
                $user->setPrenom($enseignant->getPrenom());
                $user->setRoles(['ROLE_ENSEIGNANT']);
                $user->setPassword($hasher->hashPassword($user, $plainPassword));
                $enseignant->setUser($user);
                $em->persist($user);
            }
            $em->persist($enseignant);
            $em->flush();
            $this->addFlash('success', 'Enseignant ajouté avec succès.');
            return $this->redirectToRoute('app_enseignant_index');
        }
        return $this->render('enseignant/form.html.twig', [
            'form' => $form,
            'title' => 'Nouvel enseignant',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_enseignant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enseignant $enseignant, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                if ($enseignant->getUser()) {
                    $user = $enseignant->getUser();
                    $user->setPassword($hasher->hashPassword($user, $plainPassword));
                } else {
                    $user = new User();
                    $user->setEmail($enseignant->getEmail());
                    $user->setNom($enseignant->getNom());
                    $user->setPrenom($enseignant->getPrenom());
                    $user->setRoles(['ROLE_ENSEIGNANT']);
                    $user->setPassword($hasher->hashPassword($user, $plainPassword));
                    $enseignant->setUser($user);
                    $em->persist($user);
                }
            }
            $em->flush();
            $this->addFlash('success', 'Enseignant modifié.');
            return $this->redirectToRoute('app_enseignant_index');
        }
        return $this->render('enseignant/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier l\'enseignant',
        ]);
    }

    #[Route('/{id}', name: 'app_enseignant_delete', methods: ['POST'])]
    public function delete(Request $request, Enseignant $enseignant, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $enseignant->getId(), $request->request->get('_token'))) {
            if (count($enseignant->getAllSoutenances()) > 0) {
                $this->addFlash('danger', 'Impossible de supprimer : cet enseignant participe à des soutenances.');
            } else {
                if ($enseignant->getUser()) {
                    $em->remove($enseignant->getUser());
                }
                $em->remove($enseignant);
                $em->flush();
                $this->addFlash('success', 'Enseignant supprimé.');
            }
        }
        return $this->redirectToRoute('app_enseignant_index');
    }
}
