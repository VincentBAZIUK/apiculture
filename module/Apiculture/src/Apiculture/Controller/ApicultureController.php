<?php

Namespace Apiculture\Controller;

use Apiculture\Entity\Hive;
use Apiculture\Entity\Intervention;
use Apiculture\Form\UpdateForm;
use Apiculture\Form\ApicultureAddForm;
use Apiculture\Form\ApicultureInterventionForm;
use Ivory\GoogleMap\Helper\MapHelper;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Overlays\Marker;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\I18n\Validator\Int;

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
                    //$javascript_content = "$('.content_javascript_hive').html(".'$results[$i]->getName()'.")";
                    //$javascript_content = "alert('test')";
                    $infoWindow->setContent("
                        <div id='content_infoWindow'>
                        <div id=javascript_content style='display : none'>$javascript_content</div>
                        <h4 id=".$results[$i]->getId()." class='hive_name text-center'>".$results[$i]->getName()."</h4>
                        <p class='hive_longitude'>Longitude : ".$results[$i]->getLongitude()."</p>
                        <p class='hive_latitude'>Latitude : ".$results[$i]->getLatitude()."</p>
                        <button class='btn btn-info manage_hive' data-toggle='modal' data-target='#modalIntervention'>Interventions</button>
                        </div>
                        <script>$('.manage_hive').click(function() {alert('test');});</script>
                        ");
                    $infoWindow->setAutoClose(true);
                    $marker->setPosition($results[$i]->getLatitude(),$results[$i]->getLongitude(),true);
                    $marker->setInfoWindow($infoWindow);
                    $map->addMarker($marker);
                    }
            }

        $form = new ApicultureAddForm($em);
        $formIntervention = new ApicultureInterventionForm($em);

         return new ViewModel(array('map' => $map,
                                    'form' => $form,
                                    'formIntervention' => $formIntervention,
                                    'mapHelper' => $mapHelper,
                                    'hives' => $results,
                                    'user' => $this->zfcUserAuthentication()->getIdentity()));
    }

    public function interventionAction()
    {
        $interventions = $this->getResponse();
        $response = array();
        $id_hive = $this->getRequest()->getPost('id_hive', null);
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $query = $em->createQuery(
            'SELECT i.date, i.description
            FROM Apiculture\Entity\Intervention i
            WHERE i.id_hive = :id'
        )->setParameter('id', $id_hive);
        $results = $query->getResult();
        foreach ($results as $result) {
            $response[] = $result;
        }
        $interventions->setContent(\Zend\Json\Json::encode($response));
        return $interventions;
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

    public function addinterventionAction()
    {
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $formIntervention = new ApicultureInterventionForm($em);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $intervention = new Intervention();
            $formIntervention->setData($request->getPost());
            if ($formIntervention->isValid()) {
                $datas = $formIntervention->getData();
                $intervention->setDescription($datas['description']);
                $intervention->setDate($datas['date']);
                $intervention->setIdHive($datas['id_hive']);
                $em->persist($intervention);
                $em->flush();

                return $this->redirect()->toRoute('dashboard');
            }
        }

        return new ViewModel(array('formIntervention' => $formIntervention));

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
