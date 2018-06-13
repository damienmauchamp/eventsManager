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
use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\Label;
use App\Form\CommentType;
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
     * @param Request $requete
     * @return Response
     */
    public function eventPage(Event $event, Request $requete)
    {
        // TODO : affichage d'un profil
        $participants = $event->getParticipants();
        $creator = $event->getCreatedBy();
        $comments = $event->getComments();
        $labels = $event->getLabels();

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $user = $this->getUser();
        $commentForm->handleRequest($requete);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setEvent($event);
            $comment->setPostedBy($user);
            $gestionnaire = $this->getDoctrine()->getManager();
            $gestionnaire->persist($comment);
            $gestionnaire->flush();
            $this->addFlash("success", "Commentaire ajouté.");

            return $this->redirectToRoute("page_evenement", array("id" => $event->getId()));
        }

        return $this->render("event.html.twig",
            array(
                "event" => $event,
                "participants" => $participants,
                "creator" => $creator,
                "comments" => $comments,
                "labels" => $labels,
                "commentForm" => $commentForm->createView()
            )
        );
    }

    /**
     * Page d'ajout d'un évènement
     * @Route ("/event/add/", name="page_ajout_evenement")
     * @Security("has_role('ROLE_USER') || has_role('ROLE_ADMIN')")
     * @param Request $requete
     * @return Response
     */
    public function addEventPage(Request $requete)
    {
        // Requêtage des labels
        if (isset($_GET['q']) && isset($_GET['field_name'])) {
            $as = $this->get('tetranz_select2entity.autocomplete_service');
            $result = $as->getAutocompleteResults($requete, EventType::class);

//            if (!$result["results"]) {
//                $lName = trim($_GET['q']);
//                $label = new Label();
//                $label->setName($lName);
////                    dump($label);exit;
//
//                $length = strlen($lName);
//                dump(substr(",", -$length));
//                dump((substr(",", -$length) == $lName));exit;
//
//                if ($length === 0 || (substr(",", -$length) === $lName)) {
//                    $gestionnaire = $this->getDoctrine()->getManager();
//                    $gestionnaire->persist($label);
//                    $gestionnaire->flush();
//                }
//            }


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
        if (!($formulaire->isSubmitted() && $formulaire->isValid())) {
            $formulaire->add("valider", SubmitType::class, [
                "label" => "Créer l'évènement"
            ]);
        }
        $formulaire->handleRequest($requete);

        // validation du formulaire
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
//            dump($formulaire);exit;
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
            return $this->redirectToRoute("page_evenement", array("id" => $event->getId()));
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
     * @Security("event.isCreator(user) || has_role('ROLE_ADMIN')")
     * @param Event $event
     * @param Request $requete
     * @return Response
     */
    public function editEventPage(Event $event, Request $requete)
    {
        // récupération de l'image
        $repo = array(
            "event" => $this->getDoctrine()->getRepository(Event::class),
        );
        /** @var Event $oldEvent */
        $oldEvent = $repo["event"]->findOneBy(array('id' => $event->getId()));
        $oldImage = $oldEvent->getImage();


        // Requêtage des labels
        if (isset($_GET['q']) && isset($_GET['field_name'])) {
            $as = $this->get('tetranz_select2entity.autocomplete_service');
            $result = $as->getAutocompleteResults($requete, EventType::class);
            return new JsonResponse($result);
        }
        $gestionnaire = $this->getDoctrine()->getManager();

        $formulaire = $this->createForm(EventType::class, $event);


        if (!($formulaire->isSubmitted() && $formulaire->isValid())) {
            $formulaire->add("valider", SubmitType::class, [
                "label" => "Modifier l'évènement"
            ]);
        }

        /** @var Label $label */
        foreach ($event->getLabels() as $label) {
            $label->removeEvent($event);
            $event->removeLabel($label);
        }

        $formulaire->handleRequest($requete);

        // validation du formulaire
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            // récupération de l'ancienne image si nulle
            if ($event->getImage() == null) {
                $event->setImage($oldImage);
            }

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
            return $this->redirectToRoute("page_evenement", array("id" => $event->getId()));

        } else {
            return $this->render("forms/form_event.html.twig",
                array(
                    "formulaire" => $formulaire->createView()
                )
            );
        }
    }

    /**
     * Suppression d'un évènement
     * @Route ("/event/{id}/remove/", name="suppression_evenement", requirements={"id": "\d+"})
     * @Security("event.isCreator(user) || has_role('ROLE_ADMIN')")
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

        return $this->redirectToRoute("page_accueil");
    }

    /**
     * Suppression d'un commentaire
     * @Route ("/comment/{id}/remove/", name="suppression_commentaire", requirements={"id": "\d+"})
     * @Security("comment.isCreator(user) || has_role('ROLE_ADMIN')")
     * @return Response
     */
    public function removeComment(Comment $comment)
    {
        $gestionnaire = $this->getDoctrine()->getManager();
        $gestionnaire->remove($comment);
        $gestionnaire->flush();

        $event = $comment->getEvent();

        // Message affiché
        $this->addFlash("success", "Commentaire supprimé.");

        return $this->redirectToRoute("page_evenement", array("id" => $event->getId()));
    }

    /**
     * L'utilisateur ajoute sa participation à l'évènement
     * @Route ("/event/{id}/participe", name="add_participation", requirements={"id": "\d+"})
     * @Security ("has_role('ROLE_USER') || has_role('ROLE_ADMIN')")
     * @param Event $event
     * @return Response
     */
    public function addParticipation(Event $event)
    {
        $user = $this->getUser();

        if (!$event->isParticipating($user)) {
            $event->addParticipant($user);
            $user->addEvent($event);

            $gestionnaire = $this->getDoctrine()->getManager();
            $gestionnaire->persist($user);
            $gestionnaire->persist($event);
            $gestionnaire->flush();
        }

        return $this->redirectToRoute("page_evenement", array("id" => $event->getId()));

    }

    /**
     * L'utilisateur supprime sa participation à l'évènement
     * @Route ("/event/{id}/annulation", name="remove_participation", requirements={"id": "\d+"})
     * @Security ("has_role('ROLE_USER') || has_role('ROLE_ADMIN')")
     * @param Event $event
     * @return Response
     */
    public function removeParticipation(Event $event)
    {
        $user = $this->getUser();

        if ($event->isParticipating($user)) {
            $event->removeParticipant($user);
            $user->removeEvent($event);

            $gestionnaire = $this->getDoctrine()->getManager();
            $gestionnaire->persist($event);
            $gestionnaire->persist($user);
            $gestionnaire->flush();
        }

        return $this->redirectToRoute("page_evenement", array("id" => $event->getId()));
    }
}