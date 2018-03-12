<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 12/03/2018
 * Time: 15:39
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;
use App\Entity\Event;

/**
 * Class EventController
 * @package App\Controller
 */
class EventController extends Controller
{
    /**
     * Page d'affichage d'un évènement
     * @Route ("/event/{id}", name="page_evenement", requirements={"id": "\d+"})
     * @param Event $event
     * @return Response
     */
    public function userPage(Event $event)
    {
        // TODO : affichage d'un profil
        return $this->render("event.html.twig",
            array("event" => $event));
    }
}