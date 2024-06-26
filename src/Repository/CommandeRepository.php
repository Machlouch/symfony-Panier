<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }




    public function countCommandesByClientId(int $clientId): int
{
    return $this->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        // Utilisez 'c.utilisateur' pour accéder à l'entité Utilisateur associée
        // Assurez-vous que la propriété dans Commande pointant vers Utilisateur est nommée 'utilisateur'
        ->andWhere('c.utilisateur = :clientId')
        ->setParameter('clientId', $clientId)
        ->getQuery()
        ->getSingleScalarResult();
}



// Dans CommandeRepository.php

/**
 * Trouve la dernière commande en cours pour un utilisateur donné.
 *
 * @param int $idUtilisateur L'identifiant de l'utilisateur.
 * @return Commande|null La dernière commande en cours si trouvée, sinon null.
 */
public function findDerniereCommandeEnCoursParUtilisateur(int $idUtilisateur): ?Commande
{
    return $this->createQueryBuilder('c')
        ->where('c.utilisateur = :idUtilisateur')
        ->andWhere('c.etat = :etat')
        ->setParameter('idUtilisateur', $idUtilisateur)
        ->setParameter('etat', 'non validé')
        ->orderBy('c.dateCommande', 'DESC') // Assure le tri des commandes par date, la plus récente d'abord
        ->setMaxResults(1) // Limite à la commande la plus récente
        ->getQuery()
        ->getOneOrNullResult();
}



public function trouverClientsFideles()
{
    $qb = $this->createQueryBuilder('c')
        ->join('c.utilisateur', 'u') // Jointure avec l'entité Utilisateur
        ->select('u.id_utilisateur AS idClient, SUM(c.totaleCommande) as totalCommande, COUNT(c.id) as nombreCommandes')
        ->groupBy('u.id_utilisateur') // Groupe par ID de l'Utilisateur en utilisant 'id_utilisateur'
        ->orderBy('totalCommande', 'DESC') // Ordonne par total des commandes décroissant
        ->addOrderBy('nombreCommandes', 'DESC'); // Ensuite, ordonne par nombre de commandes décroissant
    
    return $qb->getQuery()->getArrayResult();
}
    



//    /**
//     * @return Commande[] Returns an array of Commande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
