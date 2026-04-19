<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ChannelApiInterface;
use AkeneoLib\Adapter\ChannelAdapter;
use AkeneoLib\Entity\Channel;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->channelApi = mock(ChannelApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ChannelAdapter($this->channelApi, $this->serializer);
});

it('yields denormalized channels from all()', function () {
    $apiChannel = ['code' => 'ecommerce'];
    $channelObj = new Channel('ecommerce');

    $cursor = resourceCursorMock([$apiChannel]);
    $this->channelApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiChannel, Channel::class)->andReturn($channelObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($channelObj);
});

it('uses query parameters in all()', function () {
    $apiChannel = ['code' => 'print'];
    $channelObj = new Channel('print');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 5]);
    $cursor = resourceCursorMock([$apiChannel]);
    $this->channelApi->shouldReceive('all')->with(100, ['limit' => 5])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiChannel, Channel::class)->andReturn($channelObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($channelObj);
});

it('gets and denormalizes a channel by code', function () {
    $apiChannel = ['code' => 'mobile'];
    $channelObj = new Channel('mobile');
    $this->channelApi->shouldReceive('get')->with('mobile')->andReturn($apiChannel);
    $this->serializer->shouldReceive('denormalize')->with($apiChannel, Channel::class)->andReturn($channelObj);

    expect($this->adapter->get('mobile'))->toBe($channelObj);
});
