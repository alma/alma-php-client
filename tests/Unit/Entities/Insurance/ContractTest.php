<?php

namespace Alma\API\Tests\Unit\Entities\Insurance;

use Alma\API\Entities\Insurance\Contract;
use Alma\API\Entities\Insurance\File;
use PHPUnit\Framework\TestCase;


class ContractTest extends TestCase
{
	const CONTRACT_FILE_NAME = 'Alma mobility 1 an (vol+casse+assistance) - Alma}';
	/**
	 * @var Contract $contract
	 */
	protected $contract;

	/**
	 * @var array[] $contractData
	 */
	protected $contractData;

	/**
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->contractData = $this->getContractData();
		$this->contract = $this->createNewContract($this->contractData);

	}

	/**
	 * @return void
	 */
	public function testConstructObject()
	{
		$this->assertSame(Contract::class, get_class($this->contract));
	}

	/**
	 * @return void
	 */
	public function testGetIdReturnId()
	{
		$this->assertEquals($this->contract->getId(), $this->contractData['id']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnName()
	{
		$this->assertEquals($this->contract->getName(), $this->contractData['name']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnProtectionDays()
	{
		$this->assertEquals($this->contract->getProtectionDays(), $this->contractData['protection_days']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnDescription()
	{
		$this->assertEquals($this->contract->getDescription(), $this->contractData['description']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnCoverArea()
	{
		$this->assertEquals($this->contract->getCoverArea(), $this->contractData['cover_area']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnCompensationArea()
	{
		$this->assertEquals($this->contract->getCompensationArea(), $this->contractData['compensation_area']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnExclusionArea()
	{
		$this->assertEquals($this->contract->getExclusionArea(), $this->contractData['exclusion_area']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnUncoveredArea()
	{
		$this->assertEquals($this->contract->getUncoveredArea(), $this->contractData['uncovered_area']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnPrice()
	{
		$this->assertEquals($this->contract->getPrice(), $this->contractData['price']);
	}

	/**
	 * @return void
	 */
	public function testGetReturnFiles()
	{
		$this->assertEquals($this->contract->getFiles(), $this->contractData['files']);
	}

	/**
	 * @dataProvider contractDataProvider
	 * @param int $days
	 * @param int $years
	 * @return void
	 */
	public function testGetProtectionDurationInYear($days, $years)
	{
		$contractData = $this->getContractData();
		$contractData['protection_days'] = $days;
		$contract = $this->createNewContract($contractData);
		$this->assertEquals($years, $contract->getProtectionDurationInYear());
	}

	/**
	 * @dataProvider fileDataProvider
	 * @param string $type
	 * @param File $file
	 * @return void
	 */
	public function testGetFileByTypeReturnType($type, $file)
	{
		$contractData = $this->getContractData();
		$contract = $this->createNewContract($contractData);
		$this->assertEquals($file, $contract->getFileByType($type));
	}

	/**
	 * @return array[]
	 */
	public static function contractDataProvider()
	{
		return [
			'duration less than 1 year' => [
				'days' => 364,
				'years' => 0
			],
			'duration 1 year' => [
				'days' => 365,
				'years' => 1
			],
			'duration 1 leap year' => [
				'days' => 366,
				'years' => 1
			],
			'duration 2 years' => [
				'days' => 730,
				'years' => 2
			],
			'duration 3 years' => [
				'days' => 1095,
				'years' => 3
			],
			'duration 3 years and 2 days' => [
				'days' => 1097,
				'years' => 3
			]
		];
	}

	/**
	 * @return array[]
	 */
	public static function fileDataProvider()
	{
		$ipidFileData = new File(
			self::CONTRACT_FILE_NAME,
			'ipid-document',
			'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/I6LK9O3XUNKNZPDTMH58IIK2HKBMRM2MIH-V0YGPECCD5Z20YIQUKXVCZYEU_TJD.pdf/OFXRU1UHY7J0CFO7X0Y24RSDMTG-W5BVB1GZRPPZFPSJRNIGGP2HXR2CEXIPBWZ-.pdf'
		);
		$ipidFile = new File($ipidFileData->getName(), $ipidFileData->getType(), $ipidFileData->getPublicUrl());
		$ficFileData = new File(
			self::CONTRACT_FILE_NAME,
			'fic-document',
			'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/Y-PSWZG6-ADZ9MEY8PAZS2TMAUBXOLU6GYOLDWULMEAJB_VW0RGBKJTPMY7SPASN.pdf/UHSB9KVIGRLHP9DMXRZNCSWUGXCHS9VOW2EHAUNCYM_ANJIE7DOAKVLIH6EEOQYW.pdf'
		);
		$ficFile = new File($ficFileData->getName(), $ficFileData->getType(), $ficFileData->getPublicUrl());
		$noticeFileData = new File(
			self::CONTRACT_FILE_NAME,
			'notice-document',
			'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/JVPHA9RROHB6RPCG9K3VFG4EELBIMALK4QY2JVYEUTBFFT4SP1YN_ZUFXHOYRUSP.pdf/YTBTRJ6C9FFQFNW3234PHJJJT28VZR0FDOXVV0HV1SULI79S3UPSYRX7SZDNX1FX.pdf'
		);
		$noticeFile = new File($noticeFileData->getName(), $noticeFileData->getType(), $noticeFileData->getPublicUrl());

		return [
			'type is ipid-document' => [
				'type' => 'ipid-document',
				'file' => $ipidFile
			],
			'type is fic-document' => [
				'type' => 'fic-document',
				'file' => $ficFile
			],
			'type is notice-document' => [
				'type' => 'notice-document',
				'file' => $noticeFile
			],
			'type is wrong string' => [
				'type' => 'unknown-document',
				'file' => null
			],
			'type is object' => [
				'type' => new \stdClass(),
				'file' => null
			],
			'type is empty' => [
				'type' => '',
				'file' => null
			],
		];
	}

	/**
	 * @param array $contractData
	 * @return Contract
	 */
	private function createNewContract($contractData)
	{
		return new Contract(
			$contractData['id'],
			$contractData['name'],
			$contractData['protection_days'],
			$contractData['description'],
			$contractData['cover_area'],
			$contractData['compensation_area'],
			$contractData['exclusion_area'],
			$contractData['uncovered_area'],
			$contractData['price'],
			$contractData['files']
		);
	}

	/**
	 * @return array
	 */
	private static function getContractData()
	{
		return [
			'id' => 'insurance_contract_3eWqr58LexAqxUAlNVKWEf',
			'name' => 'Insurance contract Name',
			'protection_days' => 365,
			'description' => 'description_value',
			'cover_area' => 'cover_area_value',
			'compensation_area' => 'compensation_area_value',
			'exclusion_area' => 'exclusion_area_value',
			'uncovered_area' => 'uncovered_area_value',
			'price' => 6000, //price in cent
			'files' => [
				new File(
					self::CONTRACT_FILE_NAME,
					'ipid-document',
					'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/I6LK9O3XUNKNZPDTMH58IIK2HKBMRM2MIH-V0YGPECCD5Z20YIQUKXVCZYEU_TJD.pdf/OFXRU1UHY7J0CFO7X0Y24RSDMTG-W5BVB1GZRPPZFPSJRNIGGP2HXR2CEXIPBWZ-.pdf'
				),
				new File(
					self::CONTRACT_FILE_NAME,
					'fic-document',
					'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/Y-PSWZG6-ADZ9MEY8PAZS2TMAUBXOLU6GYOLDWULMEAJB_VW0RGBKJTPMY7SPASN.pdf/UHSB9KVIGRLHP9DMXRZNCSWUGXCHS9VOW2EHAUNCYM_ANJIE7DOAKVLIH6EEOQYW.pdf'
				),
				new File(
					self::CONTRACT_FILE_NAME,
					'notice-document',
					'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/JVPHA9RROHB6RPCG9K3VFG4EELBIMALK4QY2JVYEUTBFFT4SP1YN_ZUFXHOYRUSP.pdf/YTBTRJ6C9FFQFNW3234PHJJJT28VZR0FDOXVV0HV1SULI79S3UPSYRX7SZDNX1FX.pdf'
				)
			]
		];
	}

}
