<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Service\ImageService;
use Liip\ImagineBundle\Message\WarmupCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class ImageController
 *
 * @Route("image")
 * @package DanielBundle\Controller
 */
#[Route('/admin/image')]
class ImageController extends AbstractController
{
    protected const array DEFAULT_IMAGINE_FILTER = [
        'image_small_webp',
        'image_small_jpg',
        'image_middle_webp',
        'image_middle_jpg',
        'image_webp',
        'image_jpg',
        'thumb',
        'thumb_webp',
    ];

    /**
     * Lists all image entities.
     *
     * @param Request $request
     * @param ImageRepository $imageRepository
     * @return Response
     */
    #[Route('/', name: 'image_index', methods: ['GET'])]
    public function indexAction(Request $request, ImageRepository $imageRepository): Response
    {
        $paginatedList = $imageRepository->getPaginatedList($request->query->getInt('page', 1), 20);

        return $this->render('image/index.html.twig', ['pagination' => $paginatedList]);
    }

    /**
     * Creates a new image entity.
     *
     * @param Request $request
     * @param ImageRepository $imageRepository
     * @param ImageService $imageService
     * @param MessageBusInterface $messageBus
     *
     * @return Response
     */
    #[Route('/new', name: 'image_new', methods: ['GET', 'POST'])]
    public function newAction(
        Request $request,
        ImageRepository $imageRepository,
        ImageService $imageService,
        MessageBusInterface $messageBus
    ): Response {
        $image = new Image();
        $form = $this->createForm(\App\Form\Type\ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                if ($data->getImage()) {
                    $filename = $imageService->uploadNewPicture($data->getImage(), $image->getId());

                    $image->setImage($filename);
                }

                $imageRepository->persist($image);

                $messageBus->dispatch(
                    new WarmupCache(
                        'uploads/images/' . $image->getImage(),
                        self::DEFAULT_IMAGINE_FILTER
                    )
                );

                return $this->redirectToRoute('image_show', ['id' => $image->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render(
            'image/new.html.twig',
            [
                'image' => $image,
                'form'  => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a image entity.
     */
    #[Route('/{id}', name: 'image_show', methods: ['GET'])]
    public function showAction(Image $image): Response
    {
        $deleteForm = $this->createDeleteForm($image);

        return $this->render('image/show.html.twig', [
            'image'       => $image,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing image entity.
     *
     * @param Request $request
     * @param Image $image
     * @param ImageRepository $imageRepository
     * @param ImageService $imageService
     * @param MessageBusInterface $messageBus
     *
     * @return Response
     */
    #[Route('/{id}/edit', name: 'image_edit', methods: ['GET', 'POST'])]
    public function editAction(
        Request $request,
        Image $image,
        ImageRepository $imageRepository,
        ImageService $imageService,
        MessageBusInterface $messageBus
    ): Response {
        $oldImage = $image->getImage();

        if ($oldImage) {
            $path = $this->getParameter('daniel.file.path');
            if (is_string($path) === false) {
                throw new \RuntimeException('The parameter "daniel.file.path" must be a string.');
            }

            $logo = new File($path . DIRECTORY_SEPARATOR . $oldImage);
            $image->setImage($logo);
        }

        $editForm = $this->createForm(\App\Form\Type\ImageType::class, $image);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $data = $editForm->getData();
                if ($data->getImage()) {
                    $filename = $imageService->uploadNewPicture($data->getImage(), $image->getId());

                    $imageService->removeOldPicture($oldImage);
                    $image->setImage($filename);
                } else {
                    $image->setImage($oldImage);
                }

                $imageRepository->flush();
                $messageBus->dispatch(
                    new WarmupCache(
                        'uploads/images/' . $image->getImage(),
                        self::DEFAULT_IMAGINE_FILTER
                    )
                );

                return $this->redirectToRoute('image_index');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('image_edit', ['id' => $image->getId()]);
            }
        }

        $deleteForm = $this->createDeleteForm($image);
        return $this->render('image/edit.html.twig', [
            'image'       => $image,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a image entity.
     *
     * @param Request $request
     * @param Image $image
     * @param ImageRepository $imageRepository
     * @param ImageService $imageService
     *
     * @return RedirectResponse
     */
    #[Route('/{id}', name: 'image_delete', methods: ['POST','DELETE'])]
    public function deleteAction(
        Request $request,
        Image $image,
        ImageRepository $imageRepository,
        ImageService $imageService
    ): Response {
        $form = $this->createDeleteForm($image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageService->removeOldPicture($image->getImage());

            $imageRepository->remove($image);
        }

        return $this->redirectToRoute('image_index');
    }

    /**
     * Creates a form to delete a image entity.
     *
     * @param Image $image The image entity
     */
    private function createDeleteForm(Image $image): \Symfony\Component\Form\FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('image_delete', ['id' => $image->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
