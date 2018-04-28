<?php

namespace BWibrew\SiteSettings\Interfaces;

interface Setting
{
    /**
     * Update current setting name.
     *
     * @param $name
     * @return $this
     */
    public function updateName($name);

    /**
     * Update current setting value.
     *
     * @param $value
     * @param $delete_media
     * @return $this
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function updateValue($value = null, $delete_media = false);

    /**
     * Update a scope.
     *
     * @param $scope
     * @return $this
     */
    public function updateScope($scope);

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
     * @param $name
     * @param $value
     * @return $this|Model
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public static function register($name, $value = null);

    /**
     * Get a setting value.
     *
     * @param $name
     * @return mixed
     */
    public static function getValue($name);

    /**
     * Get all values in a scope.
     *
     * @param $scope
     * @return array|null
     */
    public static function getScopeValues($scope = 'default');

    /**
     * Get the 'updated_by' user ID.
     *
     * @param $name
     * @return int|null
     */
    public static function getUpdatedBy($name);

    /**
     * Get the 'updated_by' user ID for a scope.
     *
     * @param $scope
     * @return int|null
     */
    public static function getScopeUpdatedBy($scope = 'default');

    /**
     * Get the updated_at timestamp.
     *
     * @param $name
     * @return mixed
     */
    public static function getUpdatedAt($name);

    /**
     * Get the updated_at timestamp for a scope.
     *
     * @param $scope
     * @return mixed|null
     */
    public static function getScopeUpdatedAt($scope = 'default');

    /**
     * Gets the property from the setting.
     *
     * @param string $property
     * @param $name
     *
     * @return mixed
     */
    public function getProperty($property, $name);

    /**
     * Gets the property from the most recently updated setting in a scope.
     *
     * @param string $property
     * @param string $scope
     *
     * @return mixed
     */
    public function getScopeProperty($property, $scope);

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
