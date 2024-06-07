<?php

namespace Alma\API\Tests\Unit\Entities\Insurance;

use Alma\API\Entities\Insurance\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @var File $file
     */
    protected $file;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->fileData = $this->getFileData();
        $this->file = $this->createNewFile($this->fileData);
    }

    /**
     * @return void
     */
    public function testConstructObject()
    {
        $this->assertSame(File::class, get_class($this->file));
    }

    /**
     * @return void
     */
    public function testGetNameReturnName()
    {
        $this->assertEquals($this->file->getName(), $this->fileData['name']);
    }

    /**
     * @return void
     */
    public function testGetTypeReturnType()
    {
        $this->assertEquals($this->file->getType(), $this->fileData['type']);
    }

    /**
     * @return void
     */
    public function testGetPublicUrlReturnPublicUrl()
    {
        $this->assertEquals($this->file->getPublicUrl(), $this->fileData['public_url']);
    }

    /**
     * @param array $fileData
     * @return File
     */
    public function createNewFile($fileData)
    {
        return new File(
            $fileData['name'],
            $fileData['type'],
            $fileData['public_url']
        );
    }

    /**
     * @return string[]
     */
    public function getFileData()
    {
        return [
            'name' => 'Alma mobility 1 an (vol+casse+assistance) - Alma}',
            'type' => 'ipid-document',
            'public_url' => 'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/I6LK9O3XUNKNZPDTMH58IIK2HKBMRM2MIH-V0YGPECCD5Z20YIQUKXVCZYEU_TJD.pdf/OFXRU1UHY7J0CFO7X0Y24RSDMTG-W5BVB1GZRPPZFPSJRNIGGP2HXR2CEXIPBWZ-.pdf'
        ];
    }
}
