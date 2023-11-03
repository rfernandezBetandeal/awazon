<?php

namespace App\Controller;

use App\Entity\Coment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
