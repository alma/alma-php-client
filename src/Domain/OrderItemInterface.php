<?php

namespace Alma\API\Domain;

interface OrderItemInterface {

	public function __call( $name, $arguments );
}
