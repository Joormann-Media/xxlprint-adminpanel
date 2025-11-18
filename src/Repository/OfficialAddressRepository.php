<?php

namespace App\Repository;

use App\Entity\OfficialAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class OfficialAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfficialAddress::class);
    }

    /**
     * Flexible Suche auf allen oder ausgewählten Feldern (Live/Normal).
     * 
     * @param string|null $search Suchbegriff (Rohtext, normalisiert wird im Code)
     * @param array|null $fields  ['postcode', 'city', 'street', 'district', 'normalized']
     * @param int $limit
     * @return OfficialAddress[]
     */
    public function flexibleSearch(?string $search, ?array $fields = null, int $limit = 50): array
    {
        $qb = $this->createQueryBuilder('a');

        if ($search) {
            $searchNorm = self::normalizeSearch($search);

            // Default: alle Felder
            $fields = $fields ?: ['postcode', 'city', 'street', 'district', 'normalized'];

            $ors = [];
            $params = [];
            foreach ($fields as $idx => $field) {
                if ($field === 'normalized') {
                    $ors[] = "a.normalized LIKE :norm";
                    $params['norm'] = '%' . $searchNorm . '%';
                } else {
                    $ors[] = "LOWER(a.$field) LIKE :term$idx";
                    $params["term$idx"] = '%' . strtolower($search) . '%';
                }
            }
            $qb->andWhere(implode(' OR ', $ors))->setParameters($params);
        }

        $qb->orderBy('a.postcode', 'ASC')->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Normiert einen Such-String (für Vergleich & LIKE auf normalized-Spalte).
     */
    public static function normalizeSearch(string $input): string
    {
        $input = mb_strtolower($input);
        $input = strtr($input, [
            'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
            'é' => 'e', 'è' => 'e', 'á' => 'a', // beliebig erweiterbar
        ]);
        // Straße vereinheitlichen:
        $input = preg_replace('/\bstr\.\b/', 'strasse', $input);
        $input = preg_replace('/\bstraße\b/', 'strasse', $input);
        $input = preg_replace('/[^a-z0-9 ]+/u', '', $input); // Sonderzeichen raus
        $input = preg_replace('/\s+/', ' ', $input);
        return trim($input);
    }

    /**
     * Für deine Paginated-Listenansicht (weiterhin optional)
     */
    public function searchPaginated(?string $search = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');

        if ($search) {
            $searchNorm = self::normalizeSearch($search);
            $qb->andWhere('
                LOWER(a.postcode) LIKE :term OR
                LOWER(a.city) LIKE :term OR
                LOWER(a.street) LIKE :term OR
                LOWER(a.district) LIKE :term OR
                a.normalized LIKE :norm
            ')
            ->setParameter('term', '%' . strtolower($search) . '%')
            ->setParameter('norm', '%' . $searchNorm . '%');
        }

        return $qb->orderBy('a.postcode', 'ASC');
    }

    // Optionale Helper: Wie gehabt, leicht überarbeitet
    public function findSimilar(string $street, string $city): array
    {
        $streetNorm = self::normalizeSearch($street);
        return $this->createQueryBuilder('a')
            ->where('a.normalized LIKE :street')
            ->andWhere('LOWER(a.city) = :city')
            ->setParameter('street', '%' . $streetNorm . '%')
            ->setParameter('city', strtolower($city))
            ->getQuery()
            ->getResult();
    }

    public function findByCity(string $city): array
    {
        return $this->createQueryBuilder('a')
            ->where('LOWER(a.city) = :city')
            ->setParameter('city', strtolower($city))
            ->getQuery()
            ->getResult();
    }

    public function findCitiesByPlzPrefix(string $prefix): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.city')
            ->where('a.postcode LIKE :prefix')
            ->setParameter('prefix', $prefix . '%')
            ->orderBy('a.city', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function searchNormalized(string $term, int $limit = 20): array
{
    $normalized = OfficialAddress::buildNormalized($term, '', '', '');
    return $this->createQueryBuilder('a')
        ->where('a.normalized LIKE :needle')
        ->setParameter('needle', '%' . $normalized . '%')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

}
