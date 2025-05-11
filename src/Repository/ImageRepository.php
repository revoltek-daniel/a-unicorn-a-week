<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageManager
 *
 * @package DanielBundle\Manager
 */
class ImageRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManager
     */
    protected EntityManager $entityManager;

    /**
     * @var PaginatorInterface
     */
    protected PaginatorInterface $paginator;

    /**
     * @var array
     */
    protected array $repository;

    /**
     * @var array
     */
    protected array $allowedFiletypes = array('image/jpeg', 'image/png');

    /**
     * @var string
     */
    private string $filepath;

    /**
     * @param EntityManager $entityManager
     * @param PaginatorInterface     $paginator
     * @param string        $filepath
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, PaginatorInterface $paginator, string $filepath)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->filepath = $filepath;

        parent::__construct($registry, Image::class);
    }

    /**
     * get paginated entry list.
     *
     * @param int $page
     * @param int $limit
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPaginatedList(int $page = 1, int $limit = 10)
    {
        $qb = $this->createQueryBuilder('image');
        $pagination = $this->paginator->paginate(
            $qb,
            $page,
            $limit
        );

        return $pagination;
    }

    public function uploadNewPicture(UploadedFile $file, ?int $imageId): string
    {
        if (!in_array($file->getMimeType(), $this->allowedFiletypes)) {
            throw new \Exception('tubemesh.user.edit.picture.invalid');
        }

        $filepath = $this->filepath;
        $filename = sha1(random_int(0, 50) . $imageId . random_int(0, 50) . $file->getClientOriginalName() . random_int(0, 50)) . '.' . $file->getClientOriginalExtension();

        $file->move($filepath, $filename);

        $this->resizePictures($filepath, $filename);

        return $filename;
    }

    /**
     * Remove Old File.
     *
     * @param string $oldFile
     *
     * @return void
     */
    public function removeOldPicture(string $oldFile): void
    {
        $file = $this->filepath . DIRECTORY_SEPARATOR . $oldFile;
        if ($oldFile && file_exists($file)) {
            unlink($file);
            $thumb = $this->filepath . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($thumb)) {
                unlink($thumb);
            }
        }
    }

     /**
     * Resize picture and save thumb.
     *
     * @param string $filepath
     * @param string $filename
     *
     * @return void
     */
    protected function resizePictures(string $filepath, string $filename): void
    {
        $fullpath = $filepath . DIRECTORY_SEPARATOR . $filename;

        $thumbPath = $filepath . DIRECTORY_SEPARATOR . 'thumb';
        if (!is_dir($thumbPath)) {
            if (!mkdir($thumbPath) && !is_dir($thumbPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $thumbPath));
            }
        }

        $imagine = new Imagine();

        $image = $imagine->open($fullpath);
        $image->resize(new Box(120, 120))
        ->save($thumbPath . DIRECTORY_SEPARATOR . $filename);

        $image = $imagine->open($fullpath);
        $image->resize(new Box(900, 600))
        ->save($fullpath);
    }

    public function findAllByReverseOrder()
    {
        $qb = $this->createQueryBuilder('image');
        $qb->orderBy('image.id', 'DESC');

        return $qb->getQuery()->execute();
    }

    public function getLastEntry(): ?Image
    {
        $qb = $this->createQueryBuilder('image');

        $qb->orderBy('image.id', 'DESC');
        $qb->setMaxResults(1);


        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getNextEntry(int $id): ?Image
    {
        $qb = $this->createQueryBuilder('image');
        $qb->where('image.id > :id')
            ->setParameter('id', $id);

        $qb->orderBy('image.id', 'ASC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getBeforeEntry(int $id): ?Image
    {
        $qb = $this->createQueryBuilder('image');
        $qb->where('image.id < :id')
            ->setParameter('id', $id);

        $qb->orderBy('image.id', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function persist(Image $image): void
    {
        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }

    public function remove(Image $image): void
    {
        $this->entityManager->remove($image);
        $this->entityManager->flush();
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
