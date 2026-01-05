<?php

namespace Alma\API\Domain\Repository;

interface BusinessEventsRepositoryInterface
{
    /**
     * Create the necessary table in the database.
     *
     * @return void
     */
    public function createTable(): void;

    /**
     * Check if a cart ID exist and if is not converted yet in the database.
     *
     * @param int $cartId
     * @return bool
     */
    public function isCartIdValid(int $cartId): bool;
}
