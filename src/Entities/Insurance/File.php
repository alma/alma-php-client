<?php

namespace Alma\API\Entities\Insurance;

class File
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $publicUrl;

    public static $mandatoryFields = [
        'name',
        'type',
        'public_url'
    ];

    /**
     * @param string $name
     * @param string $type
     * @param string $publicUrl
     */
    public function __construct($name, $type, $publicUrl)
    {
        $this->name = $name;
        $this->type = $type;
        $this->publicUrl = $publicUrl;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPublicUrl()
    {
        return $this->publicUrl;
    }
}
