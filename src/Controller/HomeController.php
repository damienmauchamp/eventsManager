<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 12/03/2018
 * Time: 11:02
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Event;
use App\Entity\Label;

/**
 * Class HomeController
 * Contient les routes pour la page d'accueil ainsi que les pages de recherche
 * @package App\Controller
 */
class HomeController extends Controller
{
    /**
     * Affichage de la page d'accueil
     * @Route ("/", name="page_accueil")
     * @return Response
     */
    public function homepage()
    {
        // TODO : page d'accueil

        // Tableaux de repositories
        $repo = array(
            "label" => $this->getDoctrine()->getRepository(Label::class),
            "event" => $this->getDoctrine()->getRepository(Event::class)
        );

        // liste des catégories les plus utilisées
        $mostUsedLabels = $repo["label"]->findMostUsed(5);

        // liste des catégories les plus utilisées
        $mostPopularEvents = $repo["event"]->findMostPopularEvents(5);

        return $this->render("homepage.html.twig",
            array(
                "mostUsedLabels" => $mostUsedLabels,
                "mostPopularEvents" => $mostPopularEvents
            )
        );
    }

    /**
     * Page de recherche par chaîne de caractère
     * @Route ("/search/{str}", name="page_recherche_str", requirements={"id": "[a-zA-Z]+"})
     * @return Response
     */
    public function searchByString()
    {
        // TODO : page de recherche par chaîne de caractère
        return $this->render("base.html.twig");
    }

    /**
     * Page de recherche par libellé
     * @Route ("/search/label/{label}", name="page_recherche_label", requirements={"id": "[a-zA-Z]+"})
     * @param Label $label
     * @return Response
     */
    public function searchByLabel(Label $label)
    {
        // TODO : page de recherche par libellé
        $events = $label->getEvents();
        return $this->render("recherche_label.html.twig",
            array(
                "label" => $label, "events" => $events
            )
        );
    }
}