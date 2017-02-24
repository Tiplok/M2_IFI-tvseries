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
     * @Route(name="homepage_index", path="/")
     */
    public function listAction(Request $request){
        $manager = $this->get('doctrine')->getManager();
        $series = $manager->getRepository(TvSeries::class)->findAll();

        $data = $request->request->all();
        $toSend = (isset($data['message'])) ? $data['message'] : null;

        return $this->render('tvseries/index.html.twig', ['series' => $series, 'message' => $toSend]);
    }

    /**
     * @Route(name="tv_series_create", path="/series/create")
     */
    public function createSeriesAction(Request $request){
        // Permet de restreindre l'accès.
        /*$user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }*/

        $data = $request->request->all();

        if($request->getMethod() == 'GET'){
            return $this->render('tvseries/create.html.twig');
        }else if($request->getMethod() == 'POST'){
            $s = new TvSeries();
            $s->setAuthor($data['author']);
            $s->setName($data['name']);
            $s->setDescription($data['description']);

            // On récupère un manager avec Doctrine (gestion de la BDD)
            $manager = $this->getDoctrine()->getManager();
            // On surveille les changements sur l'objet $s
            $manager->persist($s);
            // Met à jour la BDD à chaque modification des objets surveillés
            $manager->flush();

            return $this->redirectToRoute('homepage_index', array('message' => 'serie_created'));
            //return new Response("Created");
        }
    }

    /**
     * @Route(name="tv_series_update", path="/series/update")
     */
    public function updateSeriesAction(Request $request){
        // Permet de restreindre l'accès.
        /*$user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }*/

        $data = $request->request->all();

        if(isset($data['updating'])){
            $tvSeriesRepository = $this->getDoctrine()->getRepository('AppBundle:TvSeries');
            $s = $tvSeriesRepository->find($data['updating']);

            return $this->render('tvseries/update.html.twig', ['serie' => $s]);
        }else{
            $tvSeriesRepository = $this->getDoctrine()->getRepository('AppBundle:TvSeries');
            $s = $tvSeriesRepository->find($data['id_serie']);

            $s->setAuthor($data['author']);
            $s->setName($data['name']);
            $s->setDescription($data['description']);

            // On récupère un manager avec Doctrine (gestion de la BDD)
            $manager = $this->getDoctrine()->getManager();
            // On surveille les changements sur l'objet $s
            $manager->persist($s);
            // Met à jour la BDD à chaque modification des objets surveillés
            $manager->flush();

            return $this->redirectToRoute('homepage_index', array('message' => 'serie_updated'));
            //return new Response("Updated");
        }
    }

    /**
     * @Route(name="tv_series_delete", path="/series/delete")
     */
    public function deleteSeriesAction(Request $request){
        // Permet de restreindre l'accès.
        /*$user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }*/

        $data = $request->request->all();

        var_dump($data['id_serie']);

        if(isset($data['id_serie'])){
            var_dump("coucou");
            $tvSeriesRepository = $this->getDoctrine()->getRepository('AppBundle:TvSeries');
            $s = $tvSeriesRepository->find($data['id_serie']);

            // On récupère un manager avec Doctrine (gestion de la BDD)
            $manager = $this->getDoctrine()->getManager();
            // On remove l'objet $s
            $manager->remove($s);
            // Met à jour la BDD à chaque modification des objets surveillés
            $manager->flush();

            return $this->redirectToRoute('homepage_index', array('message' => 'serie_removed'));
            //return new Response("Updated");
        }else{
            return new Response("Une erreur est survenue : La série n'a pas été trouvée.");
        }
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
