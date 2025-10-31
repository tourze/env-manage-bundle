<?php

namespace Tourze\EnvManageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnvManageBundle\Repository\EnvRepository;

#[ORM\Entity(repositoryClass: EnvRepository::class)]
#[ORM\Table(name: 'base_env', options: ['comment' => 'env配置相关'])]
class Env implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[TrackColumn]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '名称'])]
    private string $name;

    #[Assert\NotNull]
    #[Assert\Length(max: 65535)]
    #[TrackColumn]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '参数'])]
    private ?string $value = null;

    #[Assert\Length(max: 255)]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[Assert\Type(type: 'bool')]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => 0, 'comment' => '是否同步'])]
    private ?bool $sync = false;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function isSync(): ?bool
    {
        return $this->sync;
    }

    public function setSync(?bool $sync): void
    {
        $this->sync = $sync;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'remark' => $this->remark,
            'sync' => $this->sync,
            'valid' => $this->valid,
        ];
    }
}
