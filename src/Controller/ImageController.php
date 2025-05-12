<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    /**
     * Lists all image entities.
     *
     * @param Request $request
     * @param ImageRepository $imageManager
     * @return Response
     */
    #[Route('/', name: 'image_index', methods: ['GET'])]
    public function indexAction(Request $request, ImageRepository $imageManager): Response
    {
        // $imageManager = $this->container->get('daniel.image.manager');
        $paginatedList = $imageManager->getPaginatedList($request->query->get('page', 1), 20);

        return $this->render('image/index.html.twig', ['pagination' => $paginatedList]);
    }

    /**
     * Creates a new image entity.
     *
     * @param Request      $request
     * @param ImageRepository $imageManager
     *
     * @return Response
     *
     * @throws \Exception
     */
    #[Route('/new', name: 'image_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request, ImageRepository $imageManager): Response
    {
        $image = new Image();
        $form = $this->createForm(\App\Form\Type\ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                if ($data->getImage()) {
                    $filename = $imageManager->uploadNewPicture($data->getImage(), $image->getId());

                    $imageManager->removeOldPicture($image->getImage());
                    $image->setImage($filename);
                }

                $imageManager->persist($image);

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
     * @param Request      $request
     * @param Image        $image
     * @param ImageRepository $imageManager
     *
     * @throws \Exception
     */
    #[Route('/{id}/edit', name: 'image_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Image $image, ImageRepository $imageManager): Response
    {
        $deleteForm = $this->createDeleteForm($image);

        $oldLogo = $image->getImage();

        if ($oldLogo) {
            $logo = new File($this->getParameter('daniel.file.path') . DIRECTORY_SEPARATOR . $oldLogo);
            $image->setImage($logo);
        }

        $editForm = $this->createForm(\App\Form\Type\ImageType::class, $image);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $data = $editForm->getData();
                if ($data->getImage()) {
                    $filename = $imageManager->uploadNewPicture($data->getImage(), $image->getId());

                    $imageManager->removeOldPicture($oldLogo);
                    $image->setImage($filename);
                } else {
                    $image->setImage($oldLogo);
                }

                $imageManager->flush();

                return $this->redirectToRoute('image_index');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('image/edit.html.twig', [
            'image'       => $image,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a image entity.
     *
     * @param Request      $request
     * @param Image        $image
     * @param ImageRepository $imageManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/{id}', name: 'image_delete', methods: ['POST','DELETE'])]
    public function deleteAction(Request $request, Image $image, ImageRepository $imageManager): Response
    {
        $form = $this->createDeleteForm($image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageManager->removeOldPicture($image->getImage());

            $imageManager->remove($image);
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
