<?php

namespace App\Command;

use App\Entity\Etat;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangeStateCommand extends Command
{
    protected static $defaultName = 'app:change-state';

    /**
     * ChangeStateCommand constructor.
     */
    public function __construct(EntityManagerInterface $em, SortieRepository $sr)
    {
        parent::__construct();
        $this->em = $em;
        $this->sr = $sr;

    }


    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $etatRepository = $this->em->getRepository('App:Etat');

        $today = new \DateTime('now');

        $sorties = $this->sr->findAll();


        foreach ($sorties as $sortie) {
            $nomSortie = $sortie->getNom();
            $interval = new \DateInterval('P1M');



                if ($sortie->getDateLimiteInscription() < $today) {
//
                    $etat = $etatRepository->find(2);
                    $sortie->setEtat($etat);
                    $this->em->persist($sortie);
                    $this->em->flush();


                }
                if ($sortie->getDateHeureDebut() < $today) {
//
                    $etat = $etatRepository->find(5);
                    $sortie->setEtat($etat);
                    $this->em->persist($sortie);
                    $this->em->flush();

                }
                if ($sortie->getDateHeureDebut()->add($interval) < $today) {
//
                    $etat = $etatRepository->find(7);
                    $sortie->setEtat($etat);
                    $this->em->persist($sortie);
                    $this->em->flush();

                }


            $io->success('état : ' . $etat->getLibelle() . ' | nom sortie : ' . $nomSortie
                . ' | date de début : ' .$sortie->getDateHeureDebut()->format('d/m/Y')
                . ' | Date de debut + 1MOIS : '.$sortie->getDateHeureDebut()->add($interval)->format('d/m/Y')
                . ' | today : ' . $today->format('d/m/Y')


            );

//                $io->success('L\' état de la sortie "' . $nomSortie . '" a bien été mis à jour ');

        }
    }

}