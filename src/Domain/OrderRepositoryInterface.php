<?php

namespace Alma\API\Domain;

interface OrderRepositoryInterface {
    public function findById(int $orderId): OrderInterface;
}