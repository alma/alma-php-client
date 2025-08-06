<?php

namespace Alma\API\Domain;

interface OrderLineInterface {

	public function __call( $name, $arguments );
}
