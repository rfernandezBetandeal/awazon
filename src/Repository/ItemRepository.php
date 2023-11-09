<?php

namespace App\Repository;

use App\Entity\Coment;
use App\Entity\Item;
use App\Entity\Image;

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

    public function findAllItems(){
        return $this->getEntityManager()
        ->createQuery("
            SELECT image.id, item.id, image.route, item.name, item.url, item.description, item.price, item.brand, item.category
            FROM App:image image
            JOIN image.item item
            GROUP BY image.item
        ")
        ->getResult();
    }

    public function findByUrl($url){
    
        return $this->getEntityManager()
        ->createQuery("
            SELECT item.id, item.name, item.description, item.brand, item.url, item.price, item.category
            FROM App:item item
            WHERE item.url = :url
        ")
        ->setMaxResults(1)
        ->setParameter("url", $url)
        ->getResult();
    
    }

    public function listEdits(){
        return $this->getEntityManager()
        ->createQuery("
            SELECT item.id, item.name, item.description, item.brand, item.url, item.price
            FROM App:item item 
        ");
    }

    public function findSearch($id = null, $name = null, $url = null){

        if($id == null && $name == null && $url == null){ 

            return $this->getEntityManager()
            ->createQuery("
                SELECT item.id, item.name, item.description, item.brand, item.url, item.price, item.category, item.important, item.portada
                FROM App:item item 
                ORDER BY item.id DESC
            ");

        }else{

            return $this->getEntityManager()
            ->createQuery("
                SELECT item.id, item.name, item.description, item.brand, item.url, item.price, item.important, item.category, item.portada
                FROM App:item item 
                WHERE item.id LIKE :id
                AND item.name LIKE :name
                AND item.url LIKE :url
                ORDER BY item.id DESC
            ")
            ->setParameter("id", $id.'%')
            ->setParameter("name", '%'.$name.'%')
            ->setParameter("url", '%'.$url.'%');

        }
    }

    public function findAllSearch(){

        return $this->getEntityManager()
        ->createQuery("
            SELECT item.id, item.name, item.description, item.brand, item.url, item.price, item.important, item.category, item.portada
            FROM App:item item 
            ORDER BY item.id DESC
        ");

    }

    public function findCategories(){

        return $this->getEntityManager()
        ->createQuery("
            SELECT item.category
            FROM App:item item 
            GROUP BY item.category
        ")->getResult();

    }

    public function findCards(){
        return $this->getEntityManager()
        ->createQuery("
            SELECT item.name, item.description, item.url, item.price, item.important, item.id, item.portada
            FROM App:item item
            ORDER BY item.id DESC
        ")
        ->setMaxResults(30)
        ->getResult();
    }

    /* public function findComents($itemId)
    {
        return $this->getEntityManager()
        ->createQuery("
            SELECT coment.id, coment.coment FROM App:coment coment WHERE item_id = :itemId
        ")
         ->setParameter("itemId", $itemId)
        ->getResult();
    } */

    /* 
    Pruebas
        public function findItem($id){
          return $this->getEntityManager()
        -> createQuery("
            SELECT item1.id, item1.name, item1.size, item1.price, item1.brand, item1.description FROM App:Item item1 JOIN App:Coment coment WHERE item1.id =:id AND coment.item_id = :id
        ")
        ->setParameter('id', $id)
        ->getResult();  

        $em = $this->getEntityManager()
        ->createQueryBuilder()
        ->select("item1.id, item1.name, item1.size, item1.price, item1.brand, item1.description")
        ->from("App:Item","item1")
        ->innerJoin("App:coment","coment")
        ->where("coment.item1 = :id")
        ->andWhere("item1.id = :id")
        ->setParameter("id", $id);

        return $em->getQuery()->getResult();
   } */

   /* public function findComents($id){
    return $this->getEntityManager()
        -> createQuery("
            SELECT coment.item1, coment.coment FROM App:Coment coment WHERE coment.item1 =:id
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
