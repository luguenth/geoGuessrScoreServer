<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoundRepository::class)
 */
class Round
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $panoId;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $heading;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pitch;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $zoom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $countryCode;

    /**
     * @ORM\ManyToOne(targetEntity=GeoGuessrGame::class, inversedBy="rounds")
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

    public function getPanoId(): ?string
    {
        return $this->panoId;
    }

    public function setPanoId(?string $panoId): self
    {
        $this->panoId = $panoId;

        return $this;
    }

    public function getHeading(): ?float
    {
        return $this->heading;
    }

    public function setHeading(?float $heading): self
    {
        $this->heading = $heading;

        return $this;
    }

    public function getPitch(): ?float
    {
        return $this->pitch;
    }

    public function setPitch(?float $pitch): self
    {
        $this->pitch = $pitch;

        return $this;
    }

    public function getZoom(): ?float
    {
        return $this->zoom;
    }

    public function setZoom(?float $zoom): self
    {
        $this->zoom = $zoom;

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
