<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 12/03/2018
 * Time: 14:26
 */

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\User;
use App\Form\UserType;

class LoginController extends Controller
{

    /**
     * Page d'identification
     * @Route ("/login/", name="login")
     * @param Request $requete
     * @param AuthenticationUtils $authentificateur
     * @return Response
     */
    public function login(Request $requete, AuthenticationUtils $authentificateur)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute("page_accueil");
        } else {
            // message d'erreur
            $erreur = $authentificateur->getLastAuthenticationError();

            // dernier login entré
            $login = $authentificateur->getLastUsername();

            return $this->render("login.html.twig",
                array("login" => $login, "erreur" => $erreur));
        }
    }

    /**
     * Page de changement de mot de passe
     * @Route ("/pwd_change/", name="changement_pwd")
     * @Security("has_role('ROLE_USER') || has_role('ROLE_ADMIN')")
     * @param Request $requete
     * @return Response
     */
    public function passwordChange(Request $requete, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $formulaire = $this->createFormBuilder($user)
            ->add("password", PasswordType::class, [
                "label" => "Nouveau mot de passe"
            ])
            ->add("valider", SubmitType::class, [
                "label" => "Changer le mot de passe"
            ])
            ->getForm();

        $formulaire->handleRequest($requete);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $user->changePassword($encoder);

            $gestionnaire = $this->getDoctrine()->getManager();
            $gestionnaire->persist($user);
            $gestionnaire->flush();

            $this->addFlash("success", "Votre mot de passe a bien été changé.");

            return $this->redirectToRoute("page_accueil");
        }
        return $this->render("forms/form_recovery.html.twig",
            array(
                "formulaire" => $formulaire->createView()
            )
        );
    }

    /**
     * Page de réinitialisation de mot de passe
     * @Route ("/recovery/", name="page_oublie_mdp")
     * @param Request $requete
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function passwordRecovery(Request $requete, UserPasswordEncoderInterface $encoder)
    {

        if ($this->getUser()) {
            return $this->redirectToRoute("page_accueil");
        } else {
            $user = new User();
            $formulaire = $this->createFormBuilder($user)
                ->add("username", TextType::class, [
                    "label" => "Nom d'utilisateur"
                ])
                ->add("email", EmailType::class, [
                    "label" => "Email"
                ])
                ->add("valider", SubmitType::class, [
                    "label" => "Réinitialiser le mot de passe"
                ])
                ->getForm();

            $formulaire->handleRequest($requete);

            if ($formulaire->isSubmitted() && $formulaire->isValid()) {

                $repo = array(
                    "user" => $this->getDoctrine()->getRepository(User::class),
                );

                /** @var User $user */
                $user = $repo["user"]->findOneBy(array('username' => $user->getUsername()));
                $password = $user->reinitPassword($encoder, 8);

                $gestionnaire = $this->getDoctrine()->getManager();
                $gestionnaire->persist($user);
                $gestionnaire->flush();

                return $this->redirectToRoute("mail_recovery",
                    array(
                        "id" => $user->getId(),
                        "password" => $password
                    )
                );
            }


//            $user->reinitPassword($encoder, 8);

            return $this->render("pwd_recovery.html.twig",
                array(
                    "formulaire" => $formulaire->createView()
                )
            );
        }
    }

    /**
     * Page d'inscription
     * @Route ("/inscription/", name="inscription")
     * @param Request $requete
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function inscription(Request $requete, UserPasswordEncoderInterface $encoder)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute("page_accueil");
        } else {

            $user = new User();

            $formulaire = $this->createForm(UserType::class, $user);

            $formulaire->handleRequest($requete);

            $errors = [];

            // validation du formulaire
            if ($formulaire->isSubmitted() && $formulaire->isValid()) {

                $repo = array(
                    "user" => $this->getDoctrine()->getRepository(User::class),
                );

                // liste des catégories les plus utilisées
                if ($repo["user"]->findOneBy(array('username' => $user->getUsername()))) {
                    $errors[] = "Le nom d'utilisateur est déjà pris";
                }
                if ($repo["user"]->findOneBy(array('email' => $user->getEmail()))) {
                    $errors[] = "L'adresse mail renseignée est déjà utilisée";
                }

                if (!$errors) {

                    $user->setRole(0);
                    $encoded = $encoder->encodePassword($user, $user->getPassword());
                    $user->setPassword($encoded);
                    $user->setPasswordChange(0);

                    $gestionnaire = $this->getDoctrine()->getManager();
                    $gestionnaire->persist($user);
                    $gestionnaire->flush();
                    $this->addFlash("success", "Vous pouvez maintenant vous connecter.");

                    // sendmail
                    return $this->redirectToRoute("mail_register",
                        array("id" => $user->getId()));
                }

            }
            return $this->render("forms/form_register.html.twig",
                array(
                    "formulaire" => $formulaire->createView(),
                    "errors" => $errors
                )
            );
        }
    }
}