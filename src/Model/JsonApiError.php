<?php

namespace App\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class JsonApiError
 *
 * @package App\Model
 */
class JsonApiError implements JsonApiErrorInterface
{
    /**
     * @var string | null
     * @Serializer\Exclude()
     */
    private $about;

    /**
     * @var string | null
     */
    private $status;

    /**
     * @var string | null
     */
    private $code;

    /**
     * @var string | null
     */
    private $title;

    /**
     * @var string | null
     */
    private $detail;

    /**
     * @var string | null
     * @Serializer\Exclude()
     */
    private $pointer;

    /**
     * @var string | null
     * @Serializer\Exclude()
     */
    private $parameter;

    /**
     * @var array | null
     */
    private $meta;

    /**
     * JsonApiError constructor.
     *
     * @param string      $status
     * @param string|null $detail
     * @param string|null $title
     */
    public function __construct(string $status, string $detail = null, string $title = null)
    {
        $this->setStatus($status);
        $this->setDetail($detail);
        $this->setTitle($title);
    }

    /**
     * @return string|null
     */
    public function getAbout(): ?string
    {
        return $this->about;
    }

    /**
     * @param string|null $about
     *
     * @return JsonApiErrorInterface
     */
    public function setAbout(?string $about): JsonApiErrorInterface
    {
        $this->about = $about;

        return $this;
    }

    /**
     * @return array|null
     * @Serializer\VirtualProperty()
     */
    public function getLinks(): ?array
    {
        if ($this->getAbout())
            return [
                'links' => $this->getAbout(),
            ];
        return null;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getHttpStatus(): ?int
    {
        $status = intval($this->getStatus());
        if ($status <= 0)
            return null;
        return $status;
    }

    /**
     * @param string|null $status
     *
     * @return JsonApiErrorInterface
     */
    public function setStatus(?string $status): JsonApiErrorInterface
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     *
     * @return JsonApiErrorInterface
     */
    public function setCode(?string $code): JsonApiErrorInterface
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return JsonApiErrorInterface
     */
    public function setTitle(?string $title): JsonApiErrorInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDetail(): ?string
    {
        return $this->detail;
    }

    /**
     * @param string|null $detail
     *
     * @return JsonApiErrorInterface
     */
    public function setDetail(?string $detail): JsonApiErrorInterface
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPointer(): ?string
    {
        return $this->pointer;
    }

    /**
     * @param string|null $pointer
     *
     * @return JsonApiErrorInterface
     */
    public function setPointer(?string $pointer): JsonApiErrorInterface
    {
        $this->pointer = $pointer;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getParameter(): ?string
    {
        return $this->parameter;
    }

    /**
     * @param string|null $parameter
     *
     * @return JsonApiErrorInterface
     */
    public function setParameter(?string $parameter): JsonApiErrorInterface
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * @return array|null
     * @Serializer\VirtualProperty()
     */
    public function getSource(): ?array
    {
        $source = [];
        if ($this->getPointer())
            $source['pointer'] = $this->getPointer();
        if ($this->getParameter())
            $source['parameter'] = $this->getParameter();
        if (empty($source))
            return null;
        return $source;
    }

    /**
     * @return array|null
     */
    public function getMeta(): ?array
    {
        return $this->meta;
    }

    /**
     * @param array|null $meta
     *
     * @return JsonApiErrorInterface
     */
    public function setMeta(?array $meta): JsonApiErrorInterface
    {
        $this->meta = $meta;

        return $this;
    }
}
