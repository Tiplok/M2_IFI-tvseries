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
        // Without pagination code
        /*$manager = $this->get('doctrine')->getManager();
        $series = $manager->getRepository(TvSeries::class)->findAll();

        $data = $request->request->all();
        $toSend = (isset($data['message'])) ? $data['message'] : null;

        return $this->render('tvseries/index.html.twig', ['series' => $series, 'message' => $toSend]);*/

        return $this->SeriesListAction($request, 1);
    }

    /**
     * @Route(name="series_list", path="/series/list/{page}")
     * @param $page
     * @return Response
     */
    public function SeriesListAction(Request $request, $page)
    {
        $maxSeries = 10;
        $series_count = $this->getDoctrine()->getManager()->getRepository('AppBundle:TvSeries')->getCount();
        $series = $this->getDoctrine()->getManager()->getRepository('AppBundle:TvSeries')->findForPagination($page, $maxSeries);
        $data = $request->request->all();
        $toSend = (isset($data['message'])) ? $data['message'] : null;

        $pagination = array(
            'page' => $page,
            'route' => 'series_list',
            'pages_count' => ceil($series_count / $maxSeries),
            'route_params' => array()
        );


        return $this->render('tvseries/index.html.twig', array(
            'series' => $series,
            'pagination' => $pagination,
            'message' => $toSend
        ));
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
}
