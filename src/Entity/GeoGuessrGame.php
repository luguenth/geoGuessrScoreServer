<?php

namespace App\Entity;

use App\Repository\GeoGuessrGameRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GeoGuessrGameRepository::class)
 */
class GeoGuessrGame
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(type="integer")
     */
    private $roundCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeLimit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forbidMoving;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forbidZooming;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forbidRotating;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $panoramaProvider;

    /**
     * @ORM\Column(type="integer")
     */
    private $round;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalScoreInPoints;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalScoreInPercentage;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalDistanceInMeters;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTime;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="geoGuessrGames")
     */
    private $player;

    /**
     * @ORM\OneToMany(targetEntity=Guess::class, mappedBy="geoGuessrGame", orphanRemoval=true)
     */
    private $guesses;

    /**
     * @ORM\OneToMany(targetEntity=Round::class, mappedBy="geoGuessrGame")
     */
    private $rounds;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastChanged;

    /**
     * @ORM\ManyToOne(targetEntity=Map::class, inversedBy="geoGuessrGames")
     * @ORM\JoinColumn(nullable=false)
     */
    private $map;

    public function __construct()
    {
        $this->guesses = new ArrayCollection();
        $this->rounds = new ArrayCollection();
        $this->created = new DateTime('now');
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getRoundCount(): ?int
    {
        return $this->roundCount;
    }

    public function setRoundCount(int $roundCount): self
    {
        $this->roundCount = $roundCount;

        return $this;
    }

    public function getTimeLimit(): ?int
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(int $timeLimit): self
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    public function getForbidMoving(): ?bool
    {
        return $this->forbidMoving;
    }

    public function setForbidMoving(bool $forbidMoving): self
    {
        $this->forbidMoving = $forbidMoving;

        return $this;
    }

    public function getForbidZooming(): ?bool
    {
        return $this->forbidZooming;
    }

    public function setForbidZooming(bool $forbidZooming): self
    {
        $this->forbidZooming = $forbidZooming;

        return $this;
    }

    public function getForbidRotating(): ?bool
    {
        return $this->forbidRotating;
    }

    public function setForbidRotating(bool $forbidRotating): self
    {
        $this->forbidRotating = $forbidRotating;

        return $this;
    }

    public function getPanoramaProvider(): ?int
    {
        return $this->panoramaProvider;
    }

    public function setPanoramaProvider(?int $panoramaProvider): self
    {
        $this->panoramaProvider = $panoramaProvider;

        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getTotalScoreInPoints(): ?int
    {
        return $this->totalScoreInPoints;
    }

    public function setTotalScoreInPoints(?int $totalScoreInPoints): self
    {
        $this->totalScoreInPoints = $totalScoreInPoints;

        return $this;
    }

    public function getTotalScoreInPercentage(): ?float
    {
        return $this->totalScoreInPercentage;
    }

    public function setTotalScoreInPercentage(?float $totalScoreInPercentage): self
    {
        $this->totalScoreInPercentage = $totalScoreInPercentage;

        return $this;
    }

    public function getTotalDistanceInMeters(): ?float
    {
        return $this->totalDistanceInMeters;
    }

    public function setTotalDistanceInMeters(?float $totalDistanceInMeters): self
    {
        $this->totalDistanceInMeters = $totalDistanceInMeters;

        return $this;
    }

    public function getTotalTime(): ?float
    {
        return $this->totalTime;
    }

    public function setTotalTime(?float $totalTime): self
    {
        $this->totalTime = $totalTime;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    /**
     * @return Collection|Guess[]
     */
    public function getGuesses(): Collection
    {
        return $this->guesses;
    }

    public function addGuess(Guess $guess): self
    {
        if (!$this->guesses->contains($guess)) {
            $this->guesses[] = $guess;
            $guess->setGeoGuessrGame($this);
        }

        return $this;
    }

    public function removeGuess(Guess $guess): self
    {
        if ($this->guesses->removeElement($guess)) {
            // set the owning side to null (unless already changed)
            if ($guess->getGeoGuessrGame() === $this) {
                $guess->setGeoGuessrGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function addRound(Round $round): self
    {
        if (!$this->rounds->contains($round)) {
            $this->rounds[] = $round;
            $round->setGeoGuessrGame($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->removeElement($round)) {
            // set the owning side to null (unless already changed)
            if ($round->getGeoGuessrGame() === $this) {
                $round->setGeoGuessrGame(null);
            }
        }

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getLastChanged(): ?\DateTimeInterface
    {
        return $this->lastChanged;
    }

    public function setLastChanged(\DateTimeInterface $lastChanged): self
    {
        $this->lastChanged = $lastChanged;

        return $this;
    }

    public function getStateEmoji(): ?string
    {
        switch($this->state){
            case 'finished': return "🏁";
            case 'started': return "⌛";
            default: return "❓";
        }

    }

    public function getGameModeShort(): ?string
    {
        
        $gamemode = "";

        if($this->forbidMoving || $this->forbidRotating || $this->forbidZooming){
            $gamemode = "N";
            $gamemode .= ($this->forbidMoving) ? 'M':'';
            $gamemode .= ($this->forbidMoving) ? 'P':'';
            $gamemode .= ($this->forbidMoving) ? 'Z':'';
        } else {
            $gamemode .="No Rules";
        }

        $gamemode .= ($this->timeLimit == 0) ? "" : ' ' . $this->timeLimit . 's';

        return $gamemode;

    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): self
    {
        $this->map = $map;

        return $this;
    }
}
