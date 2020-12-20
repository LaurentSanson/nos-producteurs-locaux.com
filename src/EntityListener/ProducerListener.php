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
        $farm = new Farm();
        $farm->setId(Uuid::V4());
        $farm->setProducer($producer);
        $producer->setFarm($farm);
    }
}
