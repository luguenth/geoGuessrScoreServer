<?php

namespace App\Controller;

use App\Entity\GeoGuessrGame;
use App\Entity\Guess;
use App\Entity\Player;
use App\Entity\Round;
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
            ->add('token', TextType::class)
            ->add('search', SubmitType::class, ['label' => 'Index my Game!'])
            ->getForm();

        $form->handleRequest($request);

        $result = "";

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $this->persistGame($request, $data['token'], $geoGuessrApiService);

        }

        $gameRepo = $this->getDoctrine()->getRepository(GeoGuessrGame::class);
        $games = $gameRepo->findAll();

        return $this->render('Game/index.html.twig', [
            'form' => $form->createView(),
            'games' => $games,
        ]);
    }

    /**
     * @Route("/game/{id}", name="game_detail")
     */
    public function detail(Request $request, string $id): Response
    {
        $gameRepo = $this->getDoctrine()->getRepository(GeoGuessrGame::class);
        $game = $gameRepo->findOneBy(['token' => $id]);

        return $this->render('Game/detail.html.twig', [
            'game' => $game,
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
