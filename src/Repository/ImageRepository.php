<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Imagine\Image\ImagineInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageManager
 *
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    /**
     * @var array<string>
     */
    protected array $allowedFiletypes = ['image/jpeg', 'image/png', 'image/heic'];

    /**
     * @var array<string>
     */
    protected array $nonConvertableFiletypes = ['image/jpeg', 'image/png'];


    public function __construct(
        ManagerRegistry $registry,
        protected EntityManagerInterface $entityManager,
        protected PaginatorInterface $paginator,
        protected ImagineInterface $imagine,
        protected string $filepath,
        protected string $rootPath,
    ) {
        parent::__construct($registry, Image::class);
    }

    /**
     * get paginated entry list.
     *
     * @param int $page
     * @param int $limit
     *
     * @return PaginationInterface<int, mixed>
     */
    public function getPaginatedList(int $page = 1, int $limit = 10): PaginationInterface
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
            throw new \RuntimeException('daniel.admin.error.picture.invalid');
        }

        $filename = sha1(random_int(0, 50) . $imageId . random_int(0, 50) . $file->getClientOriginalName() . random_int(0, 50));

        if (in_array($file->getMimeType(), $this->nonConvertableFiletypes, true) === false) {
            $filename .= '.jpg';
            $this->convertImage($file, $filename);
        } else {
            $filename .= '.' . $file->getClientOriginalExtension();
            $file->move($this->filepath, $filename . '.' . $file->getClientOriginalExtension());
        }

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

    public function convertImage(UploadedFile $file, string $filename): void
    {
        $filepath = $file->getPathname();

        $filename = $this->rootPath . '/public/' . $this->filepath . '/' . $filename;
        $this->imagine->open($filepath)
            ->save($filename);
    }

    public function findAllByReverseOrder(): mixed
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
