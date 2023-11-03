<?php

namespace App\Controller;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/item/{id}', name: 'app_item')]
    public function index($id): Response
    {
        $item = $this->em->getRepository(Item::class)->find($id);

/*         $custom_item = $this->em->getRepository(Item::class)->findItem($id);
 */        /* $coments = $this->em->getRepository(Item::class)->findComents($id); */
        return $this->render('item/index.html.twig', [
            'item' => $item,
/*             'custom_item' => $custom_item,
 *//*             'coments' => $coments,
 */        ]);
    }
}
