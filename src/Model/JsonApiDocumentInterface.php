<?php

namespace App\Model;

interface JsonApiDocumentInterface
{
    /**
     * @return array
     * @Serializer\VirtualProperty(name="jsonapi")
     */
    public static function getJsonApi(): array;

    /**
     * @return JsonApiErrorInterface[]|array|null
     */
    public function getErrors();

    /**
     * @param JsonApiErrorInterface[]|array|null $errors
     *
     * @return JsonApiDocumentInterface
     */
    public function setErrors($errors): JsonApiDocumentInterface;

    /**
     * @return array|object|null
     */
    public function getData();

    /**
     * @param array|object|null $data
     *
     * @return JsonApiDocumentInterface
     */
    public function setData($data);

    /**
     * @return array|object
     */
    public function getMeta();

    /**
     * @param array|object $meta
     *
     * @return JsonApiDocumentInterface
     */
    public function setMeta($meta);

    /**
     * @return array|null
     */
    public function getLinks(): ?array;

    /**
     * @param array|null $links
     *
     * @return JsonApiDocumentInterface
     */
    public function setLinks(?array $links): JsonApiDocumentInterface;

    /**
     * @return array|null
     */
    public function getIncluded(): ?array;

    /**
     * @param array|null $included
     *
     * @return JsonApiDocumentInterface
     */
    public function setIncluded(?array $included): JsonApiDocumentInterface;
}
