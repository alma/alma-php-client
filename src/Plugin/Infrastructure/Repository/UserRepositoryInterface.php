<?php

namespace Alma\Plugin\Infrastructure\Repository;

use Alma\Plugin\Infrastructure\Adapter\UserAdapterInterface;

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
