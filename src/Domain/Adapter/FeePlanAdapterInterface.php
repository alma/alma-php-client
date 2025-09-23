<?php

namespace Alma\API\Domain\Adapter;

use Alma\API\Infrastructure\Exception\ParametersException;

interface FeePlanAdapterInterface {
	/**
	 * Get the minimum purchase amount allowed for this fee plan.
	 * @return int
	 */
	public function getOverrideMinPurchaseAmount(): int;

	/**
	 * Set a local override to the minimum purchase amount allowed for this fee plan.
	 *
	 * @param int $overrideMinPurchaseAmount Amount in cents
	 *
	 * @return void
	 * @throws ParametersException
	 */
	public function setOverrideMinPurchaseAmount( int $overrideMinPurchaseAmount ): void;

	/**
	 * Get the maximum purchase amount allowed for this fee plan.
	 * @return int
	 */
	public function getOverrideMaxPurchaseAmount(): int;

	/**
	 * Set a local override to the maximum purchase amount allowed for this fee plan.
	 *
	 * @param int $overrideMaxPurchaseAmount Amount in cents
	 *
	 * @return void
	 * @throws ParametersException
	 */
	public function setOverrideMaxPurchaseAmount( int $overrideMaxPurchaseAmount ): void;
}
