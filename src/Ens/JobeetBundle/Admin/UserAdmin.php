<?php

namespace Ens\JobeetBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Ens\JobeetBundle\Entity\User;

class UserAdmin extends Admin
{
    private $userManager;

    public function getUserManager()
    {
        return $this->userManager;
    }
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username');
        if (!$this->getRequest()->get($this->getIdParameter())) {
            $formMapper
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                ));
        }
        if ($this->getRequest()->get($this->getIdParameter())) {
            $formMapper
                ->add('plainPassword', 'password', array(
                    'required' => false,
                    'label' => 'Enter new password',
                ));
        }
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) /*TODO filters*/
    {
        $datagridMapper
            ->add('email')
            ->add('gender')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper) /*TODO listing */
    {
        $listMapper
            ->addIdentifier('id')
            ->add('email')
            ->add('firstname')
        ;
    }
    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }
    public function prePersist($user)
    {
        $user->setEmail($user->getUsername());
        $user->setPlainPassword($user->getPassword());
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }
}