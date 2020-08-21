<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subjectassign extends Admin_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
		$this->load->model('subjectassign_model');
        $this->session->set_userdata('top_menu', 'Examinations');
        $this->session->set_userdata('sub_menu', 'Subjectassign/index');
        $data['title'] = 'Add Grade';
        $data['title_list'] = 'Grade Details';
        $listsubject = $this->subjectassign_model->getsubject();
		$listclasses = $this->subjectassign_model->getclass();
        $data['listsubject'] = $listsubject;
		$data['listclasses'] = $listclasses;
		$classsubject = $this->subjectassign_model->getclasssubject('');
		$data['classsubject'] = $classsubject;
		$this->load->view('layout/header');
        $this->load->view('admin/subjectassign/createsubjectassign', $data);
        $this->load->view('layout/footer');
    }

    function create() {
        $data['title'] = 'Add Arade';
        $data['title_list'] = 'Grade Details';
        $this->form_validation->set_rules('class', 'Class Name', 'required');
        if ($this->form_validation->run() == FALSE) {
            $listgrade = $this->grade_model->get();
            $data['listgrade'] = $listgrade;
            $this->load->view('layout/header');
            $this->load->view('admin/subjectassign/', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'class' => $this->input->post('class'),
                'subject' => implode(", ",$this->input->post('subject')),
                'mark_grade' => implode(", ",$this->input->post('markgrade'))
            );
			$this->load->model('subjectassign_model');
            $this->subjectassign_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Grade added successfully</div>');
            redirect('admin/subjectassign/index');
        }
    }

    function edit($id) {
        $data['title'] = 'Edit Grade';
        $data['title_list'] = 'Grade Details';
		$this->load->model('subjectassign_model');
		$listsubject = $this->subjectassign_model->getsubject();
		$listclasses = $this->subjectassign_model->getclass();
        $data['listsubject'] = $listsubject;
		$data['listclasses'] = $listclasses;
        $classsubject = $this->subjectassign_model->getclasssubject($id);
        $data['classsubject'] = $classsubject;
        $this->form_validation->set_rules('class', 'Class', 'required');
        if ($this->form_validation->run() == FALSE) {
			
            $this->load->view('layout/header');
            $this->load->view('admin/subjectassign/editsubjectassign', $data);
            $this->load->view('layout/footer');
        } else {
             $data = array(
				'class' => $id,
				'subject' => $classsubject['subject'],
				'mark_grade' => $classsubject['mark_grade']
			);
			$this->subjectassign_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Grade updated successfully</div>');
           redirect('admin/subjectassign/index');
        }
    }

    function delete($id) {
		$this->load->model('subjectassign_model');
        $data['title'] = 'Fees Master List';
        $this->subjectassign_model->remove($id);
        redirect('admin/subjectassign/index');
    }
	function pdf($id) {
		$this->load->library('Pdf');
		$this->load->model('subjectassign_model');
		$school_info = $this->subjectassign_model->getInformation();
		
		$logo = 'http://school.shivankurvidyalaya.co.in/uploads/school_content/logo/'.$school_info[0]['app_logo'];
		$name = $school_info[0]['name'];
		$address = $school_info[0]['address'];
		 
		$student = $this->student_model->get($id);
        $gradeList = $this->grade_model->get();        
        $studentSession = $this->student_model->getStudentSession($id);
        $timeline = $this->timeline_model->getStudentTimeline($id, $status = '');
        $data["timeline_list"] = $timeline;

        $student_session_id = $studentSession["student_session_id"];

        $student_session = $studentSession["session"];
        $current_student_session = $this->student_model->get_studentsession($student['student_session_id']);  
        $data["session"]  = $current_student_session["session"]; 
        $student_due_fee = $this->studentfeemaster_model->getStudentFees($student['student_session_id']);
		//print_r(json_decode(json_encode($student_due_fee), True));
		$standered = json_decode(json_encode($student_due_fee), True)[0]['name'];
        $student_discount_fee = $this->feediscount_model->getStudentFeesDiscount($student['student_session_id']);
        $data['student_discount_fee'] = $student_discount_fee;
        $data['student_due_fee'] = $student_due_fee;
        $siblings = $this->student_model->getMySiblings($student['parent_id'], $student['id']);
		$data['siblings_classname'] = $this->student_model->getMySiblingsClass($student['parent_id'], $student['id']);
		$examList = $this->examschedule_model->getExamByClassandSection($student['class_id'], $student['section_id']);
        $data['examSchedule'] = array();
        if (!empty($examList)) {
            $new_array = array();
            foreach ($examList as $ex_key => $ex_value) {
                $array = array();
                $x = array();
                $exam_id = $ex_value['exam_id'];
                $student['id'];
                $exam_subjects = $this->examschedule_model->getresultByStudentandExam($exam_id, $student['id']);
                foreach ($exam_subjects as $key => $value) {
                    $exam_array = array();
                    $exam_array['exam_schedule_id'] = $value['exam_schedule_id'];
                    $exam_array['exam_id'] = $value['exam_id'];
                    $exam_array['full_marks'] = $value['full_marks'];
                    $exam_array['passing_marks'] = $value['passing_marks'];
                    $exam_array['exam_name'] = $value['name'];
                    $exam_array['exam_type'] = $value['type'];
                    $exam_array['attendence'] = $value['attendence'];
                    $exam_array['get_marks'] = $value['get_marks'];
                    $x[] = $exam_array;
                }
                $array['exam_name'] = $ex_value['name'];
				$exam_name = $ex_value['name'];
				$exam = $ex_value['name'];
                $array['exam_result'] = $x;
                $new_array[] = $array;
            }
            $data['examSchedule'] = $new_array;
			$student_doc = $this->student_model->getstudentdoc($id);
       }
	  // print_r($data);
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle($name);
		$pdf->setPrintHeader(true);
		$pdf->SetHeaderData('http://eziitours.in/img/logo.png', PDF_HEADER_LOGO_WIDTH, $name, '', array(0,0,0), array(255,255,255));
		$pdf->setFooterMargin(20);
		$pdf->setHeaderMargin(20);
		
		$pdf->SetAutoPageBreak(true);
		$pdf->SetFont('dejavusans', '', 10);
		$pdf->AddPage('L', 'A4');
		if($student['class'] != '9th' && $student['class']!= '10th'){
			$table = '<table border = "" width = "100%" align = "center" cellpadding = "10">
	<tr>
		<td>
			<table>
				<tr>
					<td width = "20%"><img src = "'.$logo.'" width = "80px" /></td>
					<td align = "left"><br/><h1>'.$name.'</h1></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width = "100%" cellpadding = "3px">
				<tr >
					<td align = "left" style = "border-bottom:1px dashed #ddd">Student Name :- '.$student['firstname'].' '.$student['lastname'].'</td>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Standard :- '.$student['class'].'</td>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Division :- '.$student['section'].'</td>
				</tr>
				<tr>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Role Number :- '.$student['roll_no'].'</td>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Date Of Birth :- '.$student['dob'].'</td>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Fathers Name :- '.$student['father_name'].'</td>
				</tr>
				<tr>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Mothers Name :- '.$student['mother_name'].'</td>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Address :- '.$student['guardian_address'].'</td>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Mobile Number :- '.$student['father_phone'].'</td>
				</tr>
				<tr>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Adhar Number :- '.$student['adhar_no'].'</td>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Admission Number :- '.$student['admission_no'].'</td>
					<td align = "left" style = "border-bottom:1px dashed #ddd">Id :- '.$student['id'].'</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width = "100%" border = "0px" cellpadding = "3px">
				<tr>
					<td width = "50%">
						<h4 style = "border:1px solid #000; background-color:#efefef" >First Term</h4>';
						
						if($exam_name == 'First Semester'){
						
				$table.='<table border = "1px" width = "100%" cellpadding = "2px">
							<tr style = "background-color:#efefef">
								<td width = "15%">Sr.No.</td>
								<td width = "35%">Subject</td>
								<td width = "20%">Marks/Grade</td>
								<td width = "30%">Text </td>
							</tr>';
							$obtain_marks = 0;
		$total_marks = 0;
		$result = "Pass";
		$exam_results_array = $array['exam_result'];
		$s = 0;
        foreach ($exam_results_array as $result_k => $result_v) {
			$i++;
			$globalI = $i;
			$table .= '<tr><td>'.$i.'</td><td>'.$result_v['exam_name'] . ' (' . substr($result_v['exam_type'], 0, 2) . '.) </td><td>';
			if ($result_v['attendence'] == "pre") {
				$get_marks_student = $result_v['get_marks'];
				if($get_marks_student >= 91 && $get_marks_student <= 100){
					$table .= 'A1';
				}else if($get_marks_student >= 81 && $get_marks_student <= 90){
					$table .= 'A2';
				}else if($get_marks_student >= 71 && $get_marks_student <= 80){
					$table .= 'B1';
				}else if($get_marks_student >= 61 && $get_marks_student <=70){
					$table .= 'B2';
				}else if($get_marks_student >= 51 && $get_marks_student <= 60){
					$table .= 'C1';
				}else if($get_marks_student >= 33 && $get_marks_student <= 45){
					$table .= 'D';
				}else if($get_marks_student >= 21 && $get_marks_student <= 32){
					$table .= 'E1';
				}else{
					$table .= 'E2';
				}
				$passing_marks_student = $result_v['passing_marks'];
				if ($result == "Pass") {
					if ($get_marks_student < $passing_marks_student) {
						$result = "Fail";
					}
				}
				if (is_numeric($result_v['get_marks'])) {
					$obtain_marks = (int) $obtain_marks + (int) $result_v['get_marks'];
					$total_marks = $total_marks + $result_v['full_marks'];
				}else{
					$gradeTest .= '1';
				}
			}else {
				$result = "Fail";
				$table .= $result_v['attendence'];
			}			
			$table .='</td><td class="text text-center"><table width = "100%"><tr><td style = "border-bottom:1px solid #000">';
			if($result_v['exam_name'] == 'Mathematics'){
				$table.= 'Favourites / Hobby';
			}else if($result_v['exam_name'] == 'Arts'){
				$table .= 'Mandetory Development';
			}else if($result_v['exam_name'] == 'Marathi'){
				$table .= 'Special Development';
			}
			if ($result_v['attendence'] == "pre") {
				$passing_marks_student = $result_v['passing_marks'];

				if ($get_marks_student < $passing_marks_student) {
					//$table .="<span class='label pull-right bg-red'>" . $this->lang->line('fail') . "</span>";
				} else {
					//$table .= "<span class='label pull-right bg-green'>Pass</span>";
				}
			} else {
				//$table .= "<span class='label pull-right bg-red'>" . $this->lang->line('fail') . "</span>";
				$s++;
			}
			$table .='</td></tr><tr><td></td></tr></table></td></tr>';
			if ($s == count($exam_results_array)) {
				$obtain_marks = 0;
			}
		}
		$table .='<tr><td>';
		if ($result == "Pass") {
			$table .= "<b class='text text-success'>".$this->lang->line('result') . ":".$result."</b>";
		} else {
			$table .= "<b class='text text-danger'>".$this->lang->line('result') . ": " . $result."</b>";
		}
		$table .= '</td><td>';
					
		if($globalI == strlen($gradeTest)){
			$table .= $this->lang->line('grand_total') . ": " ."N/A";
		}else{
			$table .= $this->lang->line('grand_total') . ": " . $obtain_marks . "/" . $total_marks;
		}
		$table .= '</td><td>';
		if($globalI == strlen($gradeTest)){
			$table .= "N/A";
		}else{
			$foo = ($obtain_marks * 100) / $total_marks;
			$table .= $this->lang->line('percentage') . ": " . number_format((float) $foo, 2, '.', '')."%";
		}
		$table .= '</td><td>';
		if (!empty($gradeList)) {
			foreach ($gradeList as $key => $value) {
				if ($foo >= $value['mark_from'] && $foo <= $value['mark_upto']) {
					$table .= $this->lang->line('grade') . " : " . $value['name'];
					break;
				}
			}
		}
		$table .= '</td></tr></table>';
	}	
						$table.='<br/><br/>
						
					</td>
					<td width = "50%">
						<h4 style = "border:1px solid #000; background-color:#efefef" >Second Term</h4>';
						if($exam_name == 'Second Semester'){
						
				$table.='<table border = "1px" width = "100%" cellpadding = "2px">
							<tr style = "background-color:#efefef">
								<td width = "15%">Sr.No.</td>
								<td width = "35%">Subject</td>
								<td width = "20%">Marks/Grade</td>
								<td width = "30%">Text </td>
							</tr>';
							$obtain_marks = 0;
		$total_marks = 0;
		$result = "Pass";
		$exam_results_array = $array['exam_result'];
		$s = 0;
		$i = 0;
        foreach ($exam_results_array as $result_k => $result_v) {
			$i++;
			$globalI = $i;
			$table .= '<tr><td>'.$i.'</td><td>'.$result_v['exam_name'] . ' (' . substr($result_v['exam_type'], 0, 2) . '.) </td><td>';
			if ($result_v['attendence'] == "pre") {
				$get_marks_student = $result_v['get_marks'];
				if($get_marks_student > 91 && $get_marks_student < 100){
					$table .= 'A1';
				}else if($get_marks_student > 81 && $get_marks_student <= 90){
					$table .= 'A2';
				}else if($get_marks_student > 71 && $get_marks_student <= 80){
					$table .= 'B1';
				}else if($get_marks_student > 61 && $get_marks_student <=70){
					$table .= 'B2';
				}else if($get_marks_student > 51 && $get_marks_student <= 60){
					$table .= 'C1';
				}else if($get_marks_student > 33 && $get_marks_student <= 45){
					$table .= 'D';
				}else if($get_marks_student > 21 && $get_marks_student <= 32){
					$table .= 'E1';
				}else{
					$table .= 'E2';
				}
				$passing_marks_student = $result_v['passing_marks'];
				if ($result == "Pass") {
					if ($get_marks_student < $passing_marks_student) {
						$result = "Fail";
					}
				}
				if (is_numeric($result_v['get_marks'])) {
					$obtain_marks = (int) $obtain_marks + (int) $result_v['get_marks'];
					$total_marks = $total_marks + $result_v['full_marks'];
				}else{
					$gradeTest .= '1';
				}
			}else {
				$result = "Fail";
				$table .= $result_v['attendence'];
			}			
			$table .='</td><td class="text text-center"><table width = "100%"><tr><td style = "border-bottom:1px solid #000">';
			if($result_v['exam_name'] == 'Mathematics'){
				$table.= 'Favourites / Hobby';
			}else if($result_v['exam_name'] == 'Arts'){
				$table .= 'Mandetory Development';
			}else if($result_v['exam_name'] == 'Marathi'){
				$table .= 'Special Development';
			}
			if ($result_v['attendence'] == "pre") {
				$passing_marks_student = $result_v['passing_marks'];

				if ($get_marks_student < $passing_marks_student) {
					//$table .="<span class='label pull-right bg-red'>" . $this->lang->line('fail') . "</span>";
				} else {
					//$table .= "<span class='label pull-right bg-green'>Pass</span>";
				}
			} else {
				//$table .= "<span class='label pull-right bg-red'>" . $this->lang->line('fail') . "</span>";
				$s++;
			}
			$table .='</td></tr><tr><td></td></tr></table></td></tr>';
			if ($s == count($exam_results_array)) {
				$obtain_marks = 0;
			}
		}
		$table .='<tr><td>';
		if ($result == "Pass") {
			$table .= "<b class='text text-success'>".$this->lang->line('result') . ":".$result."</b>";
		} else {
			$table .= "<b class='text text-danger'>".$this->lang->line('result') . ": " . $result."</b>";
		}
		$table .= '</td><td>';
					
		if($globalI == strlen($gradeTest)){
			$table .= $this->lang->line('grand_total') . ": " ."N/A";
		}else{
			$table .= $this->lang->line('grand_total') . ": " . $obtain_marks . "/" . $total_marks;
		}
		$table .= '</td><td>';
		if($globalI == strlen($gradeTest)){
			$table .= "N/A";
		}else{
			$foo = ($obtain_marks * 100) / $total_marks;
			$table .= $this->lang->line('percentage') . ": " . number_format((float) $foo, 2, '.', '')."%";
		}
		$table .= '</td><td>';
		if (!empty($gradeList)) {
			foreach ($gradeList as $key => $value) {
				if ($foo >= $value['mark_from'] && $foo <= $value['mark_upto']) {
					$table .= $this->lang->line('grade') . " : " . $value['name'];
					break;
				}
			}
		}
		$table .= '</td></tr></table>';
	}else{
		$table .= '<table border = "1" width = "100%" cellpadding = "2px">
							<tr style = "background-color:#efefef">
								<td width = "15%">Sr.No.</td>
								<td width = "35%">Subject</td>
								<td width = "20%">Marks/Grade</td>
								<td width = "30%">text </td>
							</tr>';
								$obtain_marks = 0;
		$total_marks = 0;
		$result = "Pass";
		$exam_results_array = $array['exam_result'];
		$s = 0;
		$i = 0;
        foreach ($exam_results_array as $result_k => $result_v) {
			$i++;
			$globalI = $i;
			$table .= '<tr><td>'.$i.'</td><td>'.$result_v['exam_name'] . ' (' . substr($result_v['exam_type'], 0, 2) . '.) </td><td></td><td class="text text-center"><table width = "100%"><tr><td style = "border-bottom:1px solid #000">';
			if($result_v['exam_name'] == 'Mathematics'){
				$table.= 'Favourites / Hobby';
			}else if($result_v['exam_name'] == 'Arts'){
				$table .= 'Mandetory Development';
			}else if($result_v['exam_name'] == 'Marathi'){
				$table .= 'Special Development';
			}
			$table .='</td></tr><tr><td></td></tr></table></td></tr>';
			if ($s == count($exam_results_array)) {
				$obtain_marks = 0;
			}
		}
		$table .='</table>';
	}	
						$table.='<br/><br/>
					</td>
				</tr>
				<tr>
					<td width = "50%"><br/><br/><br/>
						<h4 style = "border:1px solid #000; background-color:#efefef" >Grade Table</h4>
						<table border = "1" width = "100%" cellpadding = "3">
							<tr style = "background-color:#efefef">>
								<td>Marks Distribution</td><td>91-100%</td><td>81-90%</td><td>71-80%</td><td>61-70%</td><td>51-60%</td><td>41-50%</td><td>33-40%</td><td>21-32%</td><td>20% or Below 20%</td>
							</tr>
							<tr><td>Grade</td><td>A1</td><td>A2</td><td>B1</td><td>B2</td><td>C1</td><td>C2</td><td>D</td><td>E1</td><td>E2</td>
							</tr>
						</table>
					</td>
					<td width = "50%">
						<br/><br/><br/><p>School _________________ will open on date:__/__/202__.</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width = "100%">
				<tr>
					<td><br/><br/><br/><br/>Class Teacher Signature</td>
					<td><br/><br/><br/><br/>Head Master Signature</td>
					<td><br/><br/><br/><br/>Parents Signature</td>
					<td><br/><br/><br/><br/>Class Teacher Signature</td>
					<td><br/><br/><br/><br/>Head Master Signature</td>
					<td><br/><br/><br/><br/>Parents Signature</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>'.$address.'</td>
	</tr>
</table>';
		}else{
			$table = '<table border = "" width = "100%" align = "center" cellpadding = "10">
	<tr>
		<td>
			<table width = "100%">
				<tr>
					<td width = "20%"><img src = "'.$logo.'" width = "80px" /></td>
					<td align = "left" width = "65%"><br/><h1>'.$name.'</h1></td>
					<td width = "15%"><br/><br/>
						<table align  "right" border = "1px" width = "100%">
							<tr>
								<td>Exam No.</td>
							</tr>
							<tr>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align = "center"><h3>Yearly Exam Result  Year 20__ - 20___</h3></td>
	</tr>
	<tr>
		<td>
			<table cellpadding = "4px">
				<tr>
					<td style = "border-bottom:1px dashed #ddd">Student Name :- '.$student['firstname'].' '.$student['lastname'].'</td>
					<td style = "border-bottom:1px dashed #ddd">Standard :- '.$student['class'].'</td>
					<td style = "border-bottom:1px dashed #ddd">Division :- '.$student['section'].'</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table border = "1px" cellpadding = "4" width = "100%">
				<tr style = "background-color:#efefef"><td width = "7%">Subject</td>
					<td width = "58%">
						<table border = "" width = "100%" cellpadding = "4px">
							<tr>';
								$obtain_marks = 0;
								$total_marks = 0;
								$result = "Pass";
								$exam_results_array = $array['exam_result'];
								$s = 0;
								foreach ($exam_results_array as $result_k => $result_v) {
									$table .= '<td style = "border-bottom:1px solid #000;">'.$result_v['exam_name'] . ' (' . substr($result_v['exam_type'], 0, 2) . '.) </td>';
								}
							$table .= '</tr><tr>';
								foreach ($exam_results_array as $result_k => $result_v) {
									$table .= '<td>'.$result_v['full_marks'].'</td>';
								}
					
					$table .= '</tr>
						</table>
					</td>
					<td width = "7%"></td>
					<td width = "7%">Total Marks</td>';
					/*<td width = "7%">Percent Marks</td>*/
					$table .= '<td width = "6%">Self-Development and Art Appreciation</td>
					<td width = "7%">Defence studies</td>
					<td width = "7%">Student Rank</td>
				</tr>
				<tr>
					<td>Total Marks</td>
					<td>
						<table border = "" width = "100%" cellpadding = "4px">
							<tr>';
								foreach ($exam_results_array as $result_k => $result_v) {
									$i++;
									$globalI = $i;
									if ($result_v['attendence'] == "pre") {
										$get_marks_student = $result_v['get_marks'];
										$table.= '<td>'.$get_marks_student.'</td>';
										$passing_marks_student = $result_v['passing_marks'];
										if ($result == "Pass") {
											if ($get_marks_student < $passing_marks_student) {
												$result = "Fail";
											}
										}
										if (is_numeric($result_v['get_marks'])) {
											$obtain_marks = (int) $obtain_marks + (int) $result_v['get_marks'];
											$total_marks = $total_marks + $result_v['full_marks'];
										}else{
											$gradeTest .= '1';
										}
									}else {
										$result = "Fail";
										$table .= '<td>'.$result_v['attendence'].'</td>';
									}
								}
							$table .= '</tr>
						</table>
					</td>
					<td> </td>
					<td>'.$total_marks.'</td>';
						// if($globalI == strlen($gradeTest)){
							// $table .='<td>N/A</td>';
						// }else{
							// $foo = ($obtain_marks * 100) / $total_marks;
							// $table .="<td>".number_format((float) $foo, 2, '.', '')."%</td>";
						// }
						// if (!empty($gradeList)) {
							// foreach ($gradeList as $key => $value) {
								// if ($foo >= $value['mark_from'] && $foo <= $value['mark_upto']) {
									// $table .= '<td>'.$this->lang->line('grade') . " : " . $value['name'].'</td>';
									// break;
								// }
							// }
						// }	
			$table .= '<td></td><td></td><td></td>
				</tr>
			</table>
		</td>
	</tr>	
	<tr>
		<td>
			<table width = "100%">
				<tr>
					<td align = "left">Result :'.$result.'</td>
					<td align = "right">School will open on date ___/___/20__. </td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width = "100%">
				<tr>
					<td align = "left"><br/><br/><br/><br/>Class Teacher</td>
					<td align = "left"><br/><br/><br/><br/>Chief Moderator</td>
					<td align = "left"><br/><br/><br/><br/>Invigilator</td>
					<td align = "left"><br/><br/><br/>Headmaster</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>'.$address.'</td>
	</tr>
	</table>';
		}
	// echo $table;	
	 $pdf->writeHTML($table, true, false, true, false, '');
	 $pdf->Output(''.$name.'.pdf', 'I');
    }
}

?>