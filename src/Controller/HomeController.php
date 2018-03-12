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
        return $this->render("base.html.twig");
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
     * @return Response
     */
    public function searchByLabel()
    {
        // TODO : page de recherche par libellé
        return $this->render("base.html.twig");
    }
}