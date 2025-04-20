# Env Entity Design

This document describes the database entity design for the `Env` entity in the `env-manage-bundle`.

## Entity: Env

| Field           | Type                | Description                | Nullable | Default | Extra Info                     |
|-----------------|---------------------|----------------------------|----------|---------|-------------------------------|
| id              | BIGINT (Snowflake)  | Primary Key                | No       |         | CustomIdGenerator             |
| name            | STRING(100)         | Variable name (unique)     | No       |         | Indexed, Trackable, FormField |
| value           | TEXT                | Variable value             | Yes      |         | Trackable, FormField          |
| remark          | STRING(255)         | Remark                     | Yes      |         | Trackable, FormField          |
| sync            | BOOLEAN             | Is synchronized            | Yes      | false   | Trackable, FormField          |
| valid           | BOOLEAN             | Is valid                   | Yes      | false   | Indexed, Trackable, FormField |
| createdBy       | STRING              | Created by                 | Yes      |         | Trackable                     |
| updatedBy       | STRING              | Updated by                 | Yes      |         | Trackable                     |
| createdFromIp   | STRING(128)         | Created from IP            | Yes      |         | Trackable                     |
| updatedFromIp   | STRING(128)         | Updated from IP            | Yes      |         | Trackable                     |
| createTime      | DATETIME            | Created at                 | Yes      |         | Trackable, Indexed            |
| updateTime      | DATETIME            | Updated at                 | Yes      |         | Trackable, Indexed            |

## Design Notes

- The entity uses a Snowflake ID as the primary key to ensure distributed uniqueness.
- `name` is unique and indexed for fast lookup.
- Trackable and audit fields are included for full traceability.
- Sync and valid flags control environment variable status and propagation.
- All changes are timestamped and IP/user tracked for security and auditability.

---

# Env 实体设计

本文档描述了 `env-manage-bundle` 中 Env 实体的数据库设计。

## 实体：Env

| 字段名           | 类型                | 说明                       | 可空    | 默认值 | 备注                        |
|------------------|---------------------|----------------------------|---------|--------|-----------------------------|
| id               | BIGINT (雪花ID)     | 主键                       | 否      |        | 自定义ID生成器              |
| name             | STRING(100)         | 变量名（唯一）             | 否      |        | 有索引、可追踪、表单字段    |
| value            | TEXT                | 变量值                     | 是      |        | 可追踪、表单字段            |
| remark           | STRING(255)         | 备注                       | 是      |        | 可追踪、表单字段            |
| sync             | BOOLEAN             | 是否同步                   | 是      | false  | 可追踪、表单字段            |
| valid            | BOOLEAN             | 是否有效                   | 是      | false  | 有索引、可追踪、表单字段    |
| createdBy        | STRING              | 创建人                     | 是      |        | 可追踪                      |
| updatedBy        | STRING              | 更新人                     | 是      |        | 可追踪                      |
| createdFromIp    | STRING(128)         | 创建时IP                   | 是      |        | 可追踪                      |
| updatedFromIp    | STRING(128)         | 更新时IP                   | 是      |        | 可追踪                      |
| createTime       | DATETIME            | 创建时间                   | 是      |        | 可追踪、有索引              |
| updateTime       | DATETIME            | 更新时间                   | 是      |        | 可追踪、有索引              |

## 设计说明

- 主键采用雪花ID，保证分布式唯一性。
- `name` 字段唯一且有索引，便于快速查找。
- 包含完整的审计字段，便于追踪和安全合规。
- sync/valid 控制变量的同步和有效性。
- 所有变更均有时间戳和IP/用户信息记录，便于安全审计。
