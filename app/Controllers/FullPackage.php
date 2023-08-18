<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Services;
use Exception;

use CodeIgniter\HTTP\ResponseInterface;

// headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control");

class FullPackage extends BaseController
{

    public function enquiryList()
    {
        $service   =  new Services();
        $service->cors();

        $pageNo           =  $this->request->getVar('pageNo');
        $user_role        =  $this->request->getVar('logged_user_role');
        $logged_user_id   =  $this->request->getVar('logged_user_id');

        $rules = [
            'pageNo' => [
                'rules'         =>  'required|greater_than[' . PAGE_LENGTH . ']|numeric',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'greater_than'  =>  Lang('Language.greater_than', [PAGE_LENGTH]),
                    'numeric'       =>  Lang('Language.numeric', [$pageNo]),
                ]
            ],
            'language' => [
                'rules'         =>  'required|in_list[' . LANGUAGES . ']',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'in_list'       =>  Lang('Language.in_list', [LANGUAGES]),
                ]
            ],
            'logged_user_id' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'logged_user_role' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
        ];

        if(!$this->validate($rules)) {
            return $service->fail(
                [
                    'errors'     =>  $this->validator->getErrors(),
                    'message'   =>  lang('Language.invalid_inputs')
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
       
        try{
            $currentPage   = ( !empty( $pageNo ) ) ? $pageNo : 1;
            $offset        = ( $currentPage - 1 ) * PER_PAGE;
            $limit         =  PER_PAGE;

            $whereCondition = '';

            if($user_role == 'admin'){ $whereCondition .= "e.status = '1'"; }

            elseif($user_role == 'user'){ $whereCondition .= "e.user_id = ".$logged_user_id." AND e.status = '1'"; }

            $db = db_connect();
            $data = $db->table('tbl_full_package_enquiry as e')
                ->join('tbl_user as u','u.id = e.user_id')
                ->join('tbl_full_package as p','p.id = e.full_package_id')
                ->select("e.*, CONCAT(u.firstname,' ',u.lastname) as user_name, p.name as package_name")
                ->where($whereCondition)
                ->orderBy('e.id', 'DESC')
                ->limit($limit, $offset)
                ->get()->getResult();
                
            $total = count($data);

            return $service->success(
                [
                    'message'       =>  Lang('Language.list_success'),
                    'data'          =>  [
                        'total'             =>  $total,
                        'enquiries'         =>  $data,
                    ]
                ],
                ResponseInterface::HTTP_OK,
                $this->response
            );

        } catch (Exception $e) {
            return $service->fail(
                [
                    'errors'    =>  "",
                    'message'   =>  Lang('Language.fetch_list'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }

    public function addEnquiry()
    {
        $service   =  new Services();
        $service->cors();

        $logged_user_id    =  $this->request->getVar('logged_user_id');
        $user_role         =  $this->request->getVar('logged_user_role');

        $ota_id            =  $this->request->getVar('ota_id');
        $full_package_id   =  $this->request->getVar('full_package_id');
        $name              =  $this->request->getVar('name');
        $date              =  $this->request->getVar('date');
        $country_code      =  $this->request->getVar('country_code');
        $mobile            =  $this->request->getVar('mobile');
        $no_of_seats       =  $this->request->getVar('no_of_seats');

        $rules = [
            'language' => [
                'rules'         =>  'required|in_list[' . LANGUAGES . ']',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'in_list'       =>  Lang('Language.in_list', [LANGUAGES]),
                ]
            ],
            'logged_user_id' => [
                'rules'         =>  'required|numeric',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'logged_user_role' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'ota_id' => [
                'rules'         =>  'required|numeric',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'full_package_id' => [
                'rules'         =>  'required|numeric',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'name' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'country_code' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'mobile' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'date' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'no_of_persons' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
        ];

        if(!$this->validate($rules)) {
            return $service->fail(
                [
                    'errors'     =>  $this->validator->getErrors(),
                    'message'   =>  lang('Language.invalid_inputs')
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }

        try {
            $data = array(
                'user_id'       => $logged_user_id,
                'ota_id'        => $ota_id,
                'full_package_id' => $full_package_id,
                'name'          => (isset($name)) ? $name: '',
                'country_code'  => (isset($country_code)) ? $country_code : '',
                'mobile'        => (isset($mobile)) ? $mobile : '',
                'date'          => (isset($date)) ? $date : '',
                'no_of_seats'   => (isset($no_of_seats)) ? $no_of_seats : '',
                'booking_status'  => 'pending',
                'created_at'  => date('Y-m-d H:i:s')
            );

            $db = db_connect();
            $packageEnquiry = $db->table('tbl_full_package_enquiry')->insert($data);

            if($packageEnquiry) 
            {
                // PUSH NOTIFICATION
                helper('notifications');
                $db = db_connect();
                $userinfo = $db->table('tbl_user')
                    ->select('*')
                    ->where('id', $_POST['logged_user_id'])
                    ->get()->getRow();

                $title = "Full package Enquiry";
                $message = "Your Enquiry has been sent. Thank you.";
                $fmc_ids = array($userinfo->device_token);
                
                $notification = array(
                    'title' => $title ,
                    'message' => $message,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // DO NOT CHANGE THE VALUE
                    'date' => date('Y-m-d H:i'),
                );
                if($userinfo->device_type!='web'){ sendFCMMessage($notification, $fmc_ids); }
                // EnD

                return $service->success([
                        'message'       =>  Lang('Language.add_success'),
                        'data'          =>  ""
                    ],
                    ResponseInterface::HTTP_CREATED,
                    $this->response
                );
            } else {
                return $service->fail(
                    [
                        'errors'    =>  "",
                        'message'   =>  Lang('Language.add_failed'),
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST,
                    $this->response
                );
            }

        } catch (Exception $e) {
            return $service->fail(
                [
                    'errors'    =>  "",
                    'message'   =>  Lang('Language.add_failed'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }

    public function viewEnquiry()
    {
        $service   =  new Services();
        $service->cors();

        $user_role        =  $this->request->getVar('logged_user_role');
        $logged_user_id   =  $this->request->getVar('logged_user_id');
        $enquiry_id       =  $this->request->getVar('enquiry_id');

        $rules = [
            'language' => [
                'rules'         =>  'required|in_list[' . LANGUAGES . ']',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'in_list'       =>  Lang('Language.in_list', [LANGUAGES]),
                ]
            ],
            'logged_user_id' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'logged_user_role' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'enquiry_id' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
        ];

        if(!$this->validate($rules)) {
            return $service->fail(
                [
                    'errors'     =>  $this->validator->getErrors(),
                    'message'   =>  lang('Language.invalid_inputs')
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
       
        try {
            $db = db_connect();
            $info = $db->table('tbl_full_package_enquiry as e')
                ->join('tbl_user as u','u.id = e.user_id')
                ->join('tbl_full_package as p','p.id = e.full_package_id')
                ->select("e.*, CONCAT(u.firstname,' ',u.lastname) as user_name, p.name as package_name")
                ->where('e.status','1')
                ->where('e.id',$enquiry_id)
                ->get()->getRow();

            if(!empty($info))
            {
                return $service->success([
                    'message'       =>  Lang('Language.details_success'),
                    'data'          =>  $info
                    ],
                    ResponseInterface::HTTP_OK,
                    $this->response
                );
            } else {
                return $service->fail(
                    [
                        'errors'    =>  "",
                        'message'   =>  Lang('Language.details_fetch_failed'),
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST,
                    $this->response
                );
            }
        } catch (Exception $e) {
            return $service->fail(
                [
                    'errors'    =>  "",
                    'message'   =>  Lang('Language.details_fetch_failed'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }
}
