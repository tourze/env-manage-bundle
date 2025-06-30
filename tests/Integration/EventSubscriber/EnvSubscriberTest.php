<?php

namespace Tourze\EnvManageBundle\Tests\Integration\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Contracts\Cache\CacheInterface;
use Tourze\EnvManageBundle\EventSubscriber\EnvSubscriber;
use Tourze\EnvManageBundle\Repository\EnvRepository;

class EnvSubscriberTest extends TestCase
{
    /**
     * @var LoggerInterface&MockObject
     */
    private LoggerInterface $logger;
    
    /**
     * @var EnvRepository&MockObject
     */
    private EnvRepository $envRepository;
    
    /**
     * @var EntityManagerInterface&MockObject
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * @var CacheInterface&MockObject
     */
    private CacheInterface $cache;
    
    private EnvSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->envRepository = $this->createMock(EnvRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        
        $this->subscriber = new EnvSubscriber(
            $this->logger,
            $this->envRepository,
            $this->entityManager,
            $this->cache
        );
    }

    public function testGetSubscribedEvents_returnsExpectedEvents(): void
    {
        $events = EnvSubscriber::getSubscribedEvents();
        
        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);
        $this->assertArrayHasKey(WorkerStartedEvent::class, $events);
        $this->assertArrayHasKey(ConsoleEvents::COMMAND, $events);
        
        // 验证事件优先级
        $this->assertEquals([['loadDbEnv', 99999]], $events[KernelEvents::REQUEST]);
        $this->assertEquals([['loadDbEnv', 99999]], $events[WorkerStartedEvent::class]);
        $this->assertEquals([['onCommand', 99999]], $events[ConsoleEvents::COMMAND]);
    }
    
    public function testConstructor_initializesCorrectly(): void
    {
        $this->assertInstanceOf(EnvSubscriber::class, $this->subscriber);
    }
    
    public function testImplementsEventSubscriberInterface(): void
    {
        $this->assertInstanceOf(\Symfony\Component\EventDispatcher\EventSubscriberInterface::class, $this->subscriber);
    }
}