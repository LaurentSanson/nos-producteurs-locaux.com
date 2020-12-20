<?php

namespace App\EntityListener;

use App\Entity\Farm;
use App\Entity\Producer;
use Symfony\Component\Uid\Uuid;

/**
 * Class ProducerListener
 * @package App\EntityListener
 */
class ProducerListener
{
    public function prePersist(Producer $producer): void
    {
        $producer->setFarm(new Farm());
        $producer->getFarm()->setId(Uuid::v4());

    }
}