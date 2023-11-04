<?php

namespace App\Controller;

use App\Entity\Coment;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class ComentController extends AbstractController
{
    private $emComent;

    public function __construct(EntityManagerInterface $emComent)
    {
        $this->emComent = $emComent;
    }
    #[Route('/coment/{id}', name: 'app_coment')]
    public function index($id): Response
    {
        $coment = $this->emComent->getRepository(Coment::class)->find($id);

/*         $coments = $this->emComent->getRepository(Coment::class)->findComents($id);
 */        return $this->render('coment/index.html.twig', [
            'coment' => $coment,
/*             'coments' => $coments,
 */        ]);
    }

    #[Route('/insert/coment', name: 'insert_coment')]
    public function insert()
    {
        $idUser = 1;
        $idItem = 2;
        $content = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem deserunt, doloribus inventore molestiae voluptas repellat temporibus cumque voluptate, mollitia porro iusto unde, veritatis modi accusantium error optio explicabo voluptatum eius!";

        $user = $this->emComent->getRepository(User::class)->find($idUser);
        $item1 = $this->emComent->getRepository(Item::class)->find($idItem);
        $coment = new Coment($user, $item1, $content);

        $this->emComent->persist($coment);
        $this->emComent->flush();

        return new JsonResponse(["success"=> true]);

    }

    #[Route('/update/coment/{id}', name: 'update_coment')]
    public function update($id)
    {
        $content = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem deserunt, doloribus inventore molestiae voluptas¿";

        $coment = $this->emComent->getRepository(Coment::class)->find($id);
        $coment->setComent($content);        

        $this->emComent->flush();

        return new JsonResponse(["success"=> true]);
    }

    #[Route('/remove/coment/{id}', name: 'remove_coment')]
    public function remove($id)
    {

        $coment = $this->emComent->getRepository(Coment::class)->find($id);
        $this->emComent->remove($coment);        

        $this->emComent->flush();

        return new JsonResponse(["success"=> true]);
    }
}
