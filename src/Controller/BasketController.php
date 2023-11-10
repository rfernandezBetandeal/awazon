<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em){

        $this->em = $em;

    }

    #[Route('/add/basket/{itemId}', name: 'addBasket')]
    public function addBasket($itemId){

        $item = $this->em->getRepository(Item::class)->find($itemId);
        $user = $this->getUser();
        $basket = new Basket();

        $date = new \DateTime();

        $basket->setDate($date);
        $basket->setUser($user);

        $basket->addItem($item);

        $this->em->persist($basket);
        $this->em->flush();


        return $this->render('basket/index.html.twig', [
            'item' => $item,
        ]);

    }
}
