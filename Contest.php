<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Contest extends Admin_Controller {
	public function __construct() {
        parent::__construct();
	}
	public function index()
	{
		$data['page_name'] = 'contest';
		
		$data['contest'] = $this->db->get_where('kt_contest', array('kt_ct_deleted'=>'N'))->result_array();

		$this->view('contest',$data);

	}

	public function add_contest()
	{
		$data['page_name'] = 'contest';
	
		
		$this->view('add_contest',$data);
	}


	public function view_contest($kt_ct_id = false)
	{
		$data['page_name'] = 'contest';
	
		if($kt_ct_id!=false) 

			{

		$data['contest'] = $this->db->get_where('kt_contest', array('kt_ct_id'=>$kt_ct_id))->row_array();

		$data['videos'] = $this->db->order_by('kt_vid_id', 'DESC')->limit('4')->get_where('kt_video_upload', array('kt_ct_id'=>$kt_ct_id, 'kt_video_status' => 'A', ))->result_array();

		foreach($data['videos'] as $key => $val)
			{
			    $child = $this->db->get_where('kt_child_details',array('kt_cd_id'=>$val['kt_cd_id']))->row();
			    
			    $data['videos'][$key]['studentname'] = $child->kt_cd_name;
			    
			    $data['videos'][$key]['age'] 	 	 = $child->kt_cd_age;

			    $data['videos'][$key]['dob'] 	 	 = $child->kt_cd_dob;
			    
			    $data['videos'][$key]['gender'] 	 = $child->kt_cd_gender;
			    
			    $data['videos'][$key]['school'] 	 = $child->kt_cd_school_name;
			    
			    $data['videos'][$key]['location'] 	 = $child->kt_cd_location;
				
				$user = $this->db->get_where('kt_parent_details',array('kt_pd_id'=>$val['kt_pd_id']))->row();
			    
			    $data['videos'][$key]['username'] = $user->kt_pd_username;
		    }



	}

		$this->view('view_contest',$data);
	}

	

	public function submit()
	{
		$this->form_validation->set_rules('contest_name','Contest Name','trim|required|callback_alpha_dash_space');
		$this->form_validation->set_rules('contest_title','Contest Title','required');
		$this->form_validation->set_rules('age_limit','Age Limit','required');
		
		if ($this->form_validation->run() == false)
		{
			$this->session->set_flashdata('posted',$_POST);
			$this->session->set_flashdata('error',validation_errors());
			redirect(ADMIN . '/contest', 'refresh');
		}
		else
		{
			$kt_ct_id 					= $this->input->post('kt_ct_id');
			$insert['kt_ct_name'] 		= $this->input->post('contest_name');
			/*$insert['kt_ct_image'] 		= $this->input->post('contest_image');
			$insert['kt_ct_thumbnail'] 	= $this->input->post('thumbnail_image');*/
			$insert['kt_ct_title'] 		= $this->input->post('contest_title');
			$insert['kt_ct_desc'] 		= $this->input->post('contest_desc');
			$insert['kt_ct_terms'] 		= $this->input->post('contest_terms');
			$insert['kt_ct_category'] 	= $this->input->post('contest_category');
			$insert['kt_ct_age'] 		= $this->input->post('age_limit');
			$insert['kt_ct_lastdate'] 	= $this->input->post('last_date');

			$file_name = "";
			$image_url = "";
			if(isset($_FILES['contest_image']) && !empty($_FILES['contest_image']['name'])) {
				if ($_FILES['contest_image']['name']) {
					$fileExt = pathinfo($_FILES['contest_image']['name'], PATHINFO_EXTENSION);
					if($fileExt=='jpg' || $fileExt=='jpeg' || $fileExt=='gif' || $fileExt=='png') {
						$folderPath = dirname($_SERVER["SCRIPT_FILENAME"])."/uploads/contest/";
						if(!is_dir($folderPath)) mkdir($folderPath, 0777, TRUE);
						$target_path = "uploads/contest/";
						$file_name = round(microtime(true)).".".$fileExt;
						$target_path = $target_path.$file_name;  
						if(move_uploaded_file($_FILES['contest_image']['tmp_name'], $target_path)) {
							$full_path = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME']).'/uploads/contest/'.$file_name;
						} 
					} 
				}
				$insert['kt_ct_image'] = "uploads/contest/".$file_name;

			}

			
			$file_name = "";
			$image_url = "";
			if(isset($_FILES['thumbnail_image']) && !empty($_FILES['thumbnail_image']['name'])) {
				if ($_FILES['thumbnail_image']['name']) {
					$fileExt = pathinfo($_FILES['thumbnail_image']['name'], PATHINFO_EXTENSION);
					if($fileExt=='jpg' || $fileExt=='jpeg' || $fileExt=='gif' || $fileExt=='png') {
						$folderPath = dirname($_SERVER["SCRIPT_FILENAME"])."/uploads/contest_thumbnail/";
						if(!is_dir($folderPath)) mkdir($folderPath, 0777, TRUE);
						$target_path = "uploads/contest_thumbnail/";
						$file_name = round(microtime(true)).".".$fileExt;
						$target_path = $target_path.$file_name;  
						if(move_uploaded_file($_FILES['thumbnail_image']['tmp_name'], $target_path)) {
							$full_path = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME']).'/uploads/contest_thumbnail/'.$file_name;
						} 
					} 
				}
				$insert['kt_ct_thumbnail'] = "uploads/contest_thumbnail/".$file_name;

			}
			if($kt_ct_id > 0)
			{
				if (isset($insert['kt_ct_image']) && ($insert['kt_ct_thumbnail']) != '') {

				/*$file_name = $this->db->select("kt_ct_image")->get_where("kt_contest")->row();

				$file_name1 = $this->db->select("kt_ct_thumbnail")->get_where("kt_contest")->row();

				$insert['kt_ct_image'] 		= "uploads/contest/";

				$insert['kt_ct_thumbnail'] 	= "uploads/contest_thumbnail/";*/
				
				$this->db->where('kt_ct_id', $kt_ct_id);
	
				$this->db->update('kt_contest', $insert);

				$this->session->set_flashdata('success', 'Contest Details Updated Successfully');
				}

				else{

					$this->db->where('kt_ct_id', $kt_ct_id);

					$this->db->update('kt_contest', $insert);

					$this->session->set_flashdata('success', 'Contest Details Updated Successfully');
				}

			
			}

			else
			{


				$this->db->where('kt_ct_id', $kt_ct_id);

				$this->db->insert('kt_contest', $insert);

				$this->session->set_flashdata('success', 'Contest Details Added Successfully');
			}
		}	

		redirect(ADMIN . '/contest', 'refresh');
			}
			public function view_contest_list()
			{
				$data['page_name'] = 'contest';


				$data['contest_lists'] = $this->db->get_where('kt_contest')->result_array();


				$this->view('view_contest_list', $data);
			}	
			public function edit_contest($kt_ct_id = false)
			{
				$data['page_name'] = 'contest';

				if($kt_ct_id!=false) 

				{
					$data['row'] = $this->db->get_where('kt_contest', array('kt_ct_id' => $kt_ct_id))->row_array();

				}

				$this->view('edit_contest', $data);
			}
			public function delete_contest($kt_ct_id)
			{
				$status = $this->input->get('status');

				if ($status=="Y") {
					
				$this->db->where('kt_ct_id', $kt_ct_id);
				$this->db->set('kt_ct_deleted', 'Y');
				$this->db->update('kt_contest');
				$this->session->set_flashdata('success','Contest has been Inactive Now');
				}
				else{
				$this->db->where('kt_ct_id', $kt_ct_id);
				$this->db->set('kt_ct_deleted', 'N');
				$this->db->update('kt_contest');
				$this->session->set_flashdata('success','Contest has been Active Now');	
				}
				redirect(ADMIN . '/contest/view_contest_list/', 'refresh');
			}


	}