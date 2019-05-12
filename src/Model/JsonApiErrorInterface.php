<?php

namespace App\Model;

use JsonSerializable;

interface JsonApiErrorInterface
{
    public function __construct(string $status, string $detail, string $title);

    /**
     * @return string|null
     */
    public function getAbout(): ?string;

    /**
     * @param string|null $about
     *
     * @return JsonApiErrorInterface
     */
    public function setAbout(?string $about): JsonApiErrorInterface;

    /**
     * @return array|null
     */
    public function getLinks(): ?array;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @return int|null
     */
    public function getHttpStatus(): ?int;

    /**
     * @param string|null $status
     *
     * @return JsonApiErrorInterface
     */
    public function setStatus(?string $status): JsonApiErrorInterface;

    /**
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @param string|null $code
     *
     * @return JsonApiErrorInterface
     */
    public function setCode(?string $code): JsonApiErrorInterface;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @param string|null $title
     *
     * @return JsonApiErrorInterface
     */
    public function setTitle(?string $title): JsonApiErrorInterface;

    /**
     * @return string|null
     */
    public function getDetail(): ?string;

    /**
     * @param string|null $detail
     *
     * @return JsonApiErrorInterface
     */
    public function setDetail(?string $detail): JsonApiErrorInterface;

    /**
     * @return string|null
     */
    public function getPointer(): ?string;

    /**
     * @param string|null $pointer
     *
     * @return JsonApiErrorInterface
     */
    public function setPointer(?string $pointer): JsonApiErrorInterface;

    /**
     * @return string|null
     */
    public function getParameter(): ?string;

    /**
     * @param string|null $parameter
     *
     * @return JsonApiErrorInterface
     */
    public function setParameter(?string $parameter): JsonApiErrorInterface;

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
     * @return JsonApiErrorInterface
     */
    public function setMeta(?array $meta): JsonApiErrorInterface;
}
