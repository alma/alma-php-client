<?php

namespace Alma\API\Domain\Adapter;

interface OrderLineAdapterInterface {

	public function __call( $name, $arguments );
}
