<?php

namespace Tourze\EnvManageBundle\Tests\Integration\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tourze\EnvManageBundle\Controller\Admin\EnvCrudController;
use Tourze\EnvManageBundle\Entity\Env;

class EnvCrudControllerTest extends TestCase
{
    /**
     * @var EntityManagerInterface&MockObject
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * @var UrlGeneratorInterface&MockObject
     */
    private UrlGeneratorInterface $urlGenerator;
    
    private EnvCrudController $controller;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        
        $this->controller = new EnvCrudController(
            $this->entityManager,
            $this->urlGenerator
        );
    }

    public function testGetEntityFqcn_returnsCorrectEntityClass(): void
    {
        $this->assertSame(Env::class, EnvCrudController::getEntityFqcn());
    }

    public function testConfigureCrud_setsCorrectConfiguration(): void
    {
        $crud = $this->createMock(\EasyCorp\Bundle\EasyAdminBundle\Config\Crud::class);
        
        $crud->expects($this->once())
            ->method('setEntityLabelInSingular')
            ->with('环境变量')
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('setEntityLabelInPlural')
            ->with('环境变量列表')
            ->willReturnSelf();
            
        $crud->expects($this->exactly(4))
            ->method('setPageTitle')
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('setDefaultSort')
            ->with(['createTime' => 'DESC'])
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('setSearchFields')
            ->with(['id', 'name', 'value', 'remark'])
            ->willReturnSelf();
            
        $crud->expects($this->once())
            ->method('setHelp')
            ->with('index', '管理系统中的环境变量，有效且同步的环境变量将被加载到系统中')
            ->willReturnSelf();
            
        $result = $this->controller->configureCrud($crud);
        $this->assertSame($crud, $result);
    }

}