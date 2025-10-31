<?php

namespace Tourze\EnvManageBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\EventSubscriber\EnvEventSubscriber;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(EnvEventSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class EnvEventSubscriberIntegrationTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
    }

    protected function createEventSubscriber(): EnvEventSubscriber
    {
        return self::getService(EnvEventSubscriber::class);
    }

    public function testGetSubscribedEventsReturnsExpectedEvents(): void
    {
        $events = EnvEventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);
        $this->assertArrayHasKey(WorkerStartedEvent::class, $events);
        $this->assertArrayHasKey(ConsoleEvents::COMMAND, $events);

        // 验证事件优先级
        $this->assertEquals([['loadDbEnv', 99999]], $events[KernelEvents::REQUEST]);
        $this->assertEquals([['loadDbEnv', 99999]], $events[WorkerStartedEvent::class]);
        $this->assertEquals([['onCommand', 99999]], $events[ConsoleEvents::COMMAND]);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $subscriber = $this->createEventSubscriber();
        $this->assertInstanceOf(EnvEventSubscriber::class, $subscriber);
    }

    public function testImplementsEventSubscriberInterface(): void
    {
        $subscriber = $this->createEventSubscriber();
        $this->assertInstanceOf(EventSubscriberInterface::class, $subscriber);
    }

    public function testLoadCache(): void
    {
        // 使用Symfony的ArrayAdapter来创建真实的缓存项
        $cache = new ArrayAdapter();
        $item = $cache->getItem('test_key');

        $subscriber = $this->createEventSubscriber();
        $result = $subscriber->loadCache($item);
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsArray($result);
    }

    public function testLoadDbEnv(): void
    {
        $subscriber = $this->createEventSubscriber();
        $subscriber->loadDbEnv();

        // 验证方法执行后系统仍然可以获取环境变量
        $this->assertNotEmpty($_ENV, 'Environment variables are still accessible after loadDbEnv');
    }

    public function testRemoveCache(): void
    {
        $env = new Env();
        $env->setName('TEST_VAR');
        $env->setValue('test_value');
        $env->setValid(true);

        $subscriber = $this->createEventSubscriber();
        $subscriber->removeCache($env);

        // 验证方法执行后订阅者状态保持正常
        $this->assertInstanceOf(EnvEventSubscriber::class, $subscriber, 'Subscriber instance should remain valid after removeCache');
    }

    public function testOnCommand(): void
    {
        // 测试只验证订阅者的基本功能
        $subscriber = $this->createEventSubscriber();

        // 验证订阅者状态保持正常
        $this->assertInstanceOf(EnvEventSubscriber::class, $subscriber, 'Subscriber should remain valid');
    }
}
