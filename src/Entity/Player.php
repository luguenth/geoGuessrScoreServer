<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
class Player
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
    private $geoGuessId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickname;

    /**
     * @ORM\OneToMany(targetEntity=GeoGuessrGame::class, mappedBy="player")
     */
    private $geoGuessrGames;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $cookie;

    public function __construct()
    {
        $this->geoGuessrGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGeoGuessId(): ?string
    {
        return $this->geoGuessId;
    }

    public function setGeoGuessId(string $geoGuessId): self
    {
        $this->geoGuessId = $geoGuessId;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

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
            $geoGuessrGame->setPlayer($this);
        }

        return $this;
    }

    public function removeGeoGuessrGame(GeoGuessrGame $geoGuessrGame): self
    {
        if ($this->geoGuessrGames->removeElement($geoGuessrGame)) {
            // set the owning side to null (unless already changed)
            if ($geoGuessrGame->getPlayer() === $this) {
                $geoGuessrGame->setPlayer(null);
            }
        }

        return $this;
    }

    public function getCookie(): ?string
    {
        return $this->cookie;
    }

    public function setCookie(?string $cookie): self
    {
        $this->cookie = $cookie;

        return $this;
    }
}
