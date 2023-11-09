<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Item;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    /* #[Route('/insert/image/{itemId}', name: 'insertImage')]
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

                $this->em->persist($image);
                $this->em->flush();

            }

            return $this->render('item/image.html.twig', [
                'imageForm' => $imageForm->createView(),
                'item' => $item,
                'images' => $images,
            ]); 
 
    }*/
}
