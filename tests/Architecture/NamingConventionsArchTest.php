<?php

declare(strict_types=1);

arch('interfaces have Interface suffix')
    ->expect('AkeneoLib')
    ->interfaces()
    ->toHaveSuffix('Interface');

arch('exceptions have Exception suffix')
    ->expect('AkeneoLib\Exception')
    ->toHaveSuffix('Exception');

arch('traits have Trait suffix')
    ->expect('AkeneoLib')
    ->traits()
    ->toHaveSuffix('Trait');
