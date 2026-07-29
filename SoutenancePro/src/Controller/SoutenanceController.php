<?php

namespace App\Controller;

use App\Entity\Soutenance;
use App\Form\SoutenanceType;
use App\Repository\SoutenanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/soutenances')]
#[IsGranted('ROLE_ADMIN')]
class SoutenanceController extends AbstractController
{
    #[Route('', name: 'app_soutenance_index', methods: ['GET'])]
    public function index(Request $request, SoutenanceRepository $repo): Response
    {
        $dateStr = $request->query->get('date');
        $date = $dateStr ? new \DateTime($dateStr) : null;
        return $this->render('soutenance/index.html.twig', [
            'soutenances' => $repo->findByDate($date),
            'date' => $dateStr,
        ]);
    }

    #[Route('/new', name: 'app_soutenance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SoutenanceRepository $repo): Response
    {
        $soutenance = new Soutenance();
        $form = $this->createForm(SoutenanceType::class, $soutenance, ['edit_mode' => false]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->checkConflicts($soutenance, $repo);
            if ($errors) {
                foreach ($errors as $err) {
                    $this->addFlash('danger', $err);
                }
            } else {
                $em->persist($soutenance);
                $em->flush();
                $this->addFlash('success', 'Soutenance programmée avec succès.');
                return $this->redirectToRoute('app_soutenance_index');
            }
        }
        return $this->render('soutenance/form.html.twig', [
            'form' => $form,
            'title' => 'Programmer une soutenance',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_soutenance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Soutenance $soutenance, EntityManagerInterface $em, SoutenanceRepository $repo): Response
    {
        $form = $this->createForm(SoutenanceType::class, $soutenance, ['edit_mode' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->checkConflicts($soutenance, $repo);
            if ($errors) {
                foreach ($errors as $err) {
                    $this->addFlash('danger', $err);
                }
            } else {
                $em->flush();
                $this->addFlash('success', 'Soutenance modifiée.');
                return $this->redirectToRoute('app_soutenance_index');
            }
        }
        return $this->render('soutenance/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier la soutenance',
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_soutenance_cancel', methods: ['POST'])]
    public function cancel(Request $request, Soutenance $soutenance, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('cancel' . $soutenance->getId(), $request->request->get('_token'))) {
            $em->remove($soutenance);
            $em->flush();
            $this->addFlash('success', 'Soutenance annulée.');
        }
        return $this->redirectToRoute('app_soutenance_index');
    }

    private function checkConflicts(Soutenance $s, SoutenanceRepository $repo): array
    {
        $errors = [];
        if ($repo->findConflictsSalle($s)) {
            $errors[] = 'Conflit : cette salle est déjà réservée à cette date et heure.';
        }
        if ($repo->findConflictsEnseignant($s)) {
            $errors[] = 'Conflit : un membre du jury est déjà occupé à cette date et heure.';
        }
        // Check same teacher in multiple roles
        $ids = array_filter([
            $s->getPresident()?->getId(),
            $s->getRapporteur()?->getId(),
            $s->getExaminateur()?->getId(),
        ]);
        if (count($ids) !== count(array_unique($ids))) {
            $errors[] = 'Un même enseignant ne peut pas occuper plusieurs rôles dans le même jury.';
        }
        return $errors;
    }
}
