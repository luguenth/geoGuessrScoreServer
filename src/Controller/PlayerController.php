<?php

namespace App\Controller;

use App\Service\GeoGuessrApiService;
use App\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlayerController extends AbstractController
{
    /**
     * @Route("player/", name="player_index")
     */
    public function index(Request $request): Response
    {
        $playerRepo = $this->getDoctrine()->getRepository(Player::class);
        $players = $playerRepo->findAll();

        return $this->render('Player/index.html.twig', [
            'players' => $players,
        ]);
    }

    /**
     * @Route("player/{id}", name="player_detail")
     */
    public function detail(Request $request, string $id): Response
    {
        $playerRepo = $this->getDoctrine()->getRepository(Player::class);
        $player = $playerRepo->findOneBy(["geoGuessId" => $id]);

        return $this->render('Player/detail.html.twig', [
            'player' => $player,
        ]);
    }

    /**
     * @Route("api/v1/player/import/{id}", name="api_player_import")
     */
    public function import(Request $request, string $id, GeoGuessrApiService $geoGuessrApiService, HttpClientInterface $client): Response
    {
        $allGames = $geoGuessrApiService->getPlayerGames($id);

        foreach($allGames as $game){
            if($game["activityType"] == 3){
                $geoGuessrApiService->persistGame($game["payload"]["map"]["gameToken"]);
            }        
        }

        return new Response(
            '<html><body>Imported Player</body></html>'
        );
    }
}