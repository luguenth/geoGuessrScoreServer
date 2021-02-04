<?php

namespace App\Entity;

use App\Repository\GuessRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GuessRepository::class)
 */
class Guess
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $lat;

    /**
     * @ORM\Column(type="float")
     */
    private $lng;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $timedOut;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $timedOutWithGuess;

    /**
     * @ORM\Column(type="float")
     */
    private $roundScoreInPercentage;

    /**
     * @ORM\Column(type="integer")
     */
    private $roundScoreInPoints;

    /**
     * @ORM\Column(type="float")
     */
    private $distanceInMeters;

    /**
     * @ORM\Column(type="float")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $countryCode;

    /**
     * @ORM\ManyToOne(targetEntity=GeoGuessrGame::class, inversedBy="guesses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $geoGuessrGame;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getTimedOut(): ?bool
    {
        return $this->timedOut;
    }

    public function setTimedOut(?bool $timedOut): self
    {
        $this->timedOut = $timedOut;

        return $this;
    }

    public function getTimedOutWithGuess(): ?bool
    {
        return $this->timedOutWithGuess;
    }

    public function setTimedOutWithGuess(?bool $timedOutWithGuess): self
    {
        $this->timedOutWithGuess = $timedOutWithGuess;

        return $this;
    }

    public function getRoundScoreInPercentage(): ?float
    {
        return $this->roundScoreInPercentage;
    }

    public function setRoundScoreInPercentage(float $roundScoreInPercentage): self
    {
        $this->roundScoreInPercentage = $roundScoreInPercentage;

        return $this;
    }

    public function getRoundScoreInPoints(): ?int
    {
        return $this->roundScoreInPoints;
    }

    public function setRoundScoreInPoints(int $roundScoreInPoints): self
    {
        $this->roundScoreInPoints = $roundScoreInPoints;

        return $this;
    }

    public function getDistanceInMeters(): ?float
    {
        return $this->distanceInMeters;
    }

    public function setDistanceInMeters(float $distanceInMeters): self
    {
        $this->distanceInMeters = $distanceInMeters;

        return $this;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function setTime(float $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getGeoGuessrGame(): ?GeoGuessrGame
    {
        return $this->geoGuessrGame;
    }

    public function setGeoGuessrGame(?GeoGuessrGame $geoGuessrGame): self
    {
        $this->geoGuessrGame = $geoGuessrGame;

        return $this;
    }
}
