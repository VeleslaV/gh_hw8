<?php

namespace Veles\HomeWorkBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GbookRepository extends EntityRepository
{
    public function findAllOrderedById()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM VelesHomeWorkBundle:Gbook p ORDER BY p.id DESC'
            )
            ->getResult();
    }
}
