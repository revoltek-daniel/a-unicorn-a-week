<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class DefaultController
 *
 * @package DanielBundle\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @param ImageRepository $imageRepository
     *
     * @return Response
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function indexAction(ImageRepository $imageRepository): Response
    {
        $image = $imageRepository->getLastEntry();

        if ($image instanceof Image) {
            $beforeEntry = $imageRepository->getBeforeEntry($image->getId());
        }

        return $this->render(
            'default/index.html.twig',
            [
                'image' => $image,
                'before' => $beforeEntry ?? null,
            ]
        );
    }

    /**
     * @param Image $image
     * @param ImageRepository $imageRepository
     *
     * @return Response
     */
    #[Route('/image/{id}/{slug}', name: 'image_detail', methods: ['GET'])]
    public function showAction(Image $image, ImageRepository $imageRepository): Response
    {
        $beforeEntry = $imageRepository->getBeforeEntry($image->getId());
        $nextEntry = $imageRepository->getNextEntry($image->getId());

        return $this->render('default/show.html.twig', [
            'image' => $image,
            'before' => $beforeEntry,
            'next' => $nextEntry,
        ]);
    }

    /**
     *
     * @param ImageRepository $imageRepository
     *
     * @return Response
     */
    #[Route('/rss', name: 'rss', methods: ['GET'])]
    public function rssAction(ImageRepository $imageRepository): Response
    {
        $images = $imageRepository->findAllByReverseOrder();

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render(
            "default/rss.xml.twig",
            [
                'images' => $images,
            ],
            $response
        );
    }

    #[Route('/overview', name: 'image_overview', methods: ['GET'])]
    public function overviewAction(ImageRepository $imageRepository): Response
    {
        $images = $imageRepository->findAll();

        return $this->render('default/overview.html.twig', [
            'images' => $images,
        ]);
    }

    #[Route('/admin', name: 'admin_index', methods: ['GET'])]
    public function adminIndex(): Response
    {
        return $this->redirectToRoute('security_login');
    }
}
