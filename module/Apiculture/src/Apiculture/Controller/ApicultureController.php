<?php

Namespace Apiculture\Controller;

use Apiculture\Entity\Hive;
use Apiculture\Form\UpdateForm;
use Apiculture\Form\ApicultureForm;
use Ivory\GoogleMap\Helper\MapHelper;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlays\Marker;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ApicultureController extends AbstractActionController
{
    public function indexAction()
    {   
        
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $query = $em->createQuery(
            'SELECT h
            FROM Apiculture\Entity\Hive h
            WHERE h.user = :user'
        )->setParameter('user', $this->zfcUserAuthentication()->getIdentity());
        $results = $query->getResult();
            $map = new Map();
            $map->setStylesheetOption('width', '600px');
            $map->setStylesheetOption('height', '400px');
            $map->setCenter(50, 3, true);
            $mapHelper = new MapHelper();
            
            if (!empty($results)) {
                for ($i=0;$i<sizeof($results);$i++) {
                    $marker = new Marker();
                    $marker->setPosition($results[$i]->getLatitude(),$results[$i]->getLongitude(),true);
                    $map->addMarker($marker);
                    }
            }
            
         return new ViewModel(array('map' => $map,
                                    'mapHelper' => $mapHelper,
                                    'hives' => $results,
                                    'user' => $this->zfcUserAuthentication()->getIdentity()));
    }
    
    public function addhiveAction()
    {
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form = new ApicultureForm($em);
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $hive = new Hive();
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $datas = $form->getData();
                $hive->setName($datas['name']);
                $hive->setlatitude($datas['latitude']);
                $hive->setLongitude($datas['longitude']);
                $hive->setUser($this->zfcUserAuthentication()->getIdentity());
                $em->persist($hive);
                $em->flush();
                
                return $this->redirect()->toRoute('connected');
            }
        }
        return new ViewModel(array('form'=>$form));
    }
    
    public function deletehiveAction()
    {
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $id = intval($this->params('id'));
        $hive = $em->getRepository('Apiculture\Entity\Hive')->find($id);
        $em->remove($hive);
        $em->flush();
        
        return $this->redirect()->toRoute('connected');
        
    }
    
    public function updatehiveAction()
    {
        /*$em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form = new UpdateForm($em);
        $form->get('submit')->setValue('Update');
        $request = $this->getRequest();
        $hive = $em->getRepository('Apiculture\Entity\Hive')->find(22);
        $form->bind($hive);

        if ($request->isPost()) {
            $form->setDate($request->getPost());

            if ($form->isValid()) {
                $em->persist($hive);
                $em->flush();

                return $this->redirect()->toRoute('connected');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'hive' => $hive
            )
        );*/
    }
}