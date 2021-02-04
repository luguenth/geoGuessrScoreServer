<?php

namespace App\Service;

use App\Entity\GeoGuessrGame;
use App\Entity\Guess;
use App\Entity\Player;
use App\Entity\Round;
use App\Repository\GeoGuessrGameRepository;
use App\Repository\PlayerRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoGuessrApiService
{

    public function __construct(
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager,
        GeoGuessrGameRepository $geoGuessrGameRepository,
        PlayerRepository $playerRepository,
        )
    {
        $this->client = $httpClient;
        $this->apiUrl = "https://www.geoguessr.com/api/v3/";
        $this->entityManager = $entityManager;
        $this->gameRepo = $geoGuessrGameRepository;
        $this->playerRepo = $playerRepository;
    }

    public function getGameData(string $token) :array
    {
        $result = $this->client->request(
            'GET',
            $this->apiUrl . "games/" . $token,
        );
        try {
            $result = json_decode($result->getContent(), true);
        } catch (Exception $e) {
            error_log("404");
            return [];
        }
        dump($result);
        return $result;
    }

    public function getPlayerGames(string $playerId, int $page = 0) :array
    {
        $cookie = "__gads=ID=1cab7e8be7dbede9-221319be5dba008a:T=1612451231:S=ALNI_MZ6wRZyImXszkjnB6hl-WslGTdt0A; devicetoken=6B2621AB7F; _ga=GA1.2.772285641.1612451237; _gid=GA1.2.386872807.1612451242; G_ENABLED_IDPS=google; _ncfa=EKYoaut1UyvfxCa2oUDhtyDnUrNQSdcmnXmSPRdTcG0%3dkTLueXKlmEtDHGp8Q38KPsA3qypCRq%2fY6bqaLoCAvJmcGuRhVGZl3DfKv8N0ghYC; __stripe_mid=78799611-9369-41dc-8d1d-6b2a11c657cef1fe6d";
        $count = 50;
        $result = $this->client->request(
            'GET',
            $this->apiUrl . "social/feed/" . $playerId,
            [
                'query' => [
                    'count' => $count,
                    'page'=> $page,
                ],
                'headers' => [
                    'Cookie' => $cookie,
                ]
            ]
        );
        dump($result);
        $result = json_decode($result->getContent(), true);
        if(count($result) < $count){
            error_log("recursivvve");
            array_merge($this->getPlayerGames($playerId, $page++), $result);
        }
        return $result;
    }


    public function persistGame(string $token): Response
    {
        $game = $this->gameRepo->findOneBy(['token' => $token]) ?? new GeoGuessrGame(); 

        $result = $this->getGameData($token);
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
                $this->entityManager->persist($newRound);
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
                $this->entityManager->persist($newGuess);
            }
        }

        $player = 
            $this->playerRepo->findOneBy(["geoGuessId" => $result["player"]["id"]]) 
            ?? new Player();
        $player
            ->setGeoGuessId($result["player"]["id"])
            ->setNickname($result["player"]["nick"]);
        $game->setPlayer($player);
        $game->setLastChanged(New DateTime('now'));
        $this->entityManager->persist($player);
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return new Response(
            '<html><body>Imported Game</body></html>'
        );
    }
}
