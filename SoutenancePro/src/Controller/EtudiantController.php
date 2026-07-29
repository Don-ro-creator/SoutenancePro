<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/etudiants')]
#[IsGranted('ROLE_ADMIN')]
class EtudiantController extends AbstractController
{
    #[Route('', name: 'app_etudiant_index', methods: ['GET'])]
    public function index(Request $request, EtudiantRepository $repo): Response
    {
        $q = $request->query->get('q');
        return $this->render('etudiant/index.html.twig', [
            'etudiants' => $repo->searchByNom($q),
            'q' => $q,
        ]);
    }

    #[Route('/new', name: 'app_etudiant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($etudiant);
            $em->flush();
            $this->addFlash('success', 'Étudiant ajouté avec succès.');
            return $this->redirectToRoute('app_etudiant_index');
        }
        return $this->render('etudiant/form.html.twig', [
            'form' => $form,
            'title' => 'Nouvel étudiant',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etudiant $etudiant, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Étudiant modifié avec succès.');
            return $this->redirectToRoute('app_etudiant_index');
        }
        return $this->render('etudiant/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier l\'étudiant',
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            if ($etudiant->getSoutenance()) {
                $this->addFlash('danger', 'Impossible de supprimer : cet étudiant a une soutenance programmée.');
            } else {
                $em->remove($etudiant);
                $em->flush();
                $this->addFlash('success', 'Étudiant supprimé.');
            }
        }
        return $this->redirectToRoute('app_etudiant_index');
    }
}
