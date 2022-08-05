<?php


namespace App\Controller;



use App\Form\SearchType;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class SearchController extends AbstractController
{

    /**
     * @Route("/car/search", name="media_search")
     */
public function searchMedia(Request $request, MediaRepository $mediaRepository)
{
    $searchForm = $this->createForm(SearchType::class);
        if ($searchForm->handleRequest($request)->isSubmitted() && $searchForm->isValid()) {

            $criteria = $searchForm->getData();

            $media = $mediaRepository->searchMedia($criteria);
            dd($media);
        }
    return $this->render('search/car.html.twig',[
        'search_form' => $searchForm->createView(),
    ]);

}
}