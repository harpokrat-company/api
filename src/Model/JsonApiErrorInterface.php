<?php

namespace App\Model;

interface JsonApiErrorInterface
{
    /**
     * @return string|null
     */
    public function getAbout(): ?string;

    /**
     * @param string|null $about
     *
     * @return JsonApiError
     */
    public function setAbout(?string $about): JsonApiError;

    /**
     * @return array|null
     */
    public function getLinks(): ?array;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string|null $status
     *
     * @return JsonApiError
     */
    public function setStatus(?string $status): JsonApiError;

    /**
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @param string|null $code
     *
     * @return JsonApiError
     */
    public function setCode(?string $code): JsonApiError;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @param string|null $title
     *
     * @return JsonApiError
     */
    public function setTitle(?string $title): JsonApiError;

    /**
     * @return string|null
     */
    public function getDetail(): ?string;

    /**
     * @param string|null $detail
     *
     * @return JsonApiError
     */
    public function setDetail(?string $detail): JsonApiError;

    /**
     * @return string|null
     */
    public function getPointer(): ?string;

    /**
     * @param string|null $pointer
     *
     * @return JsonApiError
     */
    public function setPointer(?string $pointer): JsonApiError;

    /**
     * @return string|null
     */
    public function getParameter(): ?string;

    /**
     * @param string|null $parameter
     *
     * @return JsonApiError
     */
    public function setParameter(?string $parameter): JsonApiError;

    /**
     * @return array|null
     */
    public function getSource(): ?array;

    /**
     * @return array|null
     */
    public function getMeta(): ?array;

    /**
     * @param array|null $meta
     *
     * @return JsonApiError
     */
    public function setMeta(?array $meta): JsonApiError;
}
