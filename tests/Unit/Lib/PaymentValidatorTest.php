<?php

namespace Alma\API\Tests\Unit\Lib;

use Alma\API\Lib\PaymentValidator;
use Alma\API\Lib\RequestUtils;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

class PaymentValidatorTest extends MockeryTestCase
{
    /**
     * @var PaymentValidator
     */
    protected $paymentValidator;

    public function setUp(): void
    {
        $this->paymentValidator = new PaymentValidator();
    }
}
