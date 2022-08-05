<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Boolean;
use Vich\UploaderBundle\Form\Type\VichImageType;
use AppBundle\Entity\Reward;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use App\Controller\Admin\ProxyQueryInterface;



class DocumentCrudController extends AbstractCrudController
{


    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $aut = new Document();
        $aut->setAuthorId($this->getUser()->getId());

        return $aut;
    }

    public function configureFields(string $pageName): iterable
    {

        //dd();
        return [
            AssociationField::new('theme')->setFormTypeOptions([
                'by_reference' => true,
            ]),
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('title'),
            TextField::new('description')->hideOnIndex(),
            IntegerField::new('authorId')->hideOnForm()->hideOnIndex(),
            BooleanField::new('published'),
           // IntegerField::new('documentType')->hideOnIndex(),
            ImageField::new('image')

                ->onlyOnDetail()
                ->setBasePath('uploads/images/products')
                ->setUploadDir('public/uploads/images/products')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            DateTimeField::new('createdAt')->onlyOnDetail(),
         TextareaField::new('imageFile')->setFormType(VichImageType::class)->hideOnIndex()->hideOnDetail(),
            //TextField::new('image')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
           ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
               return $action->setIcon('fa fa-eye')->setLabel(false);
           })
        ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
        return $action->setIcon('fa fa-pencil-square-o')->setLabel(false);
    })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash-o ')->setLabel(false);
            });


       // ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }

    public function fetchUserRewardsAction(Document $document)
    {

    }



}
