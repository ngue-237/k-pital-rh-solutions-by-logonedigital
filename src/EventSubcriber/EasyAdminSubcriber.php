<?php

namespace App\EventSubcriber;

use App\Entity\Contact;
use App\Entity\Job;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Uid\Ulid;

class EasyAdminSubcriber implements EventSubscriberInterface
{
    private $appKernel;
    private $cache;

    public function __construct(CacheInterface $cache, KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
        $this->cache = $cache;
    }

    public static function getSubscribedEvents()
    {
        return[
            BeforeEntityPersistedEvent::class=>['persistanceProcess'],
            BeforeEntityUpdatedEvent::class=>['updatedProcess'],
            // AfterEntityPersistedEvent::class => ['clearCacheAfter'],
            // AfterEntityDeletedEvent::class => ['clearCacheAfterDeleted'],
            // AfterEntityUpdatedEvent::class => ['clearCacheAfterUpdated'], 
        ];
    }


/*     public function clearCacheAfter(AfterEntityPersistedEvent $event):void{
         $entity = $event->getEntityInstance();
     }
    

     public function clearCacheAfterUpdated(AfterEntityUpdatedEvent $event):void{
         $entity = $event->getEntityInstance();

        
     }

     public function clearCacheAfterDeleted(AfterEntityDeletedEvent $event):void{
        $entity = $event->getEntityInstance();
        
     }*/

    /**
     * permet de faire des actions sur l'utisateur lorsqu'il est ajouter depuis le dashboard
     *
     * @param BeforeEntityPersistedEvent $event
     * @return void
     */
    public function persistanceProcess(BeforeEntityPersistedEvent $event){
        $entity = $event->getEntityInstance();

        if($entity instanceof User){
            $entity->setPassword(md5(uniqid()));
            $entity->setCreatedAt(new \DateTimeImmutable('now'));
        }

        if($entity instanceof Job){
            $entity->setCreatedAt(new \DateTimeImmutable('now'));
            $entity->setRef(new Ulid());
        }
    }

    /**
     * permet de faire des actions après la modification d'un utilisateur
     *
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function updatedProcess(BeforeEntityUpdatedEvent $event){
        $entity = $event->getEntityInstance();

        if($entity instanceof User){
            $entity->setUpdatedAt(new \DateTimeImmutable('now'));
        }

        if($entity instanceof Job){
            $entity->setUpdatedAt(new \DateTimeImmutable('now'));
        }

        if($entity instanceof Contact){
            $entity->setUpdatedAt(new \DateTimeImmutable('now'));
        }

    }
}