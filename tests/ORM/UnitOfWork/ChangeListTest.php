<?php

namespace ORM\UnitOfWork;

use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Configurations\AnnotationConfiguration;
use Electronics\Database\ORM\UnitOfWork\ChangeList;
use Electronics\Database\ORM\UnitOfWork\State;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;
use PHPUnit\Framework\TestCase;

class ChangeListTest extends TestCase
{
    function testChanges(): void
    {
        $conf = new AnnotationConfiguration();
        $uow = new UnitOfWork();
        $changeList = new ChangeList($conf, $uow);

        $this->assertEquals(0, count($changeList->determineChanges()));

        $entity = new ChangeListTestEntity();
        $uow->register($entity);
        $this->assertEquals(1, count($changeList->determineChanges()));
        $this->assertEquals(State::ADDED, $changeList->determineChanges()[0]->getState());

        $entity->id = 1;
        $this->assertEquals(State::PERSISTED, $changeList->determineChanges()[0]->getState());

        $entity->username = 'test';
        $this->assertEquals(State::MODIFIED, $changeList->determineChanges()[0]->getState());

        $uow->delete($entity);
        $this->assertEquals(State::DELETED, $changeList->determineChanges()[0]->getState());
    }
}

#[Entity('users')]
class ChangeListTestEntity
{
    #[Id]
    public $id;
    #[Column('user_name')]
    public $username;
}