<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 13/03/2018
 * Time: 14:42
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use \App\Entity\Comment;
use \App\Entity\Event;
use \App\Entity\Label;
use \App\Entity\User;

class TestController extends Controller
{

    /**
     * @Route ("/faker/{object}/{n}", name="faker_user")
     * @Security("has_role('ROLE_ADMIN')")
     * @param string $object
     * @param int $n
     * @return Response
     */
    public function fakerUser($object, $n)
    {
        $faker = \Faker\Factory::create("fr_FR");
        $gestionnaire = $this->getDoctrine()->getManager();
        $repo = array(
            "label" => $this->getDoctrine()->getRepository(Label::class),
            "event" => $this->getDoctrine()->getRepository(Event::class),
            "comment" => $this->getDoctrine()->getRepository(Comment::class),
            "user" => $this->getDoctrine()->getRepository(User::class)
        );

        for ($i = 0; $i < $n; $i++) {
            switch ($object) {
                case "test":
                    echo $faker->numberBetween(1, 100);
                    echo "<br/>";
                    echo $faker->dateTimeInInterval("1 years", "3 years", "Europe/Paris")->format('Y-m-d H:i:s');
                    echo "<br/>";
                    $user = new User();
                    $user = $repo['user']->find($faker->numberBetween(1, 100));
                    dump($user);
                    echo $user->getId();
                    exit;
                case "user":
                    $objet = new User();
                    $objet->setUsername($faker->username);
                    $objet->setPassword("$2y$13\$IJuWtLL8gAQ3q2GRq9NpUuZAHIWFM/dlPJiF/VAxqgKs.K2djfmpa");
                    $objet->setEmail($faker->email);
                    $objet->setFirstname($faker->firstName);
                    $objet->setLastname($faker->lastName);
                    $objet->setRole(0);
                    break;
                case "comment":
                    $objet = new Comment();

                    $user = new User();
                    $user = $repo['user']->find($faker->numberBetween(1, 100));
                    $objet->setPostedBy($user);

                    $event = new Event();
                    $event = $repo['event']->find($faker->numberBetween(12, 100));
                    $objet->setEvent($event);
                    $objet->setDate($faker->dateTimeInInterval("1 years", "3 years", "Europe/Paris")->format('Y-m-d H:i:s'));
                    $objet->setContent($faker->text(1000));
                    break;
                case "label":
                    $objet = new Label();
                    $objet->setName($faker->word);
                    break;
                case "event":
                    $objet = new Event();
                    $objet->setName($faker->sentence(10));
                    $objet->setPlace($faker->city);
                    $tmp = $faker->dateTimeInInterval("1 years", "3 years", "Europe/Paris")->format('Y-m-d H:i:s');
                    $objet->setDateDebut($tmp);
                    $objet->setDateFin($faker->dateTimeInInterval($tmp, "3 days", "Europe/Paris")->format('Y-m-d H:i:s'));
                    $objet->setDescription($faker->text(3000));
                    $user = new User();
                    $user = $repo['user']->find($faker->numberBetween(1, 100));
                    $objet->setCreatedBy($user);
//                    $objet->setCreatedBy($faker->numberBetween(1, 100));
                    break;
                case "labelToEvent":
                    $nLabel = 107;
                    $nEvent = 110;
                    $conn = $this->getDoctrine()->getConnection();
                    $sql = 'INSERT INTO event_label(label_id, event_id) VALUES (:label, :event)';
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['label' => $faker->numberBetween(90, $nLabel)+1, 'event' => $faker->numberBetween(12, $nEvent)+1]);
                    break;
                case "userToEvent":
                    $nUser = 100;
                    $nEvent = 110;
                    $conn = $this->getDoctrine()->getConnection();
                    $sql = 'INSERT INTO event_user(user_id, event_id) VALUES (:user, :event)';
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['user' => $faker->numberBetween(1, $nUser), 'event' => $faker->numberBetween(12, $nEvent)]);
                    break;
            }
//            $gestionnaire->persist($objet);
        }
//        $gestionnaire->flush();
        $this->addFlash("success", "${n} objets ajouté.");

        return $this->redirect("/");
    }



    /////////////
    /// TESTS ///

    /**
     * @Route ("/event/test")
     * @Security("has_role('ROLE_ADMIN')")
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
     * @Security("has_role('ROLE_ADMIN')")
     * @return JsonResponse
     */
    public function autocompleteAction2(Request $request)
    {
        $as = $this->get('tetranz_select2entity.autocomplete_service');
        $result = $as->getAutocompleteResults($request, EventType::class);
        return new JsonResponse($result);
    }
}