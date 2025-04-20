# EnvManageBundle 工作流程（Mermaid）

```mermaid
flowchart TD
    A[应用启动/请求/命令行] --> B{触发事件}
    B -->|KernelEvents::REQUEST| C[EnvSubscriber 加载数据库环境变量]
    B -->|WorkerStartedEvent| C
    B -->|ConsoleEvents::COMMAND| D{判断命令类型}
    D -->|缓存相关命令| E[跳过加载数据库环境变量]
    D -->|其他命令| C
    C --> F[EnvRepository 查询有效且同步的环境变量]
    F --> G[设置到 $_ENV]
    G --> H[应用使用环境变量]
    subgraph 数据库
        I[Env 实体表]
    end
    F --> I
```

## 说明

- EnvSubscriber 监听 Symfony 的关键事件，在 HTTP 请求、消息队列 Worker 启动、命令行执行前加载数据库中的环境变量。
- 只加载有效（valid=true）且需要同步（sync=true）的变量。
- 某些命令（如缓存清理等）会跳过加载。
- 环境变量最终写入 $_ENV，供应用后续流程使用。
