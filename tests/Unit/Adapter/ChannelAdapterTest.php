<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ChannelApiInterface;
use AkeneoLib\Adapter\ChannelAdapter;
use AkeneoLib\Adapter\ChannelAdapterInterface;
use AkeneoLib\Entity\Channel;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->channelApi = mock(ChannelApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ChannelAdapter($this->channelApi, $this->serializer);
});

it('implements ChannelAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(ChannelAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(25);
    expect($this->adapter->getBatchSize())->toBe(25);
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);

    $channel = new Channel('ch-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->channelApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($channel);
    $this->adapter->push();
    expect($called)->toBeTrue();
});

it('yields denormalized channels from all()', function () {
    $apiChannel = ['code' => 'ch-1'];
    $channelObj = new Channel('ch-1');

    $cursor = resourceCursorMock([$apiChannel]);
    $this->channelApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiChannel, Channel::class)->andReturn($channelObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($channelObj);
});

it('uses query parameters in all()', function () {
    $apiChannel = ['code' => 'ch-2'];
    $channelObj = new Channel('ch-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 10]);
    $cursor = resourceCursorMock([$apiChannel]);
    $this->channelApi->shouldReceive('all')->with(100, ['limit' => 10])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiChannel, Channel::class)->andReturn($channelObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($channelObj);
});

it('gets and denormalizes a channel by code', function () {
    $apiChannel = ['code' => 'ch-3'];
    $channelObj = new Channel('ch-3');
    $this->channelApi->shouldReceive('get')->with('ch-3')->andReturn($apiChannel);
    $this->serializer->shouldReceive('denormalize')->with($apiChannel, Channel::class)->andReturn($channelObj);

    $result = $this->adapter->get('ch-3');
    expect($result)->toBe($channelObj);
});

it('stages channels and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $c1 = new Channel('ch-a');
    $c2 = new Channel('ch-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->channelApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($c1);
    $this->adapter->stage($c2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged channels and clears the queue', function () {
    $channel = new Channel('ch-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->channelApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($channel);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
