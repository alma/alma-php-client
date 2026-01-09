<?php

namespace Alma\API\Domain\Repository;

interface BusinessEventsRepositoryInterface
{
    /**
     * Check if a cart ID exist and if is not converted yet in the database.
     *
     * @param int $cartId
     * @return bool
     */
    public function alreadyExist(int $cartId): bool;
}
