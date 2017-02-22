<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TvSeries;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TvSeriesController extends Controller
{
    /**
     * @Route(name="tv_series_create", path="/series/create")
     */
    public function createSeriesAction(Request $request){
        $s = new TvSeries();
        $s->setAuthor('Auteur 1');
        $s->setName('Oeuvre 1');
        $s->setDescription($request->get('description'));

        // On récupère un manager avec Doctrine (gestion de la BDD)
        $manager = $this->getDoctrine()->getManager();
        // On surveille les changements sur l'objet $s
        $manager->persist($s);
        // Met à jour la BDD à chaque modification des objets surveillés
        $manager->flush();

        return new Response("Ok");
    }

    /**
     * @Route(name="homepage_index", path="/")
     */
    public function listAction(){
        $manager = $this->get('doctrine')->getManager();
        $series = $manager->getRepository(TvSeries::class)->findAll();

        return $this->render('tvseries/index.html.twig', ['series' => $series]);
    }

    /* Exemple
    public function indexAction()
    {
        $s1 = new TvSeries();
        $s1->setId('5efec9a2-31db-451d-982a-5e3b0f1b8d27');
        $s1->setAuthor('Author 1');
        $s1->setName('Title 1');

        $s2 = new TvSeries();
        $s2->setId('66b3c1d5-a4ff-41ec-809e-88e28e085a25');
        $s2->setAuthor('Author 2');
        $s2->setName('Title 2');

        $series = [
            $s1,
            $s2
        ];

        return $this->render('tvseries/index.html.twig', ['series' => $series]);
    }*/
}
