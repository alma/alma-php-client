<?php

namespace Alma\API\Domain\Repository;

use Alma\API\Domain\Adapter\UserAdapterInterface;

interface UserRepositoryInterface
{

	/**
     * Get user by ID.
     *
     * @param int $userId User ID.
     *
     * @return UserAdapterInterface
     */
    public function getById(int $userId): UserAdapterInterface;
}
