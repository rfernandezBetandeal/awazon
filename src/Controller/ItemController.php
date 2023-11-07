<?php

namespace App\Controller;

use App\Entity\Coment;
use App\Entity\Image;
use App\Entity\Item;
use App\Form\ImageType;
use App\Form\ItemType;
use App\Form\NewComentType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/item/{url}', name: 'app_item')]
    public function index($url, Request $request): Response
    {
        $item = $this->em->getRepository(Item::class)->findByUrl($url);
        

        $itemId = $item[0]['id'];

        $coments = $this->em->getRepository(Coment::class)->findComents($itemId);

        $coment = new Coment();
        $newComent = $this->createForm(NewComentType::class, $coment);

        $newComent->handleRequest($request);

        if( $newComent->isSubmitted() && $newComent->isValid() ) 
        {
            
            $coment->setComent($newComent->get('coment')->getData());
            $coment->setItem($this->em->getRepository(Item::class)->find($itemId));
            $coment->setUser($this->getUser());

            $this->em->persist($coment);
            $this->em->flush();

            return $this->redirectToRoute("app_item", ['url' => $url]);

        }

        return $this->render('item/item.html.twig', [
            'item' => $item,
/*             'custom_item' => $custom_item,
 */         'coments' => $coments,
            'newComent' => $newComent->createView(),
        ]);
    }

    #[Route('/create/item', name: 'create_item')]
    public function create(Request $request): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);

        /* $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image); */

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $i = 1;

            $url = str_replace('|','', $form->get('name')->getData());
            $url = str_replace('.','', $url);
            $url = str_replace(',','', $url);
            $url = str_replace(' ', '-', $url);
            $url = str_replace('--','-', $url);
            $url = str_replace('---','-', $url);
            
            $url = strtolower($url);

            while($this->em->getRepository(Item::class)->findByUrl($url)){

                if(str_contains($this->em->getRepository(Item::class)->findByUrl($url)[0]['url'], $i) ){
                    str_replace($i, '', $url);
                }

                $url .= $i;
                $i++;
            }

            $item->setUrl($url);

            $this->em->persist($item);
            $this->em->flush();

            return $this->redirectToRoute('create_item');
        }

        return $this->render('item/index.html.twig', [
            'form' => $form->createView(),
            /* 'imageForm' => $imageForm->createView(), */
        ]);
    }

    #[Route('/edit/item/{id}', name: 'edit_item')]
    public function edit(Request $request, $id): Response
    {

        $item = $this->em->getRepository(Item::class)->find($id);

        $form = $this->createForm(ItemType::class, $item);

        /* $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image); */

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $i = 1;

            $url = str_replace('|','', $form->get('name')->getData());
            $url = str_replace('.','', $url);
            $url = str_replace(',','', $url);
            $url = str_replace(' ', '-', $url);
            $url = str_replace('--','-', $url);
            $url = str_replace('---','-', $url);
            
            $url = strtolower($url);

            while($this->em->getRepository(Item::class)->findByUrl($url)){

                if(str_contains($this->em->getRepository(Item::class)->findByUrl($url)[0]['url'], $i) ){
                    str_replace($i, '', $url);
                }

                $url .= $i;
                $i++;
            }

            $item->setUrl($url);

            $this->em->flush();

            return $this->redirectToRoute('edit_item', ['id'=> $item->getId()]);
        }

        return $this->render('item/remove.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            /* 'imageForm' => $imageForm->createView(), */
        ]);
    }

    #[Route('/edit/item', name: 'edit_list')]
    public function editList(PaginatorInterface $paginator, Request $request): Response
    {

/*         $items = $this->em->getRepository(Item::class)->findAll();
 */
        $query = $this->em->getRepository(Item::class)->listEdits();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('item/edit.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/remove/item/{id}', name: 'remove_item')]
    public function remove($id): Response
    {

        $item = $this->em->getRepository(Item::class)->find($id);

            $this->em->remove($item);
            $this->em->flush();
 
            return $this->redirectToRoute('edit_list');
    }

    /* #[Route('/insert/item', name:'insert_item')]
    public function insert()
    {
        $desc = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Eligendi quidem atque aspernatur recusandae? Maxime, minus ex! Sint dolor velit voluptatibus accusantium nemo beatae harum adipisci, dicta totam obcaecati sapiente eius.";
        $item = new Item("Segundo insert", "M", 9.99, "BNA", $desc); 



        $this->em->persist($item);
        $this->em->flush();

        return new JsonResponse(["success"=> true]);
    } */

    /* #[Route('/update/item/{id}', name:'update_item')]
    public function update($id)
    {
        $desc = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Eligendi quidem atque aspernatur recusandae? Maxime, minus ex!";
        $item = $this->em->getRepository(Item::class)->find($id);

        $item->setDescription($desc);
        $item->setPrice(5.99);
        $this->em->flush();

        return new JsonResponse(["success"=> true]);
    } */

    /* #[Route('/remove/item/{id}', name:'remove_item')]
    public function remove($id)
    {
    
        $item = $this->em->getRepository(Item::class)->find($id);
        $this->em->remove($item);
        $this->em->flush();

        return new JsonResponse(["success"=> true]);
    } */
}
