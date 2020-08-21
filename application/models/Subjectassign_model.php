<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subjectassign_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function getsubject() {
        $this->db->select('id, name')->from('subjects');
        $query = $this->db->get();
		return $query->result_array();
    }
	public function getsubjectbyid($id) {
        $this->db->select('subject')->from('class_subject')->where('class', $id);
        $query = $this->db->get();
		$row = $query->result_array();
		$row1 =  explode(', ', $row[0]['subject']);
		return $row1;
    }
	public function getclass() {
        $this->db->select('id, class')->from('classes');
        $query = $this->db->get();
		return $query->result_array();
    }
	public function getgrademark($id) {
        $this->db->select('*')->from('class_subject')->where('class_subject.class', $id);
        $query = $this->db->get();
		$result = $query->result_array();
		
		$subject = [];
		$grade = explode(', ', $result[0]['mark_grade']);
		 $subject = explode(', ', $result[0]['subject']);
		// $sub = explode(', ', $result[0]['subject']);
		// for($i = 0; $i<count($sub); $i++){
			// $query1 = $this->db->select('*')->from('subjects')->where('id', $sub[$i])->get();
			// $row1 = $query1->result_array();
			// array_push($subject, $row1[0]['name']);
		// }
		$arr = array_combine($subject, $grade);
		return $arr;
    }
	public function getclasssubject($id) {
		$this->db->select('*')->from('class_subject');
		if($id != ''){
			$this->db->where('class', $id);
		}
        $query = $this->db->get();
		$array = [];
		foreach($query->result_array() as $row){
			$subarray = [];
			//class name
			$this->db->select('class, id')->from('classes')->where('id', $row['class']);
			$query = $this->db->get();
			$result = $query->result_array();
			$subarray['class'] = $result[0]['class'];
			$subarray['id'] = $result[0]['id'];
			//subject List
			$sub = explode(', ', $row['subject']);
			$subject = [];
			for($i = 0; $i<count($sub); $i++){
				if($sub[$i] != ''){
					$query1 = $this->db->select('name, id')->from('subjects')->where('id', $sub[$i])->get();
					$result1 = $query1->result_array();
					array_push($subject, array('name'=>$result1[0]['name'], 'id'=>$result1[0]['id']));
				 }
			}
			$subarray['subject'] = $subject;
			//grade list
			$gr = explode(', ', $row['mark_grade']);
			$gr = array_filter($gr);
			$subarray['mark_grade'] = $gr;
			array_push($array, $subarray);
		}
		return $array;
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->where('class', $id);
        $this->db->delete('class_subject');
    }

	public function getInformation() {
        $query = $this->db->select()->from('sch_settings')->get();
		$result = $query->result_array();
		return $result;
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data) {
		$query = $this->db->select()->from('class_subject')->where('class', $data['class'])->get();
		if($query->num_rows() > 0){
			 $this->db->where('class', $data['class']);
            $this->db->update('class_subject', $data);
		}else {
            $this->db->insert('class_subject', $data);
            return $this->db->insert_id();
		}
    }

}
