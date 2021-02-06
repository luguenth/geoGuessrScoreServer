<?php

namespace App\Controller;

use App\Service\GeoGuessrApiService;
use App\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlayerController extends AbstractController
{
    /**
     * @Route("player/", name="player_index")
     */
    public function index(Request $request, GeoGuessrApiService $geoGuessrApiService): Response
    {
        $form = $this->createFormBuilder()
            ->add('token', TextType::class, ['label' => 'Player Token'])
            ->add('cookie', TextareaType::class, ['label' => 'Player Cookie'])
            ->add('search', SubmitType::class, ['label' => 'Index my Games!'])
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $this->import($request, $data['token'], $data['cookie'], $geoGuessrApiService);

        }

        $playerRepo = $this->getDoctrine()->getRepository(Player::class);
        $players = $playerRepo->findAll();

        return $this->render('Player/index.html.twig', [
            'players' => $players,
            'form' => $form->createView(),
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
     * @param Request $request
     * @param string $id
     * @param string $cookie
     * @param GeoGuessrApiService $geoGuessrApiService
     * @return Response
     */
    public function import(Request $request, string $id, string $cookie, GeoGuessrApiService $geoGuessrApiService): Response
    {
        $allGames = $geoGuessrApiService->getPlayerGames($id, $cookie);

        foreach($allGames as $game){
            if($game["activityType"] === 3){
                $geoGuessrApiService->persistGame($game["payload"]["map"]["gameToken"]);
            }        
        }

        return new Response(
            '<html><body>Imported Player</body></html>'
        );
    }
}