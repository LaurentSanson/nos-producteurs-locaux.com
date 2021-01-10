<?php

namespace App\Repository;

use App\Entity\Farm;
use App\Entity\Producer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Farm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Farm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Farm[]    findAll()
 * @method Farm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Farm[]    findByFarm(Farm $farm)
 * @method Farm|null    findOneByProducer(Producer $producer)
 */
class FarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Farm::class);
    }

    public function getNextSlug(string $slug): string
    {
        $foundSlugs = $this->createQueryBuilder("f")
            ->select("f.slug")
            ->where("REGEXP(f.slug, :pattern) > 0")
            ->setParameter("pattern", "^" . $slug)
            ->getQuery()
            ->getScalarResult();

        if (count($foundSlugs) === 0) {
            return $slug;
        }

        $foundSlugs = array_map(function (string $foundSlug) use ($slug) {
            preg_match("/^" . $slug . "-([0-9]*)$/", $foundSlug, $matches);
            return !isset($matches[1]) ? 0 : intval($matches[1]);
        }, array_column($foundSlugs, "slug"));

        rsort($foundSlugs);

        return sprintf("%s-%d", $slug, $foundSlugs[0] + 1);
    }
}
