<?php

namespace Alma\API\DTO\MerchantData;

class MerchantDataDto
{
	/**
	 * @param CmsInfoDto $cmsInfo
	 * @param CmsFeaturesDto $cmsFeatures
	 * @return array
	 */
	public function toArray(CmsInfoDto $cmsInfo, CmsFeaturesDto $cmsFeatures): array
    {
		return [
			"cms_info" => $cmsInfo->toArray(),
			"cms_features" => $cmsFeatures->toArray(),
		];
	}
}
