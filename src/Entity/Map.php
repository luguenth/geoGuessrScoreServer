<?php

namespace App\Entity;

use App\Repository\MapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MapRepository::class)
 */
class Map
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=GeoGuessrGame::class, mappedBy="map")
     */
    private $geoGuessrGames;

    public function __construct()
    {
        $this->geoGuessrGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|GeoGuessrGame[]
     */
    public function getGeoGuessrGames(): Collection
    {
        return $this->geoGuessrGames;
    }

    public function addGeoGuessrGame(GeoGuessrGame $geoGuessrGame): self
    {
        if (!$this->geoGuessrGames->contains($geoGuessrGame)) {
            $this->geoGuessrGames[] = $geoGuessrGame;
            $geoGuessrGame->setMap($this);
        }

        return $this;
    }

    public function removeGeoGuessrGame(GeoGuessrGame $geoGuessrGame): self
    {
        if ($this->geoGuessrGames->removeElement($geoGuessrGame)) {
            // set the owning side to null (unless already changed)
            if ($geoGuessrGame->getMap() === $this) {
                $geoGuessrGame->setMap(null);
            }
        }

        return $this;
    }
}
