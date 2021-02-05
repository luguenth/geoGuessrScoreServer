<?php

namespace App\Controller;

use App\Entity\Round;
use App\Service\GeoGuessrApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    /**
     * @Route("/map", name="map_index")
     */
    public function index(Request $request): Response
    {
        $roundRepo = $this->getDoctrine()->getRepository(Round::class);
        $allRounds = $roundRepo->findAll();
        $heatmapArray = null;
        foreach ($allRounds as $round) {
            $heatmapArray[] = [$round->getLat(), $round->getLng(), 1];
        }
        $heatmapArray = json_encode($heatmapArray);
        return $this->render('Map/index.html.twig', [
            "heatmap_arr" => $heatmapArray,
            "rounds" => $allRounds
        ]);
    }
}