<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder, MailerInterface $mailer){
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user ,$user->getPassword());
            // on créer le token d'activation
            $user->setActivationToken(md5(uniqid()));

            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            /*$message =(new MailerInterface('Activation de votre compte'))
                // on attribut l'expediteur
                ->setFrom('packs-informatique@sfr.fr')
                // destinataire
                ->setTo($user->getEmail())
                //ON créer le content
                ->setBody(
                        $this->renderView(
                            'email/activation.html.twig', ['token' => $user->getActivationToken()]
                        ),
                        'text/html'
                )
                ;

                // ON envoie
                $mailer->send($message);
           */ return $this->redirectToRoute('security_login');
        }
        
      //  $message = (new MailerInterface('Activation de votre compte'));
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(){
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout() {}


    /**
    * @Route("/activation/{token}", name="activation")
    */
   public function activation ($token, UserRepository $userRepo){
       // verification du token pour l'user
    $user = $userRepo->findOneBy(['activation_token' =>$token]);
    // si pas d'user avec ce token
    if(!$user){
        // 404 Error 
        throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
    }
    // on supp le token
    $user->setActivationToken(null);
    $manager = $this->getDoctrine()->getManager();
    $manager->persist($user);
    $manager->flush();

    // Envoi msg flash
    $this->addFlash('message', 'Vous avez bien activé votre compte');

    //on retroune à l'accueil 
    return $this->redirectToRoute('site');
}

}