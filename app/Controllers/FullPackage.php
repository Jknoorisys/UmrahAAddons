<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FullPackage as ModelsFullPackage;
use App\Models\FullPackageDates;
use App\Models\FullPackageImages;
use App\Models\ProviderModel;
use Config\Services;
use Exception;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

// headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control");

class FullPackage extends BaseController
{
    private $user_id = null;
	private $user_role = null;
	private $token = null;
	private $service;

	public function __construct()
	{
		$this->service  = new Services();
		$this->service->cors();

		helper('auth');
		helper('notifications');

		$lang = (isset($_POST["language"]) && !empty($_POST["language"])) ? $_POST["language"] : '';
		if (!empty($lang)) {
			$language = \Config\Services::language();
			$language->setLocale($lang);
		} else {
			echo json_encode(['status' => 403, 'messages' => 'Language is Required']);
			die();
		}

		$db = \Config\Database::connect();
		// Check Authentication
        
		$this->token = $token = (isset($_POST["authorization"]) && !empty($_POST["authorization"])) ? $_POST["authorization"] : '';
		$this->user_id = $user_id = (isset($_POST["logged_user_id"]) && !empty($_POST["logged_user_id"])) ? $_POST["logged_user_id"] : '';
		$this->user_role = $user_role = (isset($_POST["logged_user_role"]) && !empty($_POST["logged_user_role"])) ? $_POST["logged_user_role"] : '';

        if (empty($token)) {
			echo json_encode(['status' => 403, 'messages' => 'Authorization Token is Required']);
			die();
		} 

        if (empty($user_id)) {
			echo json_encode(['status' => 403, 'messages' => 'User ID is Required']);
			die();
		} 

        if (empty($user_role)) {
			echo json_encode(['status' => 403, 'messages' => 'User Role is Required']);
			die();
		} 

		if (!$this->service->getAccessForSignedUser($token, $user_role)) {
			echo json_encode(['status' => 'failed', 'messages' => 'Access denied', 'status_code' => '401']);
			die();
		}

		$timezone = "Asia/Kolkata";
		date_default_timezone_set($timezone);
	}

    // add package by Admin - by Javeriya Kauser
    public function addPackage()
    {
        $service   =  new Services();
        $FullPackageImage = new FullPackageImages();
        $service->cors();

        $rules = [
            'name' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'duration' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'mecca_hotel' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'mecca_hotel_distance' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'madinah_hotel' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'madinah_hotel_distance' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'depurture_dates' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'details' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'inclusions' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'single_rate_SAR' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'single_rate_INR' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'infant_rate_with_bed_SAR' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'infant_rate_with_bed_INR' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'infant_rate_without_bed_SAR' => [
                'rules'         =>  'required',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'infant_rate_without_bed_INR' => [
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

        if (isset($_FILES) && !empty($_FILES)) {
            $file = $this->request->getFile('main_img');
            if (!$file->isValid()) {
                throw new RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
            } else {
                $path = 'public/assets/uploads/full-package/main_pic/';
                $newName = $file->getRandomName();
                $file->move($path, $newName);
            }
        } else {
            echo json_encode(['status' => 'failed', 'messages' => lang('Language.Images required')]);
            die();
        }

        $data = [
            "name" => $this->request->getPost("name"),
            "duration" => $this->request->getPost("duration"),
            "mecca_hotel" => $this->request->getPost("mecca_hotel"),
            "mecca_hotel_distance" => $this->request->getPost("mecca_hotel_distance"),
            "madinah_hotel" => $this->request->getPost("madinah_hotel"),
            "madinah_hotel_distance" => $this->request->getPost("madinah_hotel_distance"),
            "details" => $this->request->getPost("details"),
            "main_img" =>  $path . $newName,
            "inclusions" => $this->request->getPost("inclusions"),
            "single_rate_SAR" => $this->request->getPost("single_rate_SAR"),
            "single_rate_INR" => $this->request->getPost("single_rate_INR"),
            "double_rate_SAR" => $this->request->getPost("double_rate_SAR") ? $this->request->getPost("double_rate_SAR") : '',
            "double_rate_INR" => $this->request->getPost("double_rate_INR") ? $this->request->getPost("double_rate_INR") : '',
            "triple_rate_SAR" => $this->request->getPost("triple_rate_SAR") ? $this->request->getPost("triple_rate_SAR") : '',
            "triple_rate_INR" => $this->request->getPost("triple_rate_INR") ? $this->request->getPost("triple_rate_INR") : '',
            "quad_rate_SAR" => $this->request->getPost("quad_rate_SAR") ? $this->request->getPost("quad_rate_SAR") : '',
            "quad_rate_INR" => $this->request->getPost("quad_rate_INR") ? $this->request->getPost("quad_rate_INR") : '',
            "pent_rate_SAR" => $this->request->getPost("pent_rate_SAR") ? $this->request->getPost("pent_rate_SAR") : '',
            "pent_rate_INR" => $this->request->getPost("pent_rate_INR") ? $this->request->getPost("pent_rate_INR") : '',
            "infant_rate_with_bed_SAR" => $this->request->getPost("infant_rate_with_bed_SAR"),
            "infant_rate_with_bed_INR" => $this->request->getPost("infant_rate_with_bed_INR"),
            "infant_rate_without_bed_SAR" => $this->request->getPost("infant_rate_without_bed_SAR"),
            "infant_rate_without_bed_INR" => $this->request->getPost("infant_rate_without_bed_INR"),
            "status" => '1',
            "created_at" => date('Y-m-d H:i:s')
        ];

        $db = db_connect();
        $fullPackage =  $db->table('tbl_full_package')->insert($data);
        $full_package_id = $db->insertID();
        $image_array = $this->request->getPost("image_array");

        if ($fullPackage && $full_package_id) {

            $dates = explode(',',$this->request->getPost("depurture_dates"));
            foreach ($dates as $date) {
                $dates_data = [
                    'full_package_id' => $full_package_id,
                    'date'            => $date,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $deperture_date = $db->table('tbl_full_package_dates')->insert($dates_data);
            }

           
            foreach ($this->request->getFileMultiple('image_array') as $file) {
                $package_pic_path = 'public/assets/uploads/full-package/package_pic/';
                $new_name = $file->getRandomName();
                $data = [
                    'full_package_id' => $full_package_id,
                    'image' => $package_pic_path . $new_name,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $save = $FullPackageImage->insert($data);
                $file->move($package_pic_path, $new_name);
            }

            return $service->success([
                            'message'       =>  Lang('Language.Package Created Successfully'),
                            'data'          =>  ""
                        ],
                        ResponseInterface::HTTP_CREATED,
                        $this->response
                    );
        } else {
            return $service->fail(
                [
                    'message'   =>  Lang('Language.add_failed'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }

    // Delete package by Admin - by Javeriya Kauser
    public function packageDelete()
    {
        $package   =  new ModelsFullPackage();
        $service   =  new Services();
        $service->cors();

        $package_id       =  $this->request->getVar('full_package_id');

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
            'full_package_id' => [
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
                $isExist = $package->where(['id'=> $package_id])->first();
                if(!empty($isExist))
                {
                    $db = db_connect();
                    $delete = $db->table('tbl_full_package')->where('id', $package_id)->update(['status' => '2']);
                    if ($delete) {
                        $db->table('tbl_full_package_dates')->where('id', $package_id)->delete();
                        $db->table('tbl_full_package_image')->where('id', $package_id)->delete();

                        return $service->success([
                            'message'       =>  Lang('Language.delete_success'),
                            'data'          =>  ''
                            ],
                            ResponseInterface::HTTP_OK,
                            $this->response
                        );
                    } else {
                        return $service->fail([
                            'errors'    =>  "",
                            'message'       =>  Lang('Language.delete_failed'),
                            ],
                            ResponseInterface::HTTP_BAD_REQUEST,
                            $this->response
                        );
                    }
                } else {
                    return $service->fail(
                        [
                            'message'   =>  Lang('Language.Package Not Found'),
                        ],
                        ResponseInterface::HTTP_BAD_REQUEST,
                        $this->response
                    );
                }

        } catch (Exception $e) {
            return $service->fail(
                [
                    'errors'    =>  "",
                    'message'   =>  Lang('Language.delete_failed'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }

    // package list by Admin - by Javeriya Kauser
    public function packageList()
    {
        $service   =  new Services();
        $service->cors();

        $pageNo           =  $this->request->getVar('pageNo');

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
            $search        = $this->request->getVar('search');

            $db = db_connect();
            $table = $db->table('tbl_full_package as p')->where('status !=', '2');
            
            if (isset($search) && !empty($search)) {
                $table->like('p.name', $search);
            }
            
            $data = $table->orderBy('p.id', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResult(); // Fetch the paginated results
                
            $total = count($data);

            return $service->success(
                [
                    'message'       =>  Lang('Language.list_success'),
                    'data'          =>  [
                        'total'             =>  $total,
                        'packages'         =>  $data,
                    ]
                ],
                ResponseInterface::HTTP_OK,
                $this->response
            );

        } catch (Exception $e) {
            return $service->fail(
                [
                    'errors'    =>  $e->getMessage(),
                    'message'   =>  Lang('Language.fetch_list'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }

    // view package by Admin - by Javeriya Kauser
    public function viewPackage()
    {
        $package   =  new ModelsFullPackage();
        $dates   =  new FullPackageDates();
        $images   =  new FullPackageImages();
        $service   =  new Services();
        $service->cors();

        $package_id       =  $this->request->getVar('full_package_id');

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
            'full_package_id' => [
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
            $isExist = $package->where('id',$package_id)->where('status !=','2')->first();
                if(!empty($isExist))
                {
                $db = db_connect();
                $isExist['departure_dates'] = $db->table('tbl_full_package_dates')->where('full_package_id', $package_id)->get()->getResult();
                $isExist['images'] = $db->table('tbl_full_package_image')->where('full_package_id', $package_id)->get()->getResult();

                return $service->success([
                    'message'       =>  Lang('Language.details_success'),
                    'data'          =>  $isExist
                    ],
                    ResponseInterface::HTTP_OK,
                    $this->response
                );
                } else {
                    return $service->fail(
                        [
                            'errors'    =>  "",
                            'message'   =>  Lang('Language.Package Not Found'),
                        ],
                        ResponseInterface::HTTP_BAD_REQUEST,
                        $this->response
                    );
                }

        } catch (Exception $e) {
            return $service->fail(
                [
                'errors'    =>  $e->getMessage(),
                    'message'   =>  Lang('Language.details_fetch_failed'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }

    public function changePackageStatus()
    {
        $packageModel        =  new ModelsFullPackage();
        $service        =  new Services();
        $service->cors();

        $full_package_id            =  $this->request->getVar('full_package_id');
        $status            =  $this->request->getVar('status');

        $rules = [
            'language' => [
                'rules'         =>  'required|in_list[' . LANGUAGES . ']',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'in_list'       =>  Lang('Language.in_list', [LANGUAGES]),
                ]
            ],
            'full_package_id' => [
                'rules'         =>  'required|numeric',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                ]
            ],
            'status' => [
                'rules'         =>  'required|in_list[0,1]',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'in_list'       =>  Lang('Language.in_list', [0,1]),
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
            $duaDetails = $packageModel->where("id", $full_package_id)->where("status !=",'2')->first();
            if (empty($duaDetails)) {
                return $service->fail(
                    [
                        'errors'    =>  "",
                        'message'   =>  Lang('Language.Dua Not Found'),
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST,
                    $this->response
                );
            }

            $db = db_connect();
            $update = $db->table('tbl_full_package')
                ->where('id', $full_package_id)
                ->set('status', $status)
                ->update();

            if($update) 
            {
                return $service->success([
                        'message'       =>  Lang('Language.Package status changed successfully'),
                        'data'          =>  ""
                    ],
                    ResponseInterface::HTTP_CREATED,
                    $this->response
                );
            } else {
                return $service->fail(
                    [
                        'errors'    =>  "",
                        'message'   =>  Lang('Language.Unable to change Package status, please try again'),
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST,
                    $this->response
                );
            }

        } catch (Exception $e) {
            return $service->fail(
                [
                    'errors'    =>  $e->getMessage(),
                    'message'   =>  Lang('Language.Unable to change Package status, please try again'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }

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
                    'errors'    =>  $e->getMessage(),
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
                    'errors'    =>  $e->getMessage(),
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
                    'errors'    =>  $e->getMessage(),
                    'message'   =>  Lang('Language.details_fetch_failed'),
                ],
                ResponseInterface::HTTP_BAD_REQUEST,
                $this->response
            );
        }
    }
}
