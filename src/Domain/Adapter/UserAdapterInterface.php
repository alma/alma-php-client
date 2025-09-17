<?php

namespace Alma\API\Domain\Adapter;

interface UserAdapterInterface
{
    /**
     * Get the user ID.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get the display name of the user.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Check if the user can manage Alma (i.e., has 'manage_woocommerce' capability).
     *
     * @return bool
     */
    public function canManageAlma(): bool;

}
