<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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


    public function searchSorties($criteres): array
    {
        $qb = $this->createQueryBuilder('s');
        $user_session=$this->tokenStorage->getToken()->getUser()->getId();
        dump($user_session);


            if ($criteres['site'] != '' && $criteres['site'] != NULL){
                $qb ->leftJoin('s.site','site')
                    ->Where('site.id = :site')
                    ->setParameter('site', $criteres['site']);
            }

            $qb->andWhere('s.nom like :nom' )
            ->setParameter('nom', '%'.$criteres['nom'].'%');

            if ($criteres['dateMin'] != '' && $criteres['dateMin'] != NULL){
                $qb->andWhere('s.dateHeureDebut > :dateMin' )
                    ->setParameter('dateMin', $criteres['dateMin']);
            };

            if ($criteres['dateMax'] != '' && $criteres['dateMax'] != NULL){
                $qb->andWhere('s.dateHeureDebut < :dateMax' )
                    ->setParameter('dateMax', $criteres['dateMax']);
            };


            if ($criteres['isOrganisateur'] == true) {
                $qb->andWhere('s.organisateur = :user_session')
                    ->setParameter('user_session', $user_session);
            };
//            if ($criteres['isInscrit'] ==true) {
//                $qb ->leftJoin('s.users','users')
//                    ->andWhere('users.')
//                    ->setParameter('', $user_session);
//            };
//            if ($criteres['isNotInscrit'] ==true) {
//                $qb->andWhere('')
//                    ->setParameter('dateMax',$user_session);
//            };
//            if ($criteres['sortiesPassees'] ==true) {
//                $qb->andWhere('s.dateHeureDebut < :dateMax')
//                    ->setParameter('dateMax', $criteres['dateMax']);
//            };


            $qb->orderBy('s.dateHeureDebut','desc')
            ;$query=$qb->getQuery();
            return $query->getResult()


        ;
    }

}
