<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeUserType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{

    private $em;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher)
    {

        $this->em = $em;
        $this->userPasswordHasher = $userPasswordHasher;

    }

    #[Route('/user/{id}', name: 'app_user')]
    public function index($id, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        $user = $this->em->getRepository(User::class)->find($id);

        $changeUserForm = $this->createForm(ChangeUserType::class, $user);
        $changeUserForm->handleRequest($request);

        if ($changeUserForm->isSubmitted() && $changeUserForm->isValid()) {
            // encode the plain password

            if(!empty($changeUserForm->get('new_password')->getData()) && $changeUserForm->get('new_password')->getData() != ""){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $changeUserForm->get('new_password')->getData()
                    )
                );
            }



            $naame = $changeUserForm->get('name')->getData();
            echo $naame;

            $user->setName($changeUserForm->get('name')->getData());
            $user->setSurname1($changeUserForm->get('surname1')->getData());
            $user->setSurname2($changeUserForm->get('surname2')->getData());
            $user->setUsername($changeUserForm->get('username')->getData());

           $entityManager->flush();

            return $this->redirectToRoute('app_user', ['id'=> $id]);
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'editForm' => $changeUserForm->createView(),
        ]);
    }

    #[Route('/register/user', name: 'userRegistration')]
    public function userRegistration(Request $request, SluggerInterface $slugger): Response
    {

        $user = new User();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);

        $registrationForm->handleRequest($request);

        if( $registrationForm->isSubmitted() && $registrationForm->isValid() ) 
        {
            $profilePicture = $registrationForm->get('profile_picture')->getData();

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

                $user->setProfilePicture($newFileName);
            }

            $user->setRoles(['ROLE_USER']);

            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $registrationForm->get('plainPassword')->getData()
                )
            );

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute("app_index");

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $registrationForm->createView(),
        ]);
        
    }

    #[Route('/change/user/{id}', name: 'changeUser')]
    public function changeUser($id, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager){

        $user = $this->em->getRepository(User::class)->find($id);

        $changeUserForm = $this->createForm(ChangeUserType::class, $user);
        $changeUserForm->handleRequest($request);

        if ($changeUserForm->isSubmitted() && $changeUserForm->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $changeUserForm->get('plainPassword')->getData()
                )
            );

            $user->setName($changeUserForm->get('name')->getData());
            $user->setEmail($changeUserForm->get('email')->getData());
            $user->setSurname1($changeUserForm->get('surname1')->getData());
            $user->setSurname2($changeUserForm->get('surname2')->getData());
            $user->setUsername($changeUserForm->get('username')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->redirectToRoute('app_user');

    }

}
