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

use App\Entity\User;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends Controller
{
    /**
     * Page d'affichage d'un profil
     * @Route ("/user/{id}", name="page_profil", requirements={"id": "\d+"})
     * @param User $user
     * @return Response
     */
    public function userPage(User $user)
    {
        // TODO : affichage d'un profil
        $events = $user->getEvents();
        $createdEvents = $user->getCreatedEvents();
        $postedComments = $user->getPostedComments();
        return $this->render("profile.html.twig",
            array("user" => $user, "events" => $events, "created" => $createdEvents, "comments" => $postedComments));
    }
}