<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 12/03/2018
 * Time: 15:39
 */

namespace App\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Tetranz\Select2EntityBundle\Service\AutocompleteService;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Label;
use App\Form\EventType;

/**
 * Class EventController
 * @package App\Controller
 */
class EventController extends Controller
{


//    private $autocompleteService;
//
//    /**
//     * EventController constructor.
//     * @param AutocompleteService $autocompleteService
//     */
//    public function __construct(AutocompleteService $autocompleteService)
//    {
//        $this->autocompleteService = $autocompleteService;
//    }


    /**
     * Page d'affichage d'un évènement
     * @Route ("/event/{id}/", name="page_evenement", requirements={"id": "\d+"})
     * @param Event $event
     * @return Response
     */
    public function eventPage(Event $event)
    {
        // TODO : affichage d'un profil
        $participants = $event->getParticipants();
        $creator = $event->getCreatedBy();
        $comments = $event->getComments();
        $labels = $event->getLabels();
        return $this->render("event.html.twig",
            array(
                "event" => $event,
                "participants" => $participants,
                "creator" => $creator,
                "comments" => $comments,
                "labels" => $labels
            )
        );
    }

    /**
     * Page d'ajout d'un évènement
     * @Route ("/event/add/", name="page_ajout_evenement")
     * @param Request $requete
     * @return Response
     */
    public function addEventPage(Request $requete)
    {
        // Requêtage des labels
        if (isset($_GET['q']) && isset($_GET['field_name'])) {
            $as = $this->get('tetranz_select2entity.autocomplete_service');
            $result = $as->getAutocompleteResults($requete, EventType::class);
            return new JsonResponse($result);
        }

        // récupération de l'utilisateur en cours
        $user = $this->getUser();

        // création de l'évènement
        $event = new Event();

        // on attribut l'utilisateur en cours comme créateur de l'évènement
        $event->setCreatedBy($user);

        // création du formulaire
        $formulaire = $this->createForm(EventType::class, $event);
        $formulaire->handleRequest($requete);

        // validation du formulaire
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $gestionnaire = $this->getDoctrine()->getManager();

            /**
             * On attribut l'évènement aux labels
             * @var Label $label
             */
            foreach ($event->getLabels() as $label) {
                $label->setEvent($event);
                $gestionnaire->persist($label);
            }

            $gestionnaire->persist($event);
            $gestionnaire->flush();
            $this->addFlash("success", "Nouvel évènement ajouté.");
            return $this->redirect("/");
        } else {
            return $this->render("forms/form_event.html.twig",
                array(
                    "formulaire" => $formulaire->createView()
                )
            );
        }
    }

    /**
     * Page d'édition d'un évènement
     * @Route ("/event/{id}/edit/", name="page_edit_evenement", requirements={"id": "\d+"})
     * @Security("event.isCreator(user)")
     * @param Event $event
     * @param Request $requete
     * @return Response
     */
    public function editEventPage(Event $event, Request $requete)
    {
        // Requêtage des labels
        if (isset($_GET['q']) && isset($_GET['field_name'])) {
            $as = $this->get('tetranz_select2entity.autocomplete_service');
            $result = $as->getAutocompleteResults($requete, EventType::class);
            return new JsonResponse($result);
        }
        $gestionnaire = $this->getDoctrine()->getManager();
        $oldLabels = $event->getLabels();

        $formulaire = $this->createForm(EventType::class, $event);

        /** @var Label $label */
        foreach ($event->getLabels() as $label) {
            $label->removeEvent($event);
            $event->removeLabel($label);
        }

        $formulaire->handleRequest($requete);
        // validation du formulaire
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            /**
             * On attribut l'évènement aux labels
             * @var Label $label
             */
            foreach ($event->getLabels() as $label) {
                $label->setEvent($event);
                $gestionnaire->persist($label);
            }

            $gestionnaire->persist($event);
            $gestionnaire->flush();
            $this->addFlash("success", "Évènement modifié.");
            return $this->redirect("/event/" . $event->getId());

        } else {
            return $this->render("forms/form_event.html.twig",
                array(
                    "formulaire" => $formulaire->createView()
                )
            );
        }
    }


    /**
     * @Route ("/event/{id}/remove/", name="suppression_article", requirements={"id": "\d+"})
     * @Security("event.isCreator(user)")
     * @param Event $event
     * @return Response
     */
    public function removeEvent(Event $event)
    {
        $gestionnaire = $this->getDoctrine()->getManager();
        $gestionnaire->remove($event);
        $gestionnaire->flush();

        // Message affiché
        $this->addFlash("success", "Évènement supprimé.");

        return $this->redirect("/");
    }


    /////////////
    /// TESTS ///

    /**
     * @Route ("/event/test")
     */
    public function testEvent()
    {
        $gestionnaire = $this->getDoctrine()->getManager();

        $event = new Event();
        $user = $this->getUser();

        $event->setName("nom");
        $event->setPlace("place");
        $event->setDescription("description");
//        $event->setDateDebut("now");
//        $event->setDateFin("now +1 hour");
        $event->setCreatedBy($user);

        $label1 = new Label();
        $label1 = $gestionnaire->getRepository(Label::class)
            ->findOneBy(['id' => 1]);
        $event->addLabel($label1);

        $label1->setEvent($event);
        $gestionnaire->persist($label1);
        $gestionnaire->persist($event);

        $gestionnaire->flush();


        dump($label1);
        dump($event);

        exit;
    }

    /**
     * @param Request $request
     * @Route("/label_autocomplete", name="label_autocomplete")
     * @return JsonResponse
     */
    public function autocompleteAction2(Request $request)
    {
        $as = $this->get('tetranz_select2entity.autocomplete_service');
        $result = $as->getAutocompleteResults($request, EventType::class);
        return new JsonResponse($result);
    }
}