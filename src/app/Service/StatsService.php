<?php

namespace App\Service;

class StatsService {

    private $db;

    function __construct($db){
        $this->db = $db;
    }


    function getESOStats($params){

        $result = [];
        
        $params['group_name'] = 'A';
        $result[] = $this->getESOGroupStats($params);

        $params['group_name'] = 'B';
        $result[] = $this->getESOGroupStats($params);

        $params['group_name'] = 'C';
        $result[] = $this->getESOGroupStats($params);

        $params['group_name'] = 'D';
        $result[] = $this->getESOGroupStats($params);

        return $result;

    }


    function getESOGroupStats($params){
        

        if ($params['filter'] == 'tot-aprobat') {
            $sql = "select count(distinct `SubjectESO_EnrollmentESO`.`idEnrollment`) AS `total` from `SubjectESO_EnrollmentESO` where ((not(`SubjectESO_EnrollmentESO`.`idEnrollment` in (select distinct `SubjectESO_EnrollmentESO`.`idEnrollment` from `SubjectESO_EnrollmentESO` where ((`SubjectESO_EnrollmentESO`.`value` < :value) and (`SubjectESO_EnrollmentESO`.`idESO` like :idESO) and (`SubjectESO_EnrollmentESO`.`group_name` like :group_name))))) and (`SubjectESO_EnrollmentESO`.`idESO` like :idESO) and (`SubjectESO_EnrollmentESO`.`group_name` like :group_name))";
            $value = 5;
        }

        if ($params['filter'] == '1o2-suspeses') {
            $sql = "select count(*) AS `total` from (select `project.local`.`SubjectESO_EnrollmentESO`.`idEnrollment` AS `idEnrollment`,count(`project.local`.`SubjectESO_EnrollmentESO`.`value`) AS `suspendidas` from `project.local`.`SubjectESO_EnrollmentESO` where ((`project.local`.`SubjectESO_EnrollmentESO`.`value` < :value) and (`project.local`.`SubjectESO_EnrollmentESO`.`idESO` like :idESO) and (`project.local`.`SubjectESO_EnrollmentESO`.`group_name` like :group_name)) group by `project.local`.`SubjectESO_EnrollmentESO`.`idEnrollment` having (`suspendidas` < 3)) `T1`";
            $value = 5;
        }
        if ($params['filter'] == '3o4-suspeses') {
            $sql = "select count(*) AS `total` from (select `project.local`.`SubjectESO_EnrollmentESO`.`idEnrollment` AS `idEnrollment`,count(`project.local`.`SubjectESO_EnrollmentESO`.`value`) AS `suspendidas` from `project.local`.`SubjectESO_EnrollmentESO` where ((`project.local`.`SubjectESO_EnrollmentESO`.`value` < :value) and (`project.local`.`SubjectESO_EnrollmentESO`.`idESO` like :idESO) and (`project.local`.`SubjectESO_EnrollmentESO`.`group_name` like :group_name)) group by `project.local`.`SubjectESO_EnrollmentESO`.`idEnrollment` having ((`suspendidas` > 2) and (`suspendidas` < 5))) `T1`";
            $value = 5;
        }
        if ($params['filter'] == '5omes-suspeses') {
            $sql = "select count(*) AS `total` from (select `project.local`.`SubjectESO_EnrollmentESO`.`idEnrollment` AS `idEnrollment`,count(`project.local`.`SubjectESO_EnrollmentESO`.`value`) AS `suspendidas` from `project.local`.`SubjectESO_EnrollmentESO` where ((`project.local`.`SubjectESO_EnrollmentESO`.`value` < :value) and (`project.local`.`SubjectESO_EnrollmentESO`.`idESO` like :idESO) and (`project.local`.`SubjectESO_EnrollmentESO`.`group_name` like :group_name)) group by `project.local`.`SubjectESO_EnrollmentESO`.`idEnrollment` having (`suspendidas` >= 5)) `T1`";
            $value = 5;
        }
        
        $idESO = $params['idESO'];
        $group_name = $params['group_name'];


        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam("value", $value);
            $stmt->bindParam("idESO", $idESO);
            $stmt->bindParam("group_name", $group_name);

            $stmt->execute();
            $result = $stmt->fetchObject();

            return $result->total;

        }catch(PDOException $e){
            $error = array("error" => array("text" => $e->getMessage()));
            return $error;
        }
    }
}

?>