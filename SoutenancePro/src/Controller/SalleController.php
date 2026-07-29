<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/salles')]
#[IsGranted('ROLE_ADMIN')]
class SalleController extends AbstractController
{
    #[Route('', name: 'app_salle_index', methods: ['GET'])]
    public function index(SalleRepository $repo): Response
    {
        return $this->render('salle/index.html.twig', [
            'salles' => $repo->findBy([], ['code' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_salle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($salle);
            $em->flush();
            $this->addFlash('success', 'Salle ajoutée avec succès.');
            return $this->redirectToRoute('app_salle_index');
        }
        return $this->render('salle/form.html.twig', [
            'form' => $form,
            'title' => 'Nouvelle salle',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_salle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salle $salle, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Salle modifiée.');
            return $this->redirectToRoute('app_salle_index');
        }
        return $this->render('salle/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier la salle',
        ]);
    }

    #[Route('/{id}', name: 'app_salle_delete', methods: ['POST'])]
    public function delete(Request $request, Salle $salle, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $salle->getId(), $request->request->get('_token'))) {
            if (count($salle->getSoutenances()) > 0) {
                $this->addFlash('danger', 'Impossible de supprimer : cette salle est utilisée pour des soutenances.');
            } else {
                $em->remove($salle);
                $em->flush();
                $this->addFlash('success', 'Salle supprimée.');
            }
        }
        return $this->redirectToRoute('app_salle_index');
    }
}
