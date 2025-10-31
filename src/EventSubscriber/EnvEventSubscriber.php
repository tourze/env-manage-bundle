<?php

namespace Tourze\EnvManageBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Bundle\DoctrineBundle\Command\ImportMappingDoctrineCommand;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\Console\Command\RunDqlCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Symfony\Bundle\FrameworkBundle\Command\CacheWarmupCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Repository\EnvRepository;

/**
 * 我们有部分配置，是直接放到远程的，也有一些是配置到本地数据库的。
 * 之前在Kernel中提前，这种方式感觉不太可靠，还是按照 symfony 的执行 flow，使用事件来处理比较好。
 *
 * @see https://symfony.com/doc/current/messenger.html
 * @see https://symfony.com/doc/current/reference/events.html
 */
#[WithMonologChannel(channel: 'env_manage')]
#[AsEntityListener(event: Events::postPersist, method: 'removeCache', entity: Env::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'removeCache', entity: Env::class)]
#[AsEntityListener(event: Events::postRemove, method: 'removeCache', entity: Env::class)]
final class EnvEventSubscriber implements EventSubscriberInterface
{
    public const CACHE_KEY = 'custom-env';

    /**
     * @var array<string>
     */
    private static array $badEnvKeys = [
        'LD_PRELOAD', // 参考 https://www.leavesongs.com/PENETRATION/how-I-hack-bash-through-environment-injection.html 不是所有环境变量都是安全的
        'APP_',
        'DATABASE_', // 数据库地址，讲道理不应该再由此处覆盖
        'REDIS_', // Redis相关的，不应该直接被覆盖
        'JWT_', // 用户授权相关
        'MESSENGER_',
        'LOCK_',
    ];

    /**
     * @var array<string, mixed>
     */
    private array $originEnv;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EnvRepository $envRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheInterface $cache,
    ) {
        // 备份初始的配置信息
        $this->originEnv = $_ENV;
        // 这里记录的是Worker启动时的变更时间
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // 触发时间点：
            // HTTP请求
            // 每个消息队列启动前
            // 命令行执行前
            KernelEvents::REQUEST => [
                ['loadDbEnv', 99999],
            ],
            WorkerStartedEvent::class => [
                ['loadDbEnv', 99999],
            ],
            ConsoleEvents::COMMAND => [
                ['onCommand', 99999],
            ],
        ];
    }

    public function onCommand(ConsoleCommandEvent $event): void
    {
        if (
            $event->getCommand() instanceof CacheClearCommand
            || $event->getCommand() instanceof CacheWarmupCommand
            || $event->getCommand() instanceof UpdateCommand
            || $event->getCommand() instanceof RunDqlCommand
            || $event->getCommand() instanceof ValidateSchemaCommand
            || $event->getCommand() instanceof ImportMappingDoctrineCommand
        ) {
            // 正在清空缓存的话，我们不加载额外数据库配置
            return;
        }
        $this->loadDbEnv();
    }

    public function loadDbEnv(): void
    {
        $_ENV = $this->originEnv;

        // 数据库中的配置是最高优先级的
        $dbEnv = $this->cache->get(self::CACHE_KEY, $this->loadCache(...));

        $dotenv = new Dotenv();

        try {
            $dotenv->populate($dbEnv, true);
        } catch (\Throwable $exception) {
            $this->logger->error('加载数据库配置时发生错误', [
                'exception' => $exception,
            ]);
        }

        // 读取完，应该就写入到 $_ENV 的了，这里不需要了
        unset($dotenv);
    }

    /**
     * @return array<string, mixed>
     */
    public function loadCache(ItemInterface $item): array
    {
        try {
            $dbEnv = [];
            foreach ($this->envRepository->findBy(['valid' => true]) as $env) {
                foreach (self::$badEnvKeys as $badEnvKey) {
                    if (str_starts_with((string) $env->getName(), $badEnvKey)) {
                        continue 2;
                    }
                }

                $dbEnv[$env->getName()] = $env->getValue();
                $this->entityManager->detach($env); // 用完就要丢
            }
            $item->expiresAfter(60 * 60 * 24);

            return $dbEnv;
        } catch (\Throwable $exception) {
            $item->expiresAfter(1);

            return [];
        }
    }

    /**
     * 当ENV发生变化时，我们清除缓存数据
     */
    public function removeCache(Env $env): void
    {
        try {
            $this->cache->delete('GetEnvConfig_cache');
            $this->cache->delete(EnvEventSubscriber::CACHE_KEY);
        } catch (\Throwable $exception) {
            $this->logger->error('清理ENV环境失败1', [
                'exception' => $exception,
            ]);
        }
    }
}
