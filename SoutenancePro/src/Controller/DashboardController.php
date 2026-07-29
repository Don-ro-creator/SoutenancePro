<?php

namespace App\Controller;

use App\Repository\EtudiantRepository;
use App\Repository\EnseignantRepository;
use App\Repository\SalleRepository;
use App\Repository\SoutenanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_dashboard_admin');
        }
        if ($this->isGranted('ROLE_ENSEIGNANT')) {
            return $this->redirectToRoute('app_dashboard_enseignant');
        }
        return $this->redirectToRoute('app_login');
    }

    #[Route('/admin/dashboard', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(
        EtudiantRepository $etudiantRepo,
        EnseignantRepository $enseignantRepo,
        SalleRepository $salleRepo,
        SoutenanceRepository $soutenanceRepo
    ): Response {
        return $this->render('dashboard/admin.html.twig', [
            'nb_etudiants' => $etudiantRepo->count([]),
            'nb_enseignants' => $enseignantRepo->count([]),
            'nb_salles' => $salleRepo->count([]),
            'nb_soutenances' => $soutenanceRepo->count([]),
        ]);
    }

    #[Route('/enseignant/dashboard', name: 'app_dashboard_enseignant')]
    #[IsGranted('ROLE_ENSEIGNANT')]
    public function enseignant(SoutenanceRepository $soutenanceRepo): Response
    {
        $user = $this->getUser();
        $enseignant = $user->getEnseignant();
        $soutenances = $enseignant ? $soutenanceRepo->findByEnseignant($enseignant) : [];

        return $this->render('dashboard/enseignant.html.twig', [
            'soutenances' => $soutenances,
            'nb_soutenances' => count($soutenances),
            'enseignant' => $enseignant,
        ]);
    }
}
