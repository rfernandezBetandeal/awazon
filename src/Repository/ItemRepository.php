<?php

namespace App\Repository;

use App\Entity\Coment;
use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /* 
    Pruebas
        public function findItem($id){
          return $this->getEntityManager()
        -> createQuery("
            SELECT item.id, item.name, item.size, item.price, item.brand, item.description FROM App:Item item JOIN App:Coment coment WHERE item.id =:id AND coment.item_id = :id
        ")
        ->setParameter('id', $id)
        ->getResult();  

        $em = $this->getEntityManager()
        ->createQueryBuilder()
        ->select("item.id, item.name, item.size, item.price, item.brand, item.description")
        ->from("App:Item","item")
        ->innerJoin("App:coment","coment")
        ->where("coment.item = :id")
        ->andWhere("item.id = :id")
        ->setParameter("id", $id);

        return $em->getQuery()->getResult();
   } */

   /* public function findComents($id){
    return $this->getEntityManager()
        -> createQuery("
            SELECT coment.item, coment.coment FROM App:Coment coment WHERE coment.item =:id
        ")
        ->setParameter('id', $id)
        ->getResult();
   } */

//    /**
//     * @return Item[] Returns an array of Item objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Item
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
