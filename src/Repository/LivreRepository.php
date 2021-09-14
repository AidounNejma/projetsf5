<?php

namespace App\Repository;

use App\Entity\Emprunt;
use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    /*
     * @return Livre[] Returns an array of Livre objects
     */
    
    public function recherche($value)
    {   //SELECT * FROM livre as l WHERE l.titre LIKE "%xxx%" ORDER BY l.titre, l.auteur
        return $this->createQueryBuilder('l')//le paramètre l représente la table livre (comme un alias dans une requête SQL)
            ->where('l.titre LIKE :val')
            ->setParameter('val', "%$value%")
            ->orderBy('l.titre', 'ASC')
            ->addOrderBy("l.auteur")
            ->getQuery()
            ->getResult()
        ;
    }

    /*
     *  SELECT l.
        FROM livre l JOIN emprunt e ON e.livre_id = l.id
        WHERE date_retour IS NULL
     */
    public function livresEmpruntes()
    {  
        return $this->createQueryBuilder('l')
            ->join(Emprunt::class, "e", "WITH","e.livre = l.id")
            ->andWhere('e.date_retour IS NULL')
            ->orderBy('l.auteur')
            ->addOrderBy("l.titre")
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Livre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
