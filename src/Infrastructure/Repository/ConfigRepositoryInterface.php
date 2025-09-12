<?php

namespace Alma\API\Infrastructure\Repository;

interface ConfigRepositoryInterface
{
    /**
     * Get the value of all settings.
     *
     * @return array The array of settings, or an empty array.
     */
    public function getSettings(): array;

    /**
     * Check if the setting exists in the settings array.
     *
     * @param string $setting The setting name to check for existence.
     *
     * @return bool True if the setting exists, false otherwise.
     */
    public function hasSetting( string $setting ): bool;

    /**
     * Add or Update a specific setting value.
     *
     * @param string $setting The setting name to update.
     * @param mixed  $value The value to set for the setting.
     *
     * @return bool True if the setting was updated, false otherwise.
     */
    public function updateSetting( string $setting, $value ): bool;

    /**
     * Delete a specific setting from the settings array.
     *
     * @param string $setting The setting name to delete.
     *
     * @return bool True if the setting was deleted, false otherwise.
     */
    public function deleteSetting( string $setting ): bool;
}
