<?php

/*
 * Copyright (C) 2015 kevin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace KG\BeekeepingManagementBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class RucheMenu extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $colonie = $options["colonie"];

        $menu->addChild('.icon-chevron-left Retour au rucher', array(
            'route' => 'kg_beekeeping_management_view_rucher',
            'routeParameters' => array('rucher_id' => $colonie->getRuche()->getRucher()->getId())
        ));   
        
        // Ruche
        $titleRuche = '.icon-archive Ruche';
        $menu->addChild($titleRuche, array(
            'route' => 'kg_beekeeping_management_home'
        )); 

        $menu[$titleRuche]->addChild('.icon-eye Afficher', array(
            'route' => 'kg_beekeeping_management_view_ruche',
            'routeParameters' => array('ruche_id' => $colonie->getRuche()->getId())
        ));    
        
        if( !$colonie->getMorte() ){
            $menu[$titleRuche]->addChild('.icon-pencil Modifier', array(
                'route' => 'kg_beekeeping_management_update_ruche',
                'routeParameters' => array('ruche_id' => $colonie->getRuche()->getId())
            )); 
            
            if( $colonie->getRuche()->getCorps()->getNbCouvain() > 1 ){    
                $menu[$titleRuche]->addChild('.icon-scissors Diviser', array(
                    'route' => 'kg_beekeeping_management_diviser_colonie',
                    'routeParameters' => array('colonie_id' => $colonie->getId())
                )); 
            }       
        }    
        
        if( !$colonie->getRuche()->getHausses()->isEmpty()){
            $menu[$titleRuche]->addChild('.icon-upload Récolter', array(
                'route' => 'kg_beekeeping_management_add_recolte',
                'routeParameters' => array('colonie_id' => $colonie->getId())
            ));                  
        }

        if( !$colonie->getMorte() ){
            $emplacementVide = false;
            foreach ($colonie->getRuche()->getRucher()->getExploitation()->getRuchers() as $rucher) {
                foreach ($rucher->getEmplacements() as $emplacement) {
                    if( !$emplacement->getRuche() ){
                        $emplacementVide = true;
                        break;
                    }
                }
            }
            
            if( $emplacementVide ){
                $menu[$titleRuche]->addChild('.icon-exchange Transhumer', array(
                    'route' => 'kg_beekeeping_management_add_transhumance',
                    'routeParameters' => array('colonie_id' => $colonie->getId())
                ));  
            }
            
            $filleExist = false;
            foreach( $colonie->getRemerages() as $remerage ){
                if( !$remerage->getReine()->getReinesFilles()->isEmpty() ){
                    $filleExist = true;
                    break;
                }
            }            
            
            if( !$filleExist ){    
                $menu[$titleRuche]->addChild('.icon-trash Supprimer', array(
                    'route' => 'kg_beekeeping_management_delete_colonie',
                    'routeParameters' => array('colonie_id' => $colonie->getId())
                )); 
            }               
        }        
        
        // Colonie
        $titleColonie = '.icon-users Colonie';
                
        $menu->addChild($titleColonie, array(
            'route' => 'kg_beekeeping_management_home'
        )); 

        $menu[$titleColonie]->addChild('.icon-eye Afficher', array(
        'route' => 'kg_beekeeping_management_view_colonie',
        'routeParameters' => array('colonie_id' => $colonie->getId())
        )); 
        
        if( !$colonie->getMorte() ){
            $menu[$titleColonie]->addChild('.icon-pencil Modifier', array(
                'route' => 'kg_beekeeping_management_update_colonie',
                'routeParameters' => array('colonie_id' => $colonie->getId())
            )); 

            $menu[$titleColonie]->addChild('.icon-refresh Remérer', array(
                'route' => 'kg_beekeeping_management_add_remerage',
                'routeParameters' => array('colonie_id' => $colonie->getId())
            ));  
                        
            $menu[$titleColonie]->addChild('.icon-heartbeat Déclarer morte', array(
                'route' => 'kg_beekeeping_management_tuer_colonie',
                'routeParameters' => array('colonie_id' => $colonie->getId())
            ));         
        }
        
        //Visite
        $titleVisite = '.icon-file Visite';

        $menu->addChild($titleVisite, array(
            'route' => 'kg_beekeeping_management_home'
        )); 
        
        if( !$colonie->getMorte() ){
            if( !$colonie->getVisites()->isEmpty() ){
                $menu[$titleVisite]->addChild('.icon-pencil Modifier la dernière visite', array(
                    'route' => 'kg_beekeeping_management_update_visite',
                    'routeParameters' => array('visite_id' => $colonie->getVisites()->last()->getId())
                ));
                
                if( date_format($colonie->getVisites()->last()->getDate(),"Y-m-d") < date_format(new \DateTime(),"Y-m-d") ){
                    $menu[$titleVisite]->addChild('.icon-plus Créer une visite', array(
                        'route' => 'kg_beekeeping_management_add_visite',
                        'routeParameters' => array('colonie_id' => $colonie->getId())
                    ));                     
                }                     
                
            }else{
                $menu[$titleVisite]->addChild('.icon-plus Créer une visite', array(
                    'route' => 'kg_beekeeping_management_add_visite',
                    'routeParameters' => array('colonie_id' => $colonie->getId())
                ));                 
            }        
        }
        
        //Tache
        $titleTache = '.icon-thumb-tack Tâche';

        $menu->addChild($titleTache, array(
            'route' => 'kg_beekeeping_management_home'
        )); 
        
        $menu[$titleTache]->addChild('.icon-plus Créer une tâche', array(
            'route' => 'kg_beekeeping_management_add_tache',
            'routeParameters' => array('colonie_id' => $colonie->getId())
        )); 
                
        // Historique
        $titleHisto = '.icon-calendar Historique';
        $menu->addChild($titleHisto, array(
            'route' => 'kg_beekeeping_management_home'
        )); 

        if( !$colonie->getVisites()->isEmpty()){
            $menu[$titleHisto]->addChild('.icon-file Visites', array(
                'route' => 'kg_beekeeping_management_view_visites',
                'routeParameters' => array('colonie_id' => $colonie->getId())
            ));            
        }        
        
        $menu[$titleHisto]->addChild('.icon-refresh Remérages', array(
            'route' => 'kg_beekeeping_management_view_remerages',
            'routeParameters' => array('colonie_id' => $colonie->getId())
        ));          

        if( !$colonie->getTranshumances()->isEmpty()){
            $menu[$titleHisto]->addChild('.icon-exchange Transhumances', array(
                'route' => 'kg_beekeeping_management_view_transhumances',
                'routeParameters' => array('colonie_id' => $colonie->getId())
            )); 
        }    
        
        if( !$colonie->getRecoltes()->isEmpty()){
            $menu[$titleHisto]->addChild('.icon-upload Récoltes', array(
                'route' => 'kg_beekeeping_management_view_recoltes',
                'routeParameters' => array('colonie_id' => $colonie->getId())
            )); 
        }        

        return $menu;
    }
}