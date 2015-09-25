<?php

namespace KG\BeekeepingManagementBundle\Form\EventListener;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\ORM\EntityRepository;


class DiviserMielFieldSubscriber implements EventSubscriberInterface
{
    private $nbmax;
    
    /**
     * Constructor
     */
    public function __construct($nbmax)
    {
        $this->nbmax = $nbmax;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA  => 'preSetData',
            FormEvents::PRE_SUBMIT    => 'preSubmit'
        );
    }
 
    private function addNbMielForm($form)
    {       
        $form->add('nbmiel');
    }
 
    public function preSetData(FormEvent $event)
    {;
        $form = $event->getForm();
        $this->addNbMielForm($form);
    }
 
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
 
        $nbmiel = array_key_exists('nbmiel', $data) ? $data['nbmiel'] : null;
        
        if($nbmiel >= $this->nbmax){
            $form->addError(new FormError('Le nombre de cadres de miel importé doit être inférieur à celui de la colonie mère'));
        }
        
        $this->addNbmielForm($form);
    }
}