<?php


namespace App\Model;


use JMS\Serializer\Annotation as Serializer;

class JsonApiDocument implements JsonApiDocumentInterface
{
    /**
     * @var JsonApiErrorInterface[] | array | null
     * @Serializer\Accessor(getter="getErrors")
     */
    private $errors;

    /**
     * @var array | object | null
     * @Serializer\Accessor(getter="getData")
     */
    private $data;

    /**
     * @var array | object
     * @Serializer\Accessor(getter="getMeta")
     */
    private $meta;

    /**
     * @var array | null
     * @Serializer\Accessor(getter="getLinks")
     */
    private $links;

    /**
     * @var array | null
     * @Serializer\Accessor(getter="getIncluded")
     */
    private $included;

    /**
     * @return array
     * @Serializer\VirtualProperty(name="jsonapi")
     */
    public static function getJsonApi(): array
    {
        return [
            'version' => '1.0',
        ];
    }

    /**
     * @return JsonApiErrorInterface[]|array|null
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param JsonApiErrorInterface[]|array|null $errors
     *
     * @return JsonApiDocumentInterface
     */
    public function setErrors($errors): JsonApiDocumentInterface
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return array|object|null
     */
    public function getData()
    {
        if (!is_null($this->getErrors()))
            return null;
        return $this->data;
    }

    /**
     * @param array|object|null $data
     *
     * @return JsonApiDocumentInterface
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array|object
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param array|object $meta
     *
     * @return JsonApiDocumentInterface
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getLinks(): ?array
    {
        return $this->links;
    }

    /**
     * @param array|null $links
     *
     * @return JsonApiDocumentInterface
     */
    public function setLinks(?array $links): JsonApiDocumentInterface
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getIncluded(): ?array
    {
        if (is_null($this->getData()))
            return null;
        return $this->included;
    }

    /**
     * @param array|null $included
     *
     * @return JsonApiDocumentInterface
     */
    public function setIncluded(?array $included): JsonApiDocumentInterface
    {
        $this->included = $included;

        return $this;
    }
}
