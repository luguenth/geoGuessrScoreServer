<?php

namespace App\Service;

use App\Entity\GeoGuessrGame;
use App\Entity\Guess;
use App\Entity\Map;
use App\Entity\Player;
use App\Entity\Round;
use App\Repository\GeoGuessrGameRepository;
use App\Repository\MapRepository;
use App\Repository\PlayerRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use http\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoGuessrApiService
{

    public function __construct(
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager,
        GeoGuessrGameRepository $geoGuessrGameRepository,
        PlayerRepository $playerRepository,
    MapRepository $mapRepository
        )
    {
        $this->client = $httpClient;
        $this->apiUrl = "https://www.geoguessr.com/api/v3/";
        $this->entityManager = $entityManager;
        $this->gameRepo = $geoGuessrGameRepository;
        $this->playerRepo = $playerRepository;
        $this->mapRepo = $mapRepository;
    }

    public function getGameData(string $token) :array
    {
        $token = $this->sanitizeInput($token);
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
        return $result;
    }

    public function getPlayerGames(string $playerId, string $cookie, int $page = 0) :array
    {
        $playerId = $this->sanitizeInput($playerId);
        $count = 100;
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
        $result = json_decode($result->getContent(), true);
        if(count($result) >= $count){
            error_log("recursivvve");
            array_merge($this->getPlayerGames($playerId, ++$page), $result);
        }
        return $result;
    }


    public function persistGame(string $token): Response
    {
        $game = $this->gameRepo->findOneBy(['token' => $token]) ?? new GeoGuessrGame(); 
        if($game->getState()==="finished") {
            return new Response(
                '<html><body>Game Already Exists</body></html>'
            );
        }
        $result = $this->getGameData($token);
        if(empty($result)){
            throw new InvalidArgumentException("Couldn't find any Game to import");
        }
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
            ->setPanoramaProvider($result["panoramaProvider"])
            ->setRound($result["round"])
            ->setTotalDistanceInMeters($result["player"]["totalDistanceInMeters"])
            ->setTotalScoreInPercentage($result["player"]["totalScore"]["percentage"])
            ->setTotalScoreInPoints($result["player"]["totalScore"]["amount"])
            ->setTotalTime($result["player"]["totalTime"]);

        $round_difference = count($result["rounds"]) - count($game->getRounds());
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
        $this->entityManager->persist($player);

        $map =
            $this->mapRepo->findOneBy(["token" => $result["map"]])
            ?? new Map();
        $map
            ->setToken($result["map"])
            ->setName($result["mapName"]);
        $game->setMap($map);
        $this->entityManager->persist($map);

        $game->setLastChanged(New DateTime('now'));

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return new Response(
            '<html><body>Imported Game</body></html>'
        );
    }

    private function sanitizeInput(string $token):string
    {
        $pattern = "/[^0-9!&',-.\\/a-zA-Z\n]/";
        return preg_replace($pattern, "", $token);
    }
}
