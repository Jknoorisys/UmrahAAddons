<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Duas;
use App\Models\FullPackage;
use App\Models\FullPackageDates;
use App\Models\FullPackageImages;
use App\Models\Visa;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

use Config\Services;


// headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control");

class UserLists extends BaseController
{
    private $service;
	
	public function __construct()
	{
		$this->service  = new Services();
		helper('auth');

        $lang = $_POST["language"];
		if (!empty($lang)) {
			$language = \Config\Services::language();
			$language->setLocale($lang);
		} else {
			echo json_encode(['status' => 403, 'messages' => 'language required']);
			die();
		}
	}

   	// Dua List by Javeriya
	public function listOfDua()
    {
        $service           =  new Services();
        $service->cors();

        $pageNo           =  $this->request->getVar('pageNo');
        $user_role        =  'user';

        $search           =  $this->request->getVar('search');
        $type           =  $this->request->getVar('type');
        $language = $this->request->getVar('language');

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

            $whereCondition = "";

            if(isset($search) && $search!=''){
                $whereCondition .= "s.title LIKE'%" . $search . "%' AND ";
            }

            if(isset($type) && $type!=''){
                $whereCondition .= "s.type = '" . $type . "' AND "; 
            }

            if($user_role == 'admin'){ $whereCondition .= "s.status != '2'"; } 

            if($user_role == 'user'){ $whereCondition .= "s.status = '1'"; } 

            if($user_role == 'provider'){ $whereCondition .= "s.status = '1'"; }

            // By Query Builder
            $db = db_connect();
            if ($language == 'en') {
                $duasData = $db->table('tbl_duas as s')
                                ->select('s.id, s.user_id, s.user_type, s.title_en as title, s.reference_en as reference, s.image, s.type, s.status, s.created_at, s.updated_at')
                                ->where($whereCondition)
                                ->orderBy('s.id', 'DESC')
                                ->limit($limit, $offset)
                                ->get()->getResult();
            } else {
                $duasData = $db->table('tbl_duas as s')
                                ->select('s.id, s.user_id, s.user_type, s.title_ur as title, s.reference_ur as reference, s.image, s.type, s.status, s.created_at, s.updated_at')
                                ->where($whereCondition)
                                ->orderBy('s.id', 'DESC')
                                ->limit($limit, $offset)
                                ->get()->getResult();
            }
            

            $total =  $db->table('tbl_duas as s')->where($whereCondition)->countAllResults();

            return $service->success(
                [
                    'message'       =>  Lang('Language.list_success'),
                    'data'          =>  [
                        'total'             =>  $total,
                        'duasList'         =>  $duasData,
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

	// view Dua by Javeriya
	public function viewDua()
    {
        $duaModel        =  new Duas();
        $service        =  new Services();
        $service->cors();

        $dua_id            =  $this->request->getVar('dua_id');
        $language           = $this->request->getVar('language');

        $rules = [
            'language' => [
                'rules'         =>  'required|in_list[' . LANGUAGES . ']',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'in_list'       =>  Lang('Language.in_list', [LANGUAGES]),
                ]
            ],
            'dua_id' => [
                'rules'         =>  'required|numeric',
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

             // By Query Builder
             $db = db_connect();

            if ($language == 'en') {
                $duaDetails = $db->table('tbl_duas as s')
                                ->select('s.id, s.user_id, s.user_type, s.title_en as title, s.reference_en as reference, s.image, s.type, s.status, s.created_at, s.updated_at')
                                ->where("id", $dua_id)
                                ->where("status",'1')
                                ->get()->getRow();
            } else {
                $duaDetails = $db->table('tbl_duas as s')
                                ->select('s.id, s.user_id, s.user_type, s.title_ur as title, s.reference_ur as reference, s.image, s.type, s.status, s.created_at, s.updated_at')
                                ->where("id", $dua_id)
                                ->where("status",'1')
                                ->get()->getRow();
            }

            // $duaDetails = $duaModel->where("id", $dua_id)->where("status",'1')->first();

            if(!empty($duaDetails)) 
            {
                return $service->success([
                        'message'       =>  Lang('Language.details_success'),
                        'data'          =>  $duaDetails
                    ],
                    ResponseInterface::HTTP_CREATED,
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

	// Visa Price by Javeriya
	public function listOfVisaPrice()
    {
        $service           =  new Services();
        $service->cors();

        $user_role        =  'user';

        $rules = [
            'language' => [
                'rules'         =>  'required|in_list[' . LANGUAGES . ']',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'in_list'       =>  Lang('Language.in_list', [LANGUAGES]),
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

            $whereCondition = "";

            if($user_role == 'admin'){ $whereCondition .= "s.status != '2'"; } 

            if($user_role == 'user'){ $whereCondition .= "s.status = '1'"; } 

            if($user_role == 'provider'){ $whereCondition .= "s.status = '1'"; }

            // By Query Builder
            $db = db_connect();
            $visaPrice = $db->table('tbl_visa as s')
                ->select('s.*')
                ->where($whereCondition)
                ->orderBy('s.id', 'DESC')
                ->get()->getRow();

            return $service->success(
                [
                    'message'       =>  Lang('Language.list_success'),
                    'data'          =>  $visaPrice
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

	// View Visa Price by Javeriya
    public function viewVisaPrice()
    {
        $visaModel        =  new Visa();
        $service        =  new Services();
        $service->cors();

        $price_id            =  $this->request->getVar('price_id');

        $rules = [
            'language' => [
                'rules'         =>  'required|in_list[' . LANGUAGES . ']',
                'errors'        => [
                    'required'      =>  Lang('Language.required'),
                    'in_list'       =>  Lang('Language.in_list', [LANGUAGES]),
                ]
            ],
            'price_id' => [
                'rules'         =>  'required|numeric',
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
            $visaDetails = $visaModel->where("id", $price_id)->where("status !=",'2')->first();

            if(!empty($visaDetails)) 
            {
                return $service->success([
                        'message'       =>  Lang('Language.details_success'),
                        'data'          =>  $visaDetails
                    ],
                    ResponseInterface::HTTP_CREATED,
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

    // full package list- by Javeriya Kauser
    public function packageList(){
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
            $table = $db->table('tbl_full_package as p');

            if (isset($search) && !empty($search)) {
                $table->like('p.name', $search);
                $table->orLike('p.details', $search);
                $table->orLike('p.mecca_hotel', $search);
                $table->orLike('p.madinah_hotel', $search);
            }

            $data = $table->where('p.status', '1')
                        ->select('p.id, p.provider_id, p.name, p.duration, p.departure_city, p.mecca_hotel, p.mecca_hotel_distance, p.madinah_hotel, p.madinah_hotel_distance, p.details, p.main_img, p.inclusions, p.pent_rate_SAR as single_rate_SAR, p.pent_rate_INR as single_rate_INR, p.infant_rate_with_bed_SAR, p.infant_rate_with_bed_INR, p.infant_rate_without_bed_SAR, p.infant_rate_without_bed_INR, p.status, p.created_at, p.updated_at')
                        ->orderBy('p.id', 'DESC')
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
    
    // view full package- by Javeriya Kauser
    public function viewPackage(){
        $package   =  new FullPackage();
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
            $isExist = $package->where('id',$package_id)->where('status','1')->first();
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
}
