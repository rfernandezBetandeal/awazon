<?php

namespace App\Controller;

use App\Entity\Coment;
use App\Entity\Image;
use App\Entity\Item;
use App\Form\ImageType;
use App\Form\ItemType;
use App\Form\NewComentType;
use App\Form\SearchType;
use App\Form\SearchUserType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        $item = new Item();

        $search = $this->createForm(SearchType::class, $item);

        $search->handleRequest($request);

        if($search->isSubmitted()){

            $id = $search->get('id')->getData();
            $name = $search->get('name')->getData();
            $url = $search->get('url')->getData();


            $query = $this->em->getRepository(Item::class)->findSearch($id, $name, $url);

            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
            
        }else{

            $query = $this->em->getRepository(Item::class)->listEdits();

            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );

        }

        return $this->render('item/edit.html.twig', [
            'pagination' => $pagination,
            'search' => $search->createView(),
        ]);
    }

    #[Route('/remove/item/{id}', name: 'remove_item')]
    public function remove($id): Response
    {

        $item = $this->em->getRepository(Item::class)->find($id);

            $this->em->remove($item);
            $this->em->flush();
 
            return $this->redirectToRoute('edit_list');
    }

    #[Route('/search', name: 'search')]
    public function search(PaginatorInterface $paginator, Request $request): Response
    {

        $item = new Item();

        $search = $this->createForm(SearchUserType::class, $item);
        $categories = $this->em->getRepository(Item::class)->findCategories();
        $images = $this->em->getRepository(Image::class)->findCardsImages();

        $search->handleRequest($request);

        if($search->isSubmitted()){

            $name = $search->get('name')->getData();

            $query = $this->em->getRepository(Item::class)->findSearch(name: $name);

            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                30 /*limit per page*/
            );
            
        }else{

            $query = $this->em->getRepository(Item::class)->findAllSearch();

            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                30 /*limit per page*/
            );

        }

        return $this->render('item/search.html.twig', [
            'images' => $images,
            'categories'=> $categories,
            'pagination' => $pagination,
            'search' => $search->createView(),
        ]);
 
    }

    #[Route('/search/{category}', name: 'searchCategory')]
    public function searchCategory($category, PaginatorInterface $paginator, Request $request): Response
    {

        $categories = $this->em->getRepository(Item::class)->findCategories();

        $item = new Item();
        $search = $this->createForm(SearchUserType::class, $item);

        $search->handleRequest($request);

        if($search->isSubmitted()){

            $name = $search->get('name')->getData();

            $query = $this->em->getRepository(Item::class)->findSearch(name: $name);

            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                30 /*limit per page*/
            );
            
        }else{

            $query = $this->em->getRepository(Item::class)->findBy(['category' => $category]);

            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                30 /*limit per page*/
            );

        }
 
        return $this->render('item/search.html.twig', [
            'categories' => $categories,
            'pagination' => $pagination,
            'search' => $search->createView(),
        ]);
    }

    #[Route('/insert/image/{itemId}', name: 'insertImage')]
    public function insertImage(Request $request, SluggerInterface $slugger, $itemId)
    {

            $item = $this->em->getRepository(Item::class)->find($itemId);

            $images = $item->getImages();

            $image = new Image();
            $imageForm = $this->createForm(ImageType::class, $image);

            $imageForm->handleRequest($request);

            if($imageForm->isSubmitted()){

                $profilePicture = $imageForm->get('route')->getData();

                if($profilePicture)
                {
                    $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFileName = $slugger->slug($originalFilename);
                    $newFileName = $safeFileName.'-'.uniqid().'.'.$profilePicture->guessExtension();

                    try {
                        $profilePicture->move(
                            $this->getParameter('user_files_directory'),
                            $newFileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        throw new \Exception($e->getMessage());
                    }

                    $image->setRoute($newFileName);
                }

                $image->setItem($item);

                    $item->setPortada($newFileName);

                $this->em->persist($image);
                $this->em->flush();

            }

            return $this->render('item/image.html.twig', [
                'imageForm' => $imageForm->createView(),
                'item' => $item,
                'images' => $images,
            ]);
 
    }

    #[Route('/remove/image/{id}/{itemId}', name: 'removeImage')]
    public function removeImage($id, $itemId): Response
    {

        $image = $this->em->getRepository(Image::class)->find($id);

            $this->em->remove($image);
            $this->em->flush();
 
            return $this->redirectToRoute('insertImage', ['itemId'=> $itemId]);
    }
}
