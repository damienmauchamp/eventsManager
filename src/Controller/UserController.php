<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 12/03/2018
 * Time: 11:14
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Swift_Mailer;

use App\Entity\User;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends Controller
{
    /**
     * Page d'affichage d'un profil par ID
     * @Route ("/user/{id}", name="page_profilID", requirements={"id": "\d+"})
     * @param User $user
     * @return Response
     */
    public function userPageByID(User $user)
    {
        $events = $user->getEvents();
        $createdEvents = $user->getCreatedEvents();
        $postedComments = $user->getPostedComments();
        return $this->render("profile.html.twig",
            array("user" => $user, "events" => $events, "created" => $createdEvents, "comments" => $postedComments));
    }


    /**
     * Page d'affichage d'un profil par username
     * @Route ("/user/{username}", name="page_profil", requirements={"username": "[a-zA-Z0-9.]+"})
     * @param User $user
     * @return Response
     */
    public function userPageByUsername(User $user)
    {
        $events = $user->getEvents();
        $createdEvents = $user->getCreatedEvents();
        $postedComments = $user->getPostedComments();
        return $this->render("profile.html.twig",
            array("user" => $user, "events" => $events, "created" => $createdEvents, "comments" => $postedComments));
    }


    /**
     * @Route ("/mail_register/{id}", name="mail_register", requirements={"id": "\d+"})
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function envoiMailInscription(\Swift_Mailer $mailer, User $user) {
        $message = (new \Swift_Message("Confirmation de votre inscription"))
            ->setFrom("noreply@eventmanager.cefim.eu")
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('mails/mail_register.html.twig',
                    array(
                        "user" => $user
                    )), 'text/plain');
        $mailer->send($message);

        $this->addFlash("success", "Un mail de confirmation vous a été envoyé.");

        return $this->redirectToRoute("login");

    }


    /**
     * @Route ("/mail_recovery/{id}/{password}", name="mail_recovery", requirements={"id": "\d+"})
     * @param Swift_Mailer $mailer
     * @param User $user
     * @param string $password
     * @return Response
     */
    public function envoiMailRecovery(\Swift_Mailer $mailer, User $user, string $password) {
        $message = (new \Swift_Message("Réinitialisation de votre mot de passe"))
            ->setFrom("noreply@eventmanager.cefim.eu")
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('mails/mail_recovery.html.twig',
                    array(
                        "user" => $user,
                        "password" => $password
                    )), 'text/plain');
        $mailer->send($message);

        $this->addFlash("success", "Un nouveau mot de passe vous a été envoyé par mail.");

        return $this->redirectToRoute("login");

    }
}