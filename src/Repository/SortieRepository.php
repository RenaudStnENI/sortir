<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(RegistryInterface $registry, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, Sortie::class);
        $this->tokenStorage = $tokenStorage;
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

//    public function isNotInscrit($user_session,EntityManagerInterface $em): array
//    {
//        return $em->add("SELECT s
//            FROM sortie s
//            LEFT JOIN user_sortie us ON s.id = us.sortie_id
//            LEFT JOIN user u ON u.id = us.user_id
//            WHERE s.id not in (SELECT s.id
//            FROM sortie s
//            LEFT JOIN user_sortie us ON s.id = us.sortie_id
//            LEFT JOIN user u ON u.id = us.user_id
//            WHERE u.id = $user_session)");
//
//    }

    public function searchSorties($criteres): array
    {

        $user_session = $this->tokenStorage->getToken()->getUser()->getId();

        $qb = $this->createQueryBuilder('s');


        $champs = $qb->Where('s.nom like :nom')
            ->setParameter('nom', '%' . $criteres['nom'] . '%');


        if ($criteres['site'] != '' && $criteres['site'] != NULL) {
            $qb->leftJoin('s.site', 'site')
                ->andWhere('site.id = :site')
                ->setParameter('site', $criteres['site']);
        }

        if ($criteres['dateMin'] != '' && $criteres['dateMin'] != NULL) {
            $qb->andWhere('s.dateHeureDebut > :dateMin')
                ->setParameter('dateMin', $criteres['dateMin']);
        };

        if ($criteres['dateMax'] != '' && $criteres['dateMax'] != NULL) {
            $qb->andWhere('s.dateHeureDebut < :dateMax')
                ->setParameter('dateMax', $criteres['dateMax']);
        };

        //-----------------



        if($criteres['isOrganisateur'] or $criteres['isInscrit'] or $criteres['isNotInscrit'] or $criteres['sortiesPassees']){

//            $condition = $this->createQueryBuilder('s');

            if ($criteres['isOrganisateur']) {

                $qb->orWhere('s.organisateur = :user_session')
                    ->setParameter('user_session', $user_session);
            };
            if ($criteres['isInscrit']) {
                $qb->join('s.users', 'i', 'WITH', 'i.id = :user_session')
                    ->setParameter('user_session', $user_session);
            };

            if ($criteres['isNotInscrit']) {

                $dql = $this->createQueryBuilder('a');
                $dql->innerJoin('a.users', 'u')
                    ->where($qb->expr()->eq('u.id', $user_session));

                $qb->orWhere($qb->expr()->notIn('s.id', $dql->getDQL()));
            };

            if ($criteres['sortiesPassees']) {
                $qb->orWhere('s.dateHeureDebut < :now')
                    ->setParameter('now', new \DateTime());
            };

//            $qb->andWhere(':condition')
//            ->setParameter('condition', $condition);

        }


        $qb->orderBy('s.dateHeureDebut', 'desc');
        $query = $qb->getQuery();
        return $query->getResult();
    }

}

