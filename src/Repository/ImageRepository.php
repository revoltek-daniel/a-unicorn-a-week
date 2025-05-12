<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ImageManager
 *
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        protected PaginatorInterface $paginator,
        protected EntityManagerInterface $entityManager,
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
