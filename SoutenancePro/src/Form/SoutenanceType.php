<?php

namespace App\Form;

use App\Entity\Soutenance;
use App\Entity\Etudiant;
use App\Entity\Enseignant;
use App\Entity\Salle;
use App\Repository\EtudiantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoutenanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => function (Etudiant $e) {
                    return $e->getPrenom() . ' ' . $e->getNom() . ' (' . $e->getFiliere() . ')';
                },
                'label' => 'Étudiant',
                'query_builder' => function (EtudiantRepository $repo) use ($options) {
                    if ($options['edit_mode']) {
                        return $repo->createQueryBuilder('e')->orderBy('e.nom', 'ASC');
                    }
                    return $repo->createQueryBuilder('e')
                        ->leftJoin('e.soutenance', 's')
                        ->where('s.id IS NULL')
                        ->orderBy('e.nom', 'ASC');
                },
            ])
            ->add('president', EntityType::class, [
                'class' => Enseignant::class,
                'choice_label' => fn(Enseignant $e) => $e->getPrenom() . ' ' . $e->getNom(),
                'label' => 'Président du jury',
            ])
            ->add('rapporteur', EntityType::class, [
                'class' => Enseignant::class,
                'choice_label' => fn(Enseignant $e) => $e->getPrenom() . ' ' . $e->getNom(),
                'label' => 'Rapporteur',
            ])
            ->add('examinateur', EntityType::class, [
                'class' => Enseignant::class,
                'choice_label' => fn(Enseignant $e) => $e->getPrenom() . ' ' . $e->getNom(),
                'label' => 'Examinateur',
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => fn(Salle $s) => $s->getCode() . ' - ' . $s->getLocalisation(),
                'label' => 'Salle',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
            ])
            ->add('heure', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Soutenance::class,
            'edit_mode' => false,
        ]);
    }
}
