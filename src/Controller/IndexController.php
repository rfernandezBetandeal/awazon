<?php

namespace App\Controller;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $item = $this->em->getRepository(Item::class)->findAllItems();

        return $this->render('index/index.html.twig', [
            'item' => $item,
        ]);
    }
}
