<?php

declare(strict_types=1);

arch('all classes use strict types')
    ->expect('AkeneoLib')
    ->toUseStrictTypes();

arch('no classes extend Exception directly')
    ->expect('AkeneoLib')
    ->classes()
    ->not->toExtend('Exception')
    ->ignoring('AkeneoLib\Exception');

arch('exception classes extend RuntimeException or similar')
    ->expect('AkeneoLib\Exception')
    ->classes()
    ->toExtend('RuntimeException');

arch('no classes use extract function')
    ->expect('AkeneoLib')
    ->not->toUse('extract');

arch('no compact function usage')
    ->expect('AkeneoLib')
    ->not->toUse('compact');
