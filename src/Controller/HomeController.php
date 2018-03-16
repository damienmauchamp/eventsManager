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
use App\Entity\User;

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

        // changement de mot de passe
        /** @var User $user */
        $user = $this->getUser();

        if ($user && $user->needsPasswordChange()) {
            return $this->redirectToRoute("changement_pwd");
        }

        // Tableaux de repositories
        $repo = array(
            "label" => $this->getDoctrine()->getRepository(Label::class),
            "event" => $this->getDoctrine()->getRepository(Event::class)
        );

        // liste des catégories les plus utilisées
        $mostUsedLabels = $repo["label"]->findMostUsed(5);

        // liste des évènements les plus populaires
        $mostPopularEvents = $repo["event"]->findMostPopularEvents(5);

        // liste des évènements créés le plus récemment
        $findLastAddedEvents = $repo["event"]->findLastAddedEvents(5);

        return $this->render("homepage.html.twig",
            array(
                "mostUsedLabels" => $mostUsedLabels,
                "mostPopularEvents" => $mostPopularEvents,
                "findLastAddedEvents" => $findLastAddedEvents
            )
        );
    }

    /**
     * Page de recherche par chaîne de caractère
     * @Route ("/search/{query}", name="page_recherche_str", requirements={"query"})
     * @param string $query
     * @return Response
     */
    public function searchByString(string $query)
    {
        // TODO : page de recherche par chaîne de caractère

        $page = 1;
        $offset = 0;
        $limit = 20;
        $words = explode(" ", $query);

        // récupération du numéro de page et du nombre de résultat maximum
        if (isset($_GET['page']) && $_GET['page'] > 0)
            $page = $_GET['page'];
        if (isset($_GET['max-results']) && $_GET['max-results'] > 0)
            $limit = $_GET['max-results'];

        $offset = ($page-1)*$limit;

        // Tableaux de repositories
        $repo = array(
            "label" => $this->getDoctrine()->getRepository(Label::class),
            "event" => $this->getDoctrine()->getRepository(Event::class)
        );

        // liste des catégories les plus utilisées
        $eventsResults = $repo["event"]->findSearchResult($words, $offset, $limit);

        // on vérifie si il existe une page suivante
        $nextElt = ($page*$limit+1);

        $nextPage = false;
        if ($repo["event"]->findSearchResult($words, $nextElt, 1)) {
            $nextPage = true;
        }

        return $this->render("recherche.html.twig",
            array(
                "query" => $query, // la chaîne de caractères recherchée
                "events" => $eventsResults, // les résultats des évènements
                "page" => $page, // la page
                "limit" => $limit, // le nombre de résultats par page
                "nextPage" => $nextPage // si il y a des résultats à la page suivante
            )
        );
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