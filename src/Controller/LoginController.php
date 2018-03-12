<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 12/03/2018
 * Time: 14:26
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{

    /**
     * @Route ("/login/", name="login")
     * @param Request $requete
     * @param AuthenticationUtils $authentificateur
     * @return Response
     */
    public function login(Request $requete, AuthenticationUtils $authentificateur)
    {
        // message d'erreur
        $erreur = $authentificateur->getLastAuthenticationError();

        // dernier login entré
        $login = $authentificateur->getLastUsername();

        return $this->render("login.html.twig",
            array("login" => $login, "erreur" => $erreur));
    }

    /**
     * Page de réinitialisation de mot de passe
     * @Route ("/recovery/", name="page_oublie_mdp")
     * @param Request $requete
     * @return Response
     */
    public function passwordRecovery(Request $requete)
    {
        return $this->render("pwd_recovery.html.twig");
    }
}