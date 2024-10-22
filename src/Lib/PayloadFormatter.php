<?php

namespace Alma\API\Lib;

use Alma\API\Entities\MerchantData\CmsFeatures;
use Alma\API\Entities\MerchantData\CmsInfo;

class PayloadFormatter
{
	/**
	 * @param CmsInfo $cmsInfo
	 * @param CmsFeatures $cmsFeatures
	 * @return array
	 */
	public function formatConfigurationPayload(CmsInfo $cmsInfo, CmsFeatures $cmsFeatures)
	{
		return [
			"cms_info" => $cmsInfo->getProperties(),
			"cms_features" => $cmsFeatures->getProperties(),
		];
	}

}