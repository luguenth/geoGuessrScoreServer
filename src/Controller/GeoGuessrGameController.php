<?php

namespace App\Controller;

use App\Entity\GeoGuessrGame;
use App\Entity\Guess;
use App\Entity\Player;
use App\Entity\Round;
use App\Repository\GeoGuessrGameRepository;
use App\Service\GeoGuessrApiService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeoGuessrGameController extends AbstractController
{
    /**
     * @Route("/", name="game_index")
     */
    public function index(Request $request, GeoGuessrApiService $geoGuessrApiService): Response
    {
        $form = $this->createFormBuilder()
            ->add('token', TextType::class, ['label' => 'Game Token'])
            ->add('search', SubmitType::class, ['label' => 'Index my Game!'])
            ->getForm();

        $form->handleRequest($request);

        $result = "";

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $this->persistGame($request, $data['token'], $geoGuessrApiService);

        }

        $gameRepo = $this->getDoctrine()->getRepository(GeoGuessrGame::class);
        $games = $gameRepo->findBy([],['totalScoreInPoints'=> "DESC"], 50);

        return $this->render('Game/index.html.twig', [
            'form' => $form->createView(),
            'games' => $games,
        ]);
    }

    /**
     * @Route("/game/{token}", name="game_detail")
     */
    public function detail(Request $request, string $token): Response
    {
        $gameRepo = $this->getDoctrine()->getRepository(GeoGuessrGame::class);
        $game = $gameRepo->findOneBy(['token' => $token]);

        return $this->render('Game/detail.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/game/{token}/comparison/", name="game_comparison")
     */
    public function comparison(Request $request, string $token): Response
    {
        /**
         * @var GeoGuessrGameRepository
         */
        $gameRepo = $this->getDoctrine()->getRepository(GeoGuessrGame::class);
        /** @var GeoGuessrGame $game */
        $game = $gameRepo->findOneBy(['token' => $token]);
        $games = $gameRepo->findBy([
            'forbidMoving' => $game->getForbidMoving(),
            'forbidRotating' => $game->getForbidRotating(),
            'forbidZooming' => $game->getForbidZooming(),
            'timeLimit' => $game->getTimeLimit(),
            'round' => $game->getRound(),
            'map' => $game->getMap(),
        ], ['totalScoreInPoints'=> "DESC"]);

        return $this->render('Game/comparison.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/api/v1/game/import/{token}", name="api_game_import")
     */
    public function persistGame(Request $request, string $token, GeoGuessrApiService $geoGuessrApiService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $gameRepo = $this->getDoctrine()->getRepository(GeoGuessrGame::class);
        $game = $gameRepo->findOneBy(['token' => $token]) ?? new GeoGuessrGame(); 

        $result = $geoGuessrApiService->getGameData($token);
        if(empty($result)){
            return new Response(
                '<html><body>Err</body></html>'
            );
        }
        dump($result);
        $game
            ->setToken($result["token"])
            ->setType($result["type"])
            ->setMode($result["mode"])
            ->setState($result["state"])
            ->setRoundCount($result["roundCount"])
            ->setTimeLimit($result["timeLimit"])
            ->setForbidMoving($result["forbidMoving"])
            ->setForbidZooming($result["forbidZooming"])
            ->setForbidRotating($result["forbidRotating"])
            ->setMap($result["map"])
            ->setMapName($result["mapName"])
            ->setPanoramaProvider($result["panoramaProvider"])
            ->setRound($result["round"])
            ->setTotalDistanceInMeters($result["player"]["totalDistanceInMeters"])
            ->setTotalScoreInPercentage($result["player"]["totalScore"]["percentage"])
            ->setTotalScoreInPoints($result["player"]["totalScore"]["amount"]);

        $round_difference = count($result["rounds"]) - count($game->getRounds());
        dump($game->getRounds());
        dump($round_difference);
        if ($round_difference > 0) {
            array_splice($result["rounds"], 0, -1 * $round_difference);
            foreach ($result["rounds"] as $round) {
                $newRound = new Round();
                $newRound
                    ->setLat($round["lat"])
                    ->setLng($round["lng"])
                    ->setPanoId($round["panoId"])
                    ->setHeading($round["heading"])
                    ->setPitch($round["pitch"])
                    ->setZoom($round["zoom"])
                    ->setCountryCode($round["countryCode"]);
                $game->addRound($newRound);
                $entityManager->persist($newRound);
            }
        }

        $round_difference = count($result["player"]["guesses"]) - count($game->getGuesses());
        if ($round_difference > 0) {
            array_splice($result["player"]["guesses"], 0, -1 * $round_difference);
            foreach ($result["player"]["guesses"] as $guess) {
                $newGuess = new Guess();
                $newGuess
                    ->setLat($guess["lat"])
                    ->setLng($guess["lng"])
                    ->setCountryCode($guess["countryCode"])
                    ->setTimedOut($guess["timedOut"])
                    ->setTimedOutWithGuess($guess["timedOutWithGuess"])
                    ->setRoundScoreInPercentage($guess["roundScoreInPercentage"])
                    ->setRoundScoreInPoints($guess["roundScoreInPoints"])
                    ->setDistanceInMeters($guess["distanceInMeters"])
                    ->setTime($guess["time"]);
                $game->addGuess($newGuess);
                $entityManager->persist($newGuess);
            }
        }

        $player = 
            $this->getDoctrine()->getRepository(Player::class)->findOneBy(["geoGuessId" => $result["player"]["id"]]) 
            ?? new Player();
        $player
            ->setGeoGuessId($result["player"]["id"])
            ->setNickname($result["player"]["nick"]);
        $game->setPlayer($player);
        $game->setLastChanged(New DateTime('now'));
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->flush();

        return new Response(
            '<html><body>Imported Game</body></html>'
        );
    }
}
