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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends Controller
{

    /**
     * Page d'affichage d'un profil
     * @Route ("/user/{id}", name="page_connexion", requirements={"id": "\d+"})
     */
    public function userPage()
    {
        // TODO : affichage d'un profil
        return $this->render("pwd_recovery.html.twig");

    }
}