<?php

namespace BWibrew\SiteSettings\Interfaces;

interface Setting
{
    /**
     * Update current setting name.
     *
     * @param string $name
     * @return $this
     */
    public function updateName(string $name);

    /**
     * Update current setting value.
     *
     * @param $value
     * @param $delete_media
     * @return $this
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function updateValue($value = null, bool $delete_media = false);

    /**
     * Update a scope.
     *
     * @param string $scope
     * @return $this
     */
    public function updateScope(string $scope);

    /**
     * Remove scope from setting.
     *
     * @return $this
     */
    public function removeScope();

    /**
     * Register a new setting with optional value.
     *
     * Authenticated user ID will be assigned to 'updated_by' column.
     *
     * @param string $name
     * @param $value
     * @return $this|\Illuminate\Database\Eloquent\Model
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public static function register(string $name, $value = null);

    /**
     * Get a setting value.
     *
     * @param string $name
     * @return mixed
     */
    public static function getValue(string $name);

    /**
     * Get all values in a scope.
     *
     * @param string $scope
     * @return array|null
     */
    public static function getScopeValues(string $scope = 'default');

    /**
     * Get the 'updated_by' user ID.
     *
     * @param string $name
     * @return int|null
     */
    public static function getUpdatedBy(string $name);

    /**
     * Get the 'updated_by' user ID for a scope.
     *
     * @param string $scope
     * @return int|null
     */
    public static function getScopeUpdatedBy(string $scope = 'default');

    /**
     * Get the updated_at timestamp.
     *
     * @param string $name
     * @return mixed
     */
    public static function getUpdatedAt(string $name);

    /**
     * Get the updated_at timestamp for a scope.
     *
     * @param string $scope
     * @return mixed|null
     */
    public static function getScopeUpdatedAt(string $scope = 'default');

    /**
     * Gets the property from the setting.
     *
     * @param string $property
     * @param string $name
     *
     * @return mixed
     */
    public function getProperty(string $property, string $name);

    /**
     * Gets the property from the most recently updated setting in a scope.
     *
     * @param string $property
     * @param string $scope
     *
     * @return mixed
     */
    public function getScopeProperty(string $property, string $scope);

    /**
     * Cache all Setting objects.
     */
    public function refreshCache();

    /**
     * Retrieve Setting models from the cache.
     * If the models aren't already in the cache then this
     * method will cache and then return them.
     *
     * @return $this
     */
    public function getSettings();
}
