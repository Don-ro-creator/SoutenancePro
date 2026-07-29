<?php

namespace App\Controller;

use App\Repository\SoutenanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ENSEIGNANT')]
class EnseignantSoutenanceController extends AbstractController
{
    #[Route('/enseignant/mes-soutenances', name: 'app_enseignant_soutenances')]
    public function mesSoutenances(SoutenanceRepository $repo): Response
    {
        $enseignant = $this->getUser()->getEnseignant();
        $soutenances = $enseignant ? $repo->findByEnseignant($enseignant) : [];
        return $this->render('enseignant/mes_soutenances.html.twig', [
            'soutenances' => $soutenances,
        ]);
    }

    #[Route('/enseignant/mes-jurys', name: 'app_enseignant_jurys')]
    public function mesJurys(SoutenanceRepository $repo): Response
    {
        $enseignant = $this->getUser()->getEnseignant();
        $soutenances = $enseignant ? $repo->findByEnseignant($enseignant) : [];
        return $this->render('enseignant/mes_jurys.html.twig', [
            'soutenances' => $soutenances,
            'enseignant' => $enseignant,
        ]);
    }
}
