<?php

namespace Alma\API\Entities\Insurance;

class Contract
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $protectionDays;
    /**
     * @var string | null
     */
    private $description;
    /**
     * @var string | null
     */
    private $coverArea;
    /**
     * @var string | null
     */
    private $compensationArea;
    /**
     * @var string | null
     */
    private $exclusionArea;
    /**
     * @var string | null
     */
    private $uncoveredArea;
    /**
     * @var int
     */
    private $price;
    /**
     * @var array
     */
    private $files;

    public static $mandatoryFields = [
        'id',
        'name',
        'protection_days',
        'description',
        'cover_area',
        'compensation_area',
        'exclusion_area',
        'uncovered_area',
        'price',
        'files'
    ];

    /**
     * @param string $id
     * @param string $name
     * @param int $protectionDays
     * @param string $description
     * @param string $coverArea
     * @param string $compensationArea
     * @param string $exclusionArea
     * @param string $uncoveredArea
     * @param int $price
     * @param array $files
     */
    public function __construct(
        $id,
        $name,
        $protectionDays,
        $description,
        $coverArea,
        $compensationArea,
        $exclusionArea,
        $uncoveredArea,
        $price,
        $files
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->protectionDays = $protectionDays;
        $this->description = $description;
        $this->coverArea = $coverArea;
        $this->compensationArea = $compensationArea;
        $this->exclusionArea = $exclusionArea;
        $this->uncoveredArea = $uncoveredArea;
        $this->price = $price;
        $this->files = $files;

    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getProtectionDays()
    {
        return $this->protectionDays;
    }

    /**
     * @return string | null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string | null
     */
    public function getCoverArea()
    {
        return $this->coverArea;
    }

    /**
     * @return string | null
     */
    public function getCompensationArea()
    {
        return $this->compensationArea;
    }

    /**
     * @return string | null
     */
    public function getExclusionArea()
    {
        return $this->exclusionArea;
    }

    /**
     * @return string|null
     */
    public function getUncoveredArea()
    {
        return $this->uncoveredArea;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return int
     */
    public function getProtectionDurationInYear()
    {
        return (int)($this->protectionDays/365);
    }

    /**
     * document type exist: ipid-document | fic-document | notice-document
     * @param string $type
     * @return File|null
     */
    public function getFileByType($type)
    {
        /**
         * @var File $file
         */
        foreach ($this->files as $file) {
            if ($file->getType() === $type) {
                return new File(
                    $file->getName(),
                    $file->getType(),
                    $file->getPublicUrl()
                );
            }
        }

        return null;
    }
}
