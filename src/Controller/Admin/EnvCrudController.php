<?php

namespace Tourze\EnvManageBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tourze\EnvManageBundle\Entity\Env;

/**
 * 环境变量管理控制器
 */
class EnvCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Env::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('环境变量')
            ->setEntityLabelInPlural('环境变量列表')
            ->setPageTitle('index', '环境变量管理')
            ->setPageTitle('new', '新增环境变量')
            ->setPageTitle('edit', fn (Env $env) => sprintf('编辑环境变量: %s', $env->getName()))
            ->setPageTitle('detail', fn (Env $env) => sprintf('环境变量详情: %s', $env->getName()))
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['id', 'name', 'value', 'remark'])
            ->setHelp('index', '管理系统中的环境变量，有效且同步的环境变量将被加载到系统中');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '变量名'))
            ->add(TextFilter::new('value', '变量值'))
            ->add(TextFilter::new('remark', '备注'))
            ->add(BooleanFilter::new('sync', '是否同步'))
            ->add(BooleanFilter::new('valid', '是否有效'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'));
    }

    public function configureActions(Actions $actions): Actions
    {
        $copyAction = Action::new('copyEnv', '复制')
            ->linkToCrudAction('copyEnv')
            ->setCssClass('btn btn-secondary')
            ->setIcon('fa fa-copy');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $copyAction)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm()
            ->setMaxLength(9999);

        yield TextField::new('name', '变量名')
            ->setRequired(true)
            ->setHelp('变量名是唯一的，用于标识环境变量');

        yield TextareaField::new('value', '变量值')
            ->setRequired(true)
            ->setHelp('变量的值，会被加载到系统环境变量中')
            ->formatValue(function ($value) {
                if (strlen($value) > 100) {
                    return substr($value, 0, 100) . '...';
                }
                return $value;
            });

        yield TextField::new('remark', '备注')
            ->setRequired(false)
            ->setHelp('可以添加描述说明这个环境变量的用途');

        yield BooleanField::new('sync', '是否同步')
            ->setRequired(false)
            ->setHelp('设置为是时，该变量会被加载到系统中');

        yield BooleanField::new('valid', '是否有效')
            ->setRequired(false)
            ->setHelp('设置为否时，该变量将被禁用');

        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextField::new('createdBy', '创建人');
            yield TextField::new('updatedBy', '更新人');
            yield TextField::new('createdFromIp', '创建IP');
            yield TextField::new('updatedFromIp', '更新IP');
            yield DateTimeField::new('createTime', '创建时间')
                ->setFormat('yyyy-MM-dd HH:mm:ss');
            yield DateTimeField::new('updateTime', '更新时间')
                ->setFormat('yyyy-MM-dd HH:mm:ss');
        } else {
            yield TextField::new('createdBy', '创建人')
                ->hideOnForm()
                ->hideOnIndex();

            yield TextField::new('updatedBy', '更新人')
                ->hideOnForm()
                ->hideOnIndex();

            yield TextField::new('createdFromIp', '创建IP')
                ->hideOnForm()
                ->hideOnIndex();

            yield TextField::new('updatedFromIp', '更新IP')
                ->hideOnForm()
                ->hideOnIndex();

            yield DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss');

            yield DateTimeField::new('updateTime', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss');
        }
    }

    /**
     * 复制环境变量
     */
    #[AdminAction('{entityId}/copy', 'copy_env')]
    public function copyEnv(AdminContext $context): Response
    {
        $env = $context->getEntity()->getInstance();
        $newEnv = new Env();
        $newEnv->setName($env->getName() . '_copy');
        $newEnv->setValue($env->getValue());
        $newEnv->setRemark($env->getRemark() ? $env->getRemark() . ' (复制)' : '');
        $newEnv->setSync(false);
        $newEnv->setValid(false);

        $this->entityManager->persist($newEnv);
        $this->entityManager->flush();

        $this->addFlash('success', '环境变量复制成功');

        return $this->redirect($this->urlGenerator->generate('admin', [
            'crudAction' => 'edit',
            'crudControllerFqcn' => self::class,
            'entityId' => $newEnv->getId(),
        ]));
    }
}
