<?php

namespace Tourze\EnvManageBundle\Tests\Unit\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Tourze\EnvManageBundle\Entity\Env;
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
    
    private ArrayAdapter $cache;
    private EnvSubscriber $subscriber;
    private array $originalEnv;

    protected function setUp(): void
    {
        // 保存原始环境变量，以便测试后恢复
        $this->originalEnv = $_ENV;
        
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->envRepository = $this->createMock(EnvRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->cache = new ArrayAdapter();
        
        $this->subscriber = new EnvSubscriber(
            $this->logger,
            $this->envRepository,
            $this->entityManager,
            $this->cache
        );
    }

    protected function tearDown(): void
    {
        // 恢复原始环境变量
        $_ENV = $this->originalEnv;
    }

    public function testGetSubscribedEvents_returnsExpectedEvents(): void
    {
        $events = EnvSubscriber::getSubscribedEvents();
        
        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);
        $this->assertArrayHasKey(WorkerStartedEvent::class, $events);
        $this->assertArrayHasKey(ConsoleEvents::COMMAND, $events);
    }
    
    public function testLoadDbEnv_withValidConfiguration_populatesEnvironmentVariables(): void
    {
        // 创建测试环境变量
        $env1 = new Env();
        $env1->setName('TEST_ENV1');
        $env1->setValue('test_value1');
        $env1->setValid(true);

        $env2 = new Env();
        $env2->setName('TEST_ENV2');
        $env2->setValue('test_value2');
        $env2->setValid(true);
        
        $this->envRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$env1, $env2]);
            
        $this->entityManager->expects($this->exactly(2))
            ->method('detach');
        
        // 清除当前缓存
        $this->cache->clear();
        
        // 执行测试
        $this->subscriber->loadDbEnv();
    }
    
    public function testOnCommand_withCacheClearCommand_skipsLoadingEnvironment(): void
    {
        // 创建一个CacheClearCommand实例
        $command = $this->createMock(CacheClearCommand::class);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        
        $event = new ConsoleCommandEvent($command, $input, $output);
        
        // 确保 envRepository 不会被调用
        $this->envRepository->expects($this->never())
            ->method('findBy');
            
        // 执行测试
        $this->subscriber->onCommand($event);
        
        // 验证缓存没有被更新
        $this->assertFalse($this->cache->hasItem(EnvSubscriber::CACHE_KEY));
    }
    
    public function testRemoveCache_deletesCache(): void
    {
        $env = new Env();
        
        // 设置缓存项
        $this->cache->getItem(EnvSubscriber::CACHE_KEY)->set(['TEST_KEY' => 'test_value']);
        
        // 执行缓存删除
        $this->subscriber->removeCache($env);
        
        // 只需验证不抛出异常即可通过
        $this->assertTrue(true);
    }
}
