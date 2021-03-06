<?php

Namespace Apiculture\Controller;

use Apiculture\Entity\Hive;
use Apiculture\Entity\Intervention;
use Apiculture\Form\UpdateForm;
use Apiculture\Form\ApicultureAddForm;
use Apiculture\Form\ApicultureInterventionForm;
use Apiculture\Form\ApicultureProductionForm;
use Apiculture\Entity\Production;
use Ivory\GoogleMap\Helper\MapHelper;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Overlays\Marker;
use Zend\Form\Element\Date;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\I18n\Validator\Int;
use Zend\I18n\Validator\Float;

class ApicultureController extends AbstractActionController
{
    public function dashboardAction()
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
                    $marker->setOption('class','marker');
                    $infoWindow = new InfoWindow();
                    $javascript_content = "$('div.hidden').removeClass('hidden')";
                    //$javascript_content = "alert('test')";
                    $infoWindow->setContent("
                        <div id='content_infoWindow'>
                        <div id=javascript_content style='display : none'>$javascript_content</div>
                        <h4 id=".$results[$i]->getId()." class='hive_name text-center'>".$results[$i]->getName()."</h4>
                        <p class='hive_longitude'>Longitude : ".$results[$i]->getLongitude()."</p>
                        <p class='hive_latitude'>Latitude : ".$results[$i]->getLatitude()."</p>
                        <button class='btn btn-info manage_hive' data-toggle='modal' data-target='#modalIntervention'>Interventions</button>
                        </div>
                        ");
                    $infoWindow->setAutoClose(true);
                    $marker->setPosition($results[$i]->getLatitude(),$results[$i]->getLongitude(),true);
                    $marker->setInfoWindow($infoWindow);
                    $map->addMarker($marker);
                    }
            }

        $form = new ApicultureAddForm($em);
        $formIntervention = new ApicultureInterventionForm($em);
        $formProduction = new ApicultureProductionForm($em);

         return new ViewModel(array('map' => $map,
                                    'form' => $form,
                                    'formIntervention' => $formIntervention,
                                    'formProduction' => $formProduction,
                                    'mapHelper' => $mapHelper,
                                    'hives' => $results,
                                    'user' => $this->zfcUserAuthentication()->getIdentity()));
    }

    public function interventionAction()
    {
        $interventions = $this->getResponse();
        $response = array();
        $nb_results_per_page = 10;
        $page = (int)$this->getRequest()->getPost('page',null);
        if ($page == 0)
            $page = 1;
        $begin = ($page * $nb_results_per_page)-$nb_results_per_page;
        $id_hive = $this->getRequest()->getPost('id_hive', null);
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $query = $em->createQuery('
            SELECT i.date, i.description
            FROM Apiculture\Entity\Intervention i
            WHERE i.id_hive = :id
            ORDER BY i.date DESC, i.id DESC')
            ->setFirstResult($begin)
            ->setMaxResults($nb_results_per_page)
            ->setParameter('id', $id_hive)
        ;

        $results = $query->getResult();
        foreach ($results as $result) {
            $date = new \DateTime($result['date']);
            $response[] = array('date'=>$date->format('d/m/Y'),'description' => $result['description']);
        }

        $interventions->setContent(\Zend\Json\Json::encode($response));
        return $interventions;



    }

    public function productionAction()
    {
        $id_hive = $this->getRequest()->getPost('id_hive',null);
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $query = $em->createQuery('
            SELECT p.date, p.production
            FROM Apiculture\Entity\Production p
            WHERE p.id_hive = :id
            ORDER BY p.date')
            ->setParameter('id', $id_hive)
        ;

        $table = array();
        $table['cols'] = array(
            array('label' => 'Date', 'type' => 'string'),
            array('label' => 'Production', 'type' => 'number')
        );

        $rows = array();
        $total = 0;
        $results = $query->getResult();
        foreach ($results as $result) {
            $date = date_create($result['date']);
            $rows[] = array('c' => array( array('v'=>date_format($date, 'd/m/Y')), array('v'=>$result['production'])));
            $total += $result['production'];
        }

        $table['rows'] = $rows;
        $table['poids'] = $total;
        $productions = $this->getResponse();
        $productions->setContent(\Zend\Json\Json::encode($table));
        return $productions;
    }

    public function displaypaginationAction()
    {
        $nb_pages = $this->getResponse();
        $id_hive = $this->getRequest()->getPost('id_hive',null);
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $query = $em->createQuery('
            SELECT count(i.id)
            FROM Apiculture\Entity\Intervention i
            WHERE i.id_hive = :id')
            ->setParameter('id',$id_hive);

        $result = $query->getSingleresult();

        $nb_pages->setContent(\Zend\Json\Json::encode($result));
        return $nb_pages;
    }

    public function addhiveAction()
    {
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form = new ApicultureAddForm($em);
        $validator = new Int();
        $error = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $hive = new Hive();
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $datas = $form->getData();
                $hive->setName($datas['name']);
                if (!($validator->isValid($datas['latitude'])))
                  $error[] = 'Latitude invalide';
                if (!($validator->isValid($datas['longitude'])))
                  $error[] = 'Longitude invalide';
                if (!empty($error))
                  return new ViewModel(array('error' => $error,'form' => $form));
                $hive->setlatitude($datas['latitude']);
                $hive->setLongitude($datas['longitude']);
                $hive->setUser($this->zfcUserAuthentication()->getIdentity());
                $em->persist($hive);
                $em->flush();

                return $this->redirect()->toRoute('dashboard');
            }
        }
        return new ViewModel(array('form'=>$form));
    }

    public function addproductionAction()
    {
        $response = $this->getResponse();
        $validator = new Int();
        $errors = array();
        $production = new Production();
        $values = array();
        $datas = $this->getRequest()->getPost('datas',null);
        $fields = explode('&',$datas);
        foreach ($fields as $field) {
            $value = (explode('=',$field));
            $values[] = $value;
        }

        foreach ($values as $value) {
            if ($value[0] == 'id_hive' && !empty($value[1]))
                $production->setIdHive((int)$value[1]);
            if ($value[0] == 'production' && !empty($value[1]) && $validator->isValid($value[1]))
                $production->setProduction($value[1]);
            if ($value[0] == 'date' && !empty($value[1]))
                $production->setDate(urldecode($value[1]));
            if ($value[0] == 'production' && !$validator->isValid($value[1]))
                $errors['production'] = 'Production invalide';
            if ($value[0] == 'date' && empty($value[1]))
                $errors['date'] = 'Date invalide';

        }

        if (!empty($errors)) {
            $response->setContent(\Zend\Json\Json::encode($errors));
            return $response;
        }
        else {
            $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $em->persist($production);
            $em->flush();
            $response->setContent(\Zend\Json\Json::encode($production));
            return $response;
        }
    }

    public function addinterventionAction()
    {
        $response = $this->getResponse();
        $intervention = new Intervention();
        $values = array();
        $datas = $this->getRequest()->getPost('datas',null);
        $fields = explode('&',$datas);
        foreach ($fields as $field) {
            $value = (explode('=',$field));
            $values[] = $value;
        }
        foreach ($values as $value) {
            if ($value[0] == 'id_hive' && !empty($value[1]))
                $intervention->setIdHive((int)$value[1]);
            if ($value[0] == 'description' && !empty($value[1]))
                $intervention->setDescription(urldecode(strtr ($value[1], '+', ' ')));
        }
        $intervention->setDate(date('Y-m-j'));
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $em->persist($intervention);
        $em->flush();
        $response->setContent(\Zend\Json\Json::encode($intervention));
        return $response;
    }


    public function deletehiveAction()
    {
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $id = intval($this->params('id'));
        $hive = $em->getRepository('Apiculture\Entity\Hive')->find($id);
        $em->remove($hive);
        $em->flush();

        return $this->redirect()->toRoute('dashboard');

    }


    public function checkaddhiveAction()
    {
      $response = $this->getResponse();
      $errors = array();
      $latitude = $_POST['latitude'];
      $longitude = $_POST['longitude'];
      $validator = new Int();
      if (!$validator->isValid($latitude))
        $errors['latitude'] = 'Latitude invalide';
      if (!$validator->isValid($longitude))
        $errors['longitude'] = 'Longitude invalide';
      $response->setContent(\Zend\Json\Json::encode($errors));
      return $response;
    }
}
