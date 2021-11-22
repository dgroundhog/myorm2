<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{


    protected $rgv = "A10902";

    protected $logger = null;

    /**
     * 访问者模型
     *
     * @var Employee
     */
    protected $_visitor = null;

    /**
     * 品牌
     *
     * @var Brand
     */
    protected $_brand = null;

    /**
     * 分类
     *
     * @var Category
     */
    protected $_category = null;


    /**
     * 单位
     *
     * @var Unit
     */
    protected $_unit = null;


    /**
     * 部门
     *
     * @var Department
     */
    protected $_department = null;


    /**
     * @var Url
     */
    protected $url;


    /**
     * 上下文数据池
     *
     * @var array
     */
    protected $_pool = null;


    /**
     * 获取
     *
     * @param string $key
     *
     * @return mixed
     * @throws Exception
     */
    public function getPool($key)
    {
        return $this->_pool[$key];
    }


    /**
     * @param $dispatcher
     * 优先级1
     */
    public function beforeExecuteRoute($dispatcher)
    {
        $this->assign('route_controller_name', $dispatcher->getControllerName());
        $this->assign('route_action_name', $dispatcher->getActionName());

        // 生成日志新组件实例
        $today = date("Y-m-d");
        $log_file = APP_PATH . "/app/logs/trace_{$today}.log";
        $this->logger = new FileAdapter($log_file);
        $this->logger->setLogLevel(Logger::WARNING);

        // 开启事务
        $this->logger->begin();
        // 添加消息
    }

    /**
     * Register an authenticated user into session data
     *
     * @param Users $user
     */
    public function _registerSession(Users $user)
    {
        $this->session->set(
            'auth',
            [
                'id' => $user->id,
                'name' => $user->name,
            ]
        );
    }


    /**
     * 优先级2
     */
    public function initialize()
    {

        $this->tag->prependTitle('INVO | ');
        $this->view->setTemplateAfter('main');

        $this->url = new Url();
        $this->url->setBaseUri("/public/");

        $this->assign('url_base', $this->url->getBaseUri());


        $a_common_url = array(
            'url_index' => 'index/index',

            'url_signin' => 'default/signin',
            'url_signout' => 'default/signout',
            'url_signin_as_su' => 'default/sa',
            'url_dashboard' => 'default/dashboard',

            'url_ticket_list' => 'op/ticket_list',
            'url_package_list' => 'op/package_list',
            'url_package_out' => 'op/package_out',
            'url_package_out_man' => 'op/package_out_man',
            'url_ajax_package_out' => 'op/ajax_package_out',
            'url_ajax_package_out_man' => 'op/ajax_package_out_man',
            'url_ajax_package_queue' => 'op/ajax_queue_package_list',
            'url_package_state' => 'op/package_state',
            'url_ajax_package_update' => 'op/ajax_package_update',

            'url_ajax_package_queue_list' => 'op/ajax_package_queue_list',
            'url_ajax_package_reorder' => 'op/ajax_package_reorder',
            'url_ajax_device_list' => 'op/ajax_device_list',
            'url_ajax_workbench_reset' => 'op/ajax_workbench_reset',
            'url_ajax_stacker_reset' => 'op/ajax_stacker_reset',
            'url_ajax_stacker_reset_dir' => 'op/ajax_stacker_reset_dir',

            'url_shelf_list' => 'op/shelf_list',
            'url_ajax_shelf_mix' => 'op/ajax_shelf_mix',
            'url_pallet_list' => 'op/pallet_list',
            'url_pallet_list_free' => 'op/pallet_list_free',
            'url_pallet_code_pdf' => 'op/pallet_code_pdf',
            'url_pallet_list_by_item' => 'op/pallet_list_by_item',
            'url_empty_pallet_out' => 'op/empty_pallet_out',

            'url_pallet_item_list' => 'op/pallet_item_list',
            'url_pallet_item_edit' => 'op/pallet_item_edit',

            'url_item_expired_list' => 'op/item_expired_list',

            'url_item_list' => 'data/item_list',
            'url_item_list_by_pallet' => 'data/item_list_by_pallet',
            'url_item_code1d' => 'data/item_code_print',
            'url_item_pdf' => 'data/item_code_pdf',
            'url_item_edit' => 'data/item_edit',

            'url_item_batch_list' => 'data/item_batch_list',
            'url_item_batch_list_by_item' => 'data/item_batch_list_by_item',
            'url_item_batch_list_by_pallet' => 'data/item_batch_list_by_pallet',
            'url_item_batch_code1d' => 'data/item_batch_code_print',
            'url_item_batch_pdf' => 'data/item_batch_code_pdf',
            'url_item_batch_edit' => 'data/item_batch_edit',

            'url_item_amount' => 'op/item_batch_amount',

            'url_brand_list' => 'data/brand_list',
            'url_brand_edit' => 'data/brand_edit',

            'url_category_list' => 'data/category_list',
            'url_category_edit' => 'data/category_edit',

            'url_unit_list' => 'data/unit_list',
            'url_unit_edit' => 'data/unit_edit',

            'url_department_list' => 'data/department_list',
            'url_department_edit' => 'data/department_edit',

            'url_data_import' => 'data/data_import',

            'url_department' => 'company/department',
            'url_employee' => 'company/employee',
            'url_employee_edit' => 'company/employee_edit',
            'url_employee_psd_edit' => 'company/employee_psd_edit',
            'url_profile' => 'company/profile',

            'url_role' => 'company/role',
            'url_role_edit' => 'company/role_edit',

            'url_report_item' => 'report/sum_item',
            'url_report_item_excel' => 'report/excel_sum_item',
            'url_report_shelf' => 'report/sum_shelf',
            'url_report_shelf_excel' => 'report/excel_sum_shelf',

            'url_report_item_in' => 'report/item_in',
            'url_report_item_out' => 'report/item_out',
            'url_report_item_full' => 'report/item_full',
            'url_report_item_inout_excel' => 'report/item_inout_excel',
            'url_report_packages' => 'report/packages',


        );


        foreach ($a_common_url as $key => $url0) {
            $url = $this->url->get($url0);
            $this->assign($key, $url);
        }


        $a_main_menus = array(

            'default' => array(
                'name' => '0、系统概况',
                'icon' => 'dashboard',
                'link' => $this->_pool['url_dashboard'],
                'sub_menu' => array()
            ),

            'op' => array(
                'name' => '1、任务管理',
                'icon' => 'tasks',
                'link' => "###",
                'sub_menu' => array(

                    'package_list' => array(
                        'name' => '任务队列',
                        'link' => $this->_pool['url_package_list'],
                        'icon' => 'random'
                    ),

                    'package_out' => array(
                        'name' => '物料出库',
                        'link' => $this->_pool['url_package_out'],
                        'icon' => 'sign-out'
                    ),

                    'item_expired_list' => array(
                        'name' => '到期物料提醒',
                        'link' => $this->_pool['url_item_expired_list'],
                        'icon' => 'clock-o'
                    ),


                    'empty_pallet_out' => array(
                        'name' => '空托盘出库',
                        'link' => $this->_pool['url_empty_pallet_out'],
                        'icon' => 'sign-out'
                    ),

                    'shelf_list' => array(
                        'name' => '货架',
                        'link' => $this->_pool['url_shelf_list'],
                        'icon' => 'trello'
                    ),

                    'pallet_list' => array(
                        'name' => '在架的托盘',
                        'link' => $this->_pool['url_pallet_list'],
                        'icon' => 'codepen'
                    ),

                    'pallet_list_free' => array(
                        'name' => '游离的托盘',
                        'link' => $this->_pool['url_pallet_list_free'],
                        'icon' => 'truck'
                    ),
                )
            ),

            'data' => array(
                'name' => '2、基础数据',
                'icon' => 'database',
                'link' => "###",
                'sub_menu' => array(
                    'item_batch_list' => array(
                        'name' => '物资和批次',
                        'link' => $this->_pool['url_item_batch_list'],
                        'icon' => 'calendar'
                    ),
                    'data_import' => array(
                        'name' => '批量导入',
                        'link' => $this->_pool['url_data_import'],
                        'icon' => 'truck'
                    ),
                    'category_list' => array(
                        'name' => '分类和组别',
                        'link' => $this->_pool['url_category_list'],
                        'icon' => 'cubes'
                    ),
                    'brand_list' => array(
                        'name' => '品牌',
                        'link' => $this->_pool['url_brand_list'],
                        'icon' => 'tags'
                    ),
                    'unit_list' => array(
                        'name' => '单位',
                        'link' => $this->_pool['url_unit_list'],
                        'icon' => 'puzzle-piece'
                    ),
                    'item_list' => array(
                        'name' => '纯物资信息',
                        'link' => $this->_pool['url_item_list'],
                        'icon' => 'sitemap'
                    ),

                )
            ),

            'report' => array(
                'name' => '3、统计报表',
                'icon' => 'archive',
                'link' => "###",
                'sub_menu' => array(
                    'item' => array(
                        'name' => '总库存表',
                        'link' => $this->_pool['url_report_item'],
                        'icon' => 'cubes'
                    ),
                    'shelf' => array(
                        'name' => '总储位表',
                        'link' => $this->_pool['url_report_shelf'],
                        'icon' => 'trello'
                    ),
                    'item_full' => array(
                        'name' => '库存分析',
                        'link' => $this->_pool['url_report_item_full'],
                        'icon' => 'sign-in'
                    )
                )
            ),

            'settings' => array(
                'name' => '4、管理设置',
                'icon' => 'cogs',
                'link' => "###",
                'sub_menu' => array(

                    'department_list' => array(
                        'name' => '部门管理',
                        'link' => $this->_pool['url_department_list'],
                        'icon' => 'group'
                    ),

                    'company' => array(
                        'name' => '用户管理',
                        'link' => $this->_pool['url_department'],
                        'icon' => 'group'
                    ),


                    'role_list' => array(
                        'name' => '权限管理',
                        'link' => $this->_pool['url_role'],
                        'icon' => 'cogs'
                    ),

                    'ent_list' => array(
                        'name' => '数据备份',
                        'link' => '###',
                        'icon' => 'database'
                    ),

                    'log_list' => array(
                        'name' => '命令日志',
                        'link' => "###",
                        'icon' => 'fa-list-ol'
                    )

                )
            ),

        );


        $this->_visitor = new Employee();

        $this->_category = new Category();
        $this->_brand = new Brand();
        $this->_unit = new Unit();
        $this->_department = new Department();


        /**
         * 基础数据
         */
        $a_category_list_kv = $this->_category->getDataListKv();
        $a_brand_list_kv = $this->_brand->getDataListKv();
        $a_unit_list_kv = $this->_unit->getDataListKv();
        $a_department_list_kv = $this->_department->getDataListKv();

        $a_employee_list_kv = Employee::getListKV();
        $a_employee_list_kv[666] = "admin";
        $a_item_log_types = Item::getLogTypes();


        $this->assign('a_category_list_kv', $a_category_list_kv);
        $this->assign('a_brand_list_kv', $a_brand_list_kv);
        $this->assign('a_unit_list_kv', $a_unit_list_kv);
        $this->assign('a_department_list_kv', $a_department_list_kv);

        $this->assign('a_employee_list_kv', $a_employee_list_kv);
        $this->assign('a_item_log_types', $a_item_log_types);

        //TODO 可以考虑整体放进缓存里

        list($i_w_count, $a_w_list) = Workbench::getList();
        list($i_s_count, $a_s_list) = Stacker::getList();
        list($i_c_count, $a_c_list) = Conveyor::getList();
        list($i_r_count, $a_r_list) = Rgv::getList();

        $this->assign('a_workbench_list', $a_w_list);
        $this->assign('a_stacker_list', $a_s_list);
        $this->assign('a_conveyor_list', $a_c_list);
        $this->assign('a_rgv_list', $a_r_list);


        $this->_workbenchXD = array("X" => array(), "D" => array());
        $this->_conveyorXD = array("X" => array(), "D" => array());
        $this->_stackerXD = array("X" => array(), "D" => array());

        foreach ($a_w_list as $a_row) {
            $this_num = $a_row['number'];
            $idx = $this->isDeviceXorD($this_num);
            $this->_workbenchXD[$idx][] = $this_num;
            $this->_workbenchList[$this_num] = $a_row;
        }

        foreach ($a_c_list as $a_row) {
            $this_num = $a_row['number'];
            $idx = $this->isDeviceXorD($this_num);
            $this->_conveyorXD[$idx][] = $this_num;
            $this->_conveyorList[$this_num] = $a_row;
        }


        foreach ($a_s_list as $a_row) {
            $this_num = $a_row['number'];
            $idx = $this->isDeviceXorD($this_num);
            $this->_stackerXD[$idx][] = $this_num;
            $this->_stackerList[$this_num] = $a_row;
        }


        $this->_mustLogin();
        /**
         * 其他公用静态数据,无db操作
         */
        $a_main_menus_ok['default'] = $a_main_menus['default'];
        try {
            $a_visitor_info = $this->_visitor->getInfo();

            $user_level = $a_visitor_info['level'];

            if ($user_level >= Employee::LEVEL_STAFF) {
                $a_main_menus_ok['op'] = $a_main_menus['op'];
            }

            if ($user_level >= Employee::LEVEL_MANAGER) {
                $a_main_menus_ok['data'] = $a_main_menus['data'];
                $a_main_menus_ok['report'] = $a_main_menus['report'];
            }

            if ($user_level == Employee::LEVEL_ADMIN) {
                $a_main_menus_ok['settings'] = $a_main_menus['settings'];
            }

        } catch (Exception $ex) {

        }
        $this->assign('a_menu_1', $a_main_menus_ok);


        /*各app自行初始化*/
        $this->_beforeAction();
    }

    /**
     * 检查设备是大还是小
     * @param string $number
     * @return  string
     */
    protected function isDeviceXorD($number)
    {
        $row_mid = substr($number, 2, 2);
        $i_row2 = intval($row_mid);
        if ($i_row2 <= 9) {
            return "X";
        } else {
            return "D";
        }
    }

    protected $_workbenchXD;
    protected $_stackerXD;
    protected $_conveyorXD;

    protected $_workbenchList;
    protected $_stackerList;
    protected $_conveyorList;


    /**
     * for local init
     * 优先级3
     */
    protected function _beforeAction()
    {
    }


    /**
     *  优先级4 为 action
     */

    /**
     * 优先级5
     */
    public function afterExecuteRoute($dispatcher)
    {


    }


    protected function _debug($will_die = false)
    {
        $this->view->disable();
        if ($will_die) {
            exit();
        }

    }


    public function notFoundAction()
    {
        // 发送一个HTTP 404 响应的header
        $this->response->setStatusCode(404, "Not Found");
    }


    protected function fetch($key)
    {
        if ($this->request->hasPost($key)) {
            return $this->request->getPost($key);
        }
        return $this->request->get($key);
    }


    protected function assign($key, $val)
    {
        $this->view->setVar($key, $val);
        $this->_pool[$key] = $val;
    }


    public function __destruct()
    {
        if (null != $this->logger) {
            /*保存消息到文件中*/
            $this->logger->commit();
        }
    }


    /**
     * 改进的ajax输出
     *
     * @param string $code
     * @param array $a_data
     * @param array $other_root_data
     */
    protected function _ajax($code, $a_data = array(), $other_root_data = array())
    {


        $this->view->disable();
        $a_result = array(
            'code' => $code,
            'data' => $a_data
        );
        if ($other_root_data && is_array($other_root_data) && count($other_root_data) > 0) {
            foreach ($other_root_data as $k => $v) {
                if ($k == 'code' || $k == 'data') {
                    continue;
                }
                $a_result[$k] = $v;
            }
        }
        echo json_encode($a_result);
        exit();
    }


    /**
     * 渲染分页
     *
     * @param string $url
     * @param int $i_total
     * @param int $i_page
     * @param int $i_size
     *
     * @return void
     */
    protected function _pager($url, $i_total, $i_page, $i_size = 20)
    {

        $o_pager = new PagerHelper($url, $i_total, $i_size, $i_page);

        $pager_html = $o_pager->render();

        $this->assign('pager_html', $pager_html);
    }

    /**
     * 预先处理一些公共表单信息
     *
     * @param $form_scope
     * @return array
     */
    protected function _beforeFormEdit($form_scope)
    {
        /**
         * 启用CSRF预防
         */
        $this->touchFormToken($form_scope);

        //通过这个可以去获取内存的数据
        $s_last_form_id = $this->get("last_form_id", "string", "");

        //TODO 获取上一个编辑的错误
        //a_last_form_error 是一个数组
        $this->assign('a_last_form_error', $aaa);

        //TODO 获取上一个编辑的最后输入的数据，回填，如果是edit

        $this->assign('form_save_result', $this->fetch('form_save_result'));
        $this->assign('form_save_error', urldecode($this->fetch('form_save_error')));

        //TODO 输出最后的错误信息，返回已经输入的信息

        return array();
    }

    /**
     * 预先处理一些公共表单信息
     * @param string $form_scope
     * @return boolean
     */
    protected function _beforeFormSave($form_scope)
    {

        $this->view->disable();

        _php_comment("", 2);

        $this->assign('form_save_result', $this->fetch('form_save_result'));
        $this->assign('form_save_error', urldecode($this->fetch('form_save_error')));

        if (!$this->request->isPost()) {
            // 非post提交，不响应
            return false;
        }

        if (!$this->checkFormToken($form_scope)) {
            // 检测CSRF预防
            return false;
        }


    }

    /**
     * 后置处理一些公共表单保存
     *
     * @param array $a_param
     * @param string $s_error
     *
     * @return array
     */
    protected function _afterFormSave(&$a_param, $s_error)
    {
        //TODO 通过sessin 传递错误和最后一次失败的数据
        if ($s_error) {
            $a_param ['form_save_result'] = FORM_SAVE_FAIL;
            $a_param ['form_save_error'] = $s_error;
        } else {
            $a_param ['form_save_result'] = FORM_SAVE_SUCC;
        }
        return $a_param;
    }

    /**
     * 下载.，不要超过26栏目
     *
     * @param array $a_data
     * @param string $file_name
     * @param string $creator
     * @param string $subject
     * @param string $description
     * @param string $keywords
     * @param string $category
     *
     * @return PHPExcel
     */
    protected function _excelDownload($a_data, $file_name, $creator = 'jingshan', $subject = '',
                                      $description = '', $keywords = '', $category = '')
    {

        /* Create new PHPExcel object*/
        $objPHPExcel = new PHPExcel();

        /* Set document properties */
        $objPHPExcel->getProperties()
            ->setCreator($creator)
            ->setLastModifiedBy($creator)
            ->setTitle($file_name)
            ->setSubject($subject)
            ->setDescription($description)
            ->setKeywords($keywords)
            ->setCategory($category);

        $objPHPExcel->removeSheetByIndex(0);
        $a_letters = range('A', 'Z');

        foreach ($a_data as $i_sheet => $a_sheet) {

            $ii = 1;
            $s_name = $a_sheet['sheet_title'];
            $a_row_list = $a_sheet['sheet_data'];

            $oWorkSheet = new PHPExcel_Worksheet($objPHPExcel, $s_name); //创建一个工作表
            // $objPHPExcel->setActiveSheetIndex($i_sheet); //切换到新创建的工作表
            foreach ($a_row_list as $a_line) {
                $jj = 0;
                foreach ($a_line as $v) {
                    $row_name = $a_letters[$jj] . $ii;
                    $oWorkSheet->setCellValue($row_name, $v);
                    $jj++;
                }
                $ii++;
            }
            $objPHPExcel->addSheet($oWorkSheet); //插入工作表
        }

        /* Set active sheet index to the first sheet, so Excel opens this as the first sheet*/
        $objPHPExcel->setActiveSheetIndex(0);

        /* Redirect output to a client’s web browser (Excel2007)*/
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        /* If you're serving to IE 9, then the following may be needed*/
        header('Cache-Control: max-age=1');

        /*  If you're serving to IE over SSL, then the following may be needed*/
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 修正excel读取时间的bug
     * @param $date
     * @param bool $time
     * @return array|int|string
     */
    protected function _excelTime($date, $time = false)
    {
        if (function_exists('GregorianToJD')) {
            if (is_numeric($date)) {
                $jd = GregorianToJD(1, 1, 1970);
                $gregorian = JDToGregorian($jd + intval($date) - 25569);
                $date = explode('/', $gregorian);
                $date_str = str_pad($date [2], 4, '0', STR_PAD_LEFT)
                    . "-" . str_pad($date [0], 2, '0', STR_PAD_LEFT)
                    . "-" . str_pad($date [1], 2, '0', STR_PAD_LEFT)
                    . ($time ? " 00:00:00" : '');
                return $date_str;
            }
        } else {
            $date = $date > 25568 ? $date + 1 : 25569;
            /*There was a bug if Converting date before 1-1-1970 (tstamp 0)*/
            $ofs = (70 * 365 + 17 + 2) * 86400;
            $date = date("Y-m-d", ($date * 86400) - $ofs) . ($time ? " 00:00:00" : '');
        }
        return $date;
    }


    /**
     * cookie 有效期 一天
     *
     * @var int
     */
    protected $_expire = 86400;

    /**
     * 授权key
     *
     * @var int
     */
    protected $_token_key = '__token_js';

    /**
     * 登录，中加密cookie
     *
     * @param array $user_info
     */
    protected function _login($user_info)
    {
        $i_expire = NOW + $this->_expire;
        $user_info['expire'] = $i_expire;
        $value = base64_encode(EncryptHelper::encrypt(serialize($user_info)));
        $this->_visitor->init($user_info);
        $this->cookies->set($this->_token_key, $value, $i_expire);

    }

    /**
     * 登出，中加密cookie
     *
     */
    protected function _logout()
    {
        $this->cookies->set($this->_token_key, "", time() - 3600);
        $this->cookies->delete($this->_token_key);
    }


    /**
     * 检测登录
     *
     * @param $s_token string
     *
     * @return boolean UsersModel
     */
    protected function _checkLogin($s_token = '')
    {
        $key = $this->_token_key;
        if (empty ($s_token)) {
            $s_token = $this->cookies->get($key)->getValue();
            if (false == $s_token) {
                return false;
            }
        }
        if (false != $s_token) {
            $a_user_info = unserialize(EncryptHelper::decrypt(base64_decode($s_token)));
            if (isset ($a_user_info ['expire']) && (NOW < (0 + $a_user_info ['expire']))) {
                $this->_login($a_user_info);
                return true;
            }
        }


        return false;
    }

    /**
     * 强制登录
     */
    protected function _mustLogin()
    {

        $ctl = $this->_pool['route_controller_name'];
        $act = $this->_pool['route_action_name'];
        if ('ajax_' == substr($act, 0, 5)) {
            $is_ajax = true;
        } else {
            $is_ajax = false;
        }
        $token = $this->fetch('token');
        if (false == $this->_checkLogin($token)) {
            //没有login

            $a_no_need_auth = array('index', 'default', 'test', 'api');

            if (!in_array($ctl, $a_no_need_auth)) {
                if ($is_ajax) {
                    $a_result = array(
                        'code' => 'no_login'
                    );
                    echo json_encode($a_result);
                    exit();
                } else {
                    $this->view->disable();
                    return $this->response->redirect($this->_pool['url_index']);


                    exit();
                }
            }
        } else {
            $a_visitor_info = $this->_visitor->getBasicInfo();
            $this->assign('g_visitor_info', $a_visitor_info);
        }


        return true;
    }

    /**
     * 设定菜单
     *
     * @param string $menu_0
     * @param bool|string $menu_1
     *
     */
    protected function _menu($menu_0, $menu_1 = '')
    {

        if ('' == $menu_0) {
            $menu_0 = $this->_pool['route_controller_name'];
        }

        if ('' == $menu_1) {
            $menu_0 = $this->_pool['route_action_name'];
        }


        $this->assign('curr_menu_0', $menu_0);
        $this->assign('curr_menu_1', $menu_1);


        $a_menu = $this->_pool['a_menu_1'];
        $curr_menu_icon_0 = "";
        $curr_menu_link_0 = "";
        $curr_menu_name_0 = "";

        $curr_menu_name_1 = "";
        $curr_menu_icon_1 = "";
        if ($menu_0 != '') {
            $curr_menu_name_0 = $a_menu[$menu_0]['name'];
            $curr_menu_icon_0 = $a_menu[$menu_0]['icon'];
            $curr_menu_link_0 = $a_menu[$menu_0]['link'];

            $curr_menu_icon_1 = $curr_menu_icon_0;
            if ($menu_1 != '') {
                $curr_menu_name_1 = $a_menu[$menu_0]['sub_menu'][$menu_1]['name'];
                $curr_menu_icon_1 = $a_menu[$menu_0]['sub_menu'][$menu_1]['icon'];
            }
        }

        $this->assign('curr_menu_name_0', $curr_menu_name_0);
        $this->assign('curr_menu_icon_0', $curr_menu_icon_0);
        $this->assign('curr_menu_link_0', $curr_menu_link_0);
        $this->assign('curr_menu_name_1', $curr_menu_name_1);
        $this->assign('curr_menu_icon_1', $curr_menu_icon_1);

    }

    /**
     * 统一错误处理
     *
     * @param string $message
     */
    protected function _error($message)
    {
        $a_param = array(
            'msg' => $message
        );
        $s_url = $this->url->get('index/error', $a_param);

        $this->view->disable();

        $this->response->redirect($s_url);


        exit();
        return;

    }

    /**
     * 退后
     *
     */
    protected function _goBack($msg)
    {
        echo '<script type="text/javascript">';
        echo "alert('{$msg}');";
        echo 'history.go(-1);';
        echo '</script>';
    }

    /**
     * 退后
     *
     */
    protected function _reloadParent($url = "")
    {

        echo '<script type="text/javascript">';
        if (!$url) {
            echo 'parent.location.reload()';
        } else {
            echo "parent.location.href='{$url}';";
        }
        echo '</script>';
    }

    /**
     * 处理一般的开始和结束日期
     *
     * @return array
     */
    protected function _listDateFromTo()
    {
        $date_from = $this->request->get('date_from');
        $date_to = $this->request->get('date_to');


        if (!$date_from) {
            $date_from = date('Y-m-d', NOW - (30 * 86400));
        }


        if (!$date_to) {
            $date_to = date('Y-m-d', NOW);
        }

        if ($date_from > $date_to) {
            $date_temp = $date_from;
            $date_from = $date_to;
            $date_to = $date_temp;
        }
        $this->assign('selected_date_from', $date_from);
        $this->assign('selected_date_to', $date_to);
        return array(
            $date_from,
            $date_to
        );
    }

    /**
     * 处理分页
     *
     * @param int $default_size
     *
     * @return array
     */
    protected function _listPageAndSize($default_size = 10)
    {
        $page = $this->fetch('page', 1);
        $page_size = $this->fetch('page_size', $default_size);
        if ($page_size < 0 || $page_size > 9999) {
            $page_size = $default_size;
        }

        $this->assign('selected_page', $page);
        $this->assign('selected_page_size', $page_size);

        return array(
            $page,
            $page_size
        );
    }

    /**
     * 一般数组遍历来分页
     *
     * @param array $a_array
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    protected function _arrayPager($a_array, $page, $page_size)
    {
        $page = max(1, abs($page));
        $length = max(1, abs($page_size));
        $offset = ($page - 1) * $length;
        return array_slice($a_array, $offset, $length);
    }

    /**
     * @param $id
     * @return bool
     */
    protected function isPalletId($id)
    {
        if ((strpos($id, "P") === 0) && strlen($id) == 8) {
            return true;
        }
        return false;
    }

    /**
     * 是否大托盘
     * @param $id
     * @return bool
     */
    protected function isBigPalletId($id)
    {
        if ((strpos($id, "PD") === 0) && strlen($id) == 8) {
            return true;
        }
        return false;
    }


    /**
     * 是否小托盘
     * @param $id
     * @return bool
     */
    protected function isSmallPalletId($id)
    {
        if ((strpos($id, "PX") === 0) && strlen($id) == 8) {
            return true;
        }
        return false;
    }


    /**
     * 8位数字
     *
     * @param id
     * @return bool
     */
    public function isItemId($id)
    {
        if (strlen($id) > 1) {
            return true;
        }
        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    protected function isWorkbenchId($id)
    {
        if ((strpos($id, "A") === 0) && strlen($id) == 6) {
            return true;
        }
        return false;
    }

    /**
     * 是否堆垛机ID
     * @param $id
     * @return bool
     */
    protected function isStacketId($id)
    {
        if ((strpos($id, "A") === 0) && strlen($id) == 6) {
            return true;
        }
        return false;
    }


    /**
     * @param $id
     * @return bool
     */
    protected function isShelfId($id)
    {
        if ((strpos($id, "A") === 0) && strlen($id) == 8) {
            return true;
        }
        return false;
    }

    function makeItemBarcode($number)
    {
        //构建画布
        $t1 = "/public/temp/item_1d_t_" . $number . ".jpg";
        $t2 = "/public/temp/item_1d_" . $number . ".jpg";

        $target = APP_PATH . $t1;
        $target_fix = APP_PATH . $t2;

        $item_info = Item::getDetailByNumber($number);
        $a_brand_list = $this->getPool('a_brand_list_kv');
        $a_unit_list = $this->getPool('a_unit_list_kv');
        $name = $item_info['name'];
        $brand_name = $a_brand_list[$item_info['brand_id']];
        $unit_name = $a_unit_list[$item_info['unit_id']];

        $this->_barcode($target, $number, 300);

        $canvas = new Imagick();
        $canvas->newimage(1000, 600, 'white');
        $canvas->setimageformat("jpg");

        $draw = new ImagickDraw();
        $draw->setFont(APP_PATH . "/public/fonts/simhei.ttf");
        $draw->setFontSize(60);//设置字体大小
        $draw->setTextUnderColor(new ImagickPixel ('white'));//设置背景色
        $draw->setFillColor(new ImagickPixel('black'));//设置字体颜色
        $draw->setGravity(Imagick::GRAVITY_NORTHWEST);//设置水印位置
//$draw->setFillAlpha(0.5);
        $waterText1 = iconv("UTF-8", "UTF-8//IGNORE", "名称:" . $name);
        $draw->annotation(60, 60, $waterText1);
        $canvas->drawImage($draw);
        $draw->setFontSize(60);

        $waterText3 = iconv("UTF-8", "UTF-8//IGNORE", "型号:" . $brand_name);
        $draw->annotation(60, 120, $waterText3);
        $canvas->drawImage($draw);

        $waterText2 = iconv("UTF-8", "UTF-8//IGNORE", "单位:" . $unit_name);
        $draw->annotation(450, 120, $waterText2);
        $canvas->drawImage($draw);


        $pic = new Imagick();
        $pic->readImage($target);
        // $pic->scaleImage(200, 150);

        // 将图片合并到画布
        $canvas->compositeImage($pic, Imagick::COMPOSITE_OVER, 20, 180);
        $draw->setFontSize(100);//设置字体大小
        $draw->annotation(200, 500, $number);
        $canvas->drawImage($draw);

        $canvas->rotateImage(new ImagickPixel(), 90);

        $canvas->writeimage($target_fix);

        //销毁对象
        $canvas->destroy();
        return $t2;
    }

    /**
     * 批次
     * @param $batch_id
     */
    function makeItemBatchBarcode($batch_id)
    {
        $a_batch_info = Item::getBatchDetail($batch_id);

        //构建画布
        $t1 = "/public/temp/item_1d_batch_t_" . $batch_id . ".jpg";
        $t2 = "/public/temp/item_1d_batch_" . $batch_id . ".jpg";

        $target = APP_PATH . $t1;
        $target_fix = APP_PATH . $t2;

        //$item_info = Item::getDetailByNumber($a_batch_info['item_num']);

        $item_num = $a_batch_info['item_num'];
        $name = $a_batch_info['item_name'];

        $a_brand_list = $this->getPool('a_brand_list_kv');
        $a_unit_list = $this->getPool('a_unit_list_kv');

        $brand_name = $a_brand_list[$a_batch_info['brand_id']];
        //$unit_name = $a_unit_list[$item_info['unit_id']];

        $bbid = $this->packBatchId($batch_id);
        $this->_barcode($target, $bbid, 1000, $orientation = "horizontal",
            $code_type = "code128", $print = false, $SizeFactor = 7);

        $canvas = new Imagick();
        $canvas->newimage(1200, 620, 'white');
        $canvas->setimageformat("jpg");

        $draw = new ImagickDraw();
        $draw->setFont(APP_PATH . "/public/fonts/simhei.ttf");
        $draw->setFontSize(60);//设置字体大小
        $draw->setTextUnderColor(new ImagickPixel ('white'));//设置背景色
        $draw->setFillColor(new ImagickPixel('black'));//设置字体颜色
        $draw->setGravity(Imagick::GRAVITY_NORTHWEST);//设置水印位置
        //$draw->setFillAlpha(0.5);

        $waterText0 = iconv("UTF-8", "UTF-8//IGNORE", $item_num);
        $draw->annotation(10, 1, $waterText0);
        $canvas->drawImage($draw);


        $waterText1 = iconv("UTF-8", "UTF-8//IGNORE", $name);
        $draw->annotation(10, 60, $waterText1);
        $canvas->drawImage($draw);

        $draw->setFontSize(60);
        $waterText3 = iconv("UTF-8", "UTF-8//IGNORE", "品牌:" . $brand_name);
        $draw->annotation(10, 120, $waterText3);
        $canvas->drawImage($draw);

        //$waterText2 = iconv("UTF-8", "UTF-8//IGNORE", "单位:" . $unit_name);
        //$draw->annotation(450, 120, $waterText2);
        //$canvas->drawImage($draw);


        $pic = new Imagick();
        $pic->readImage($target);
        //$pic->scaleImage(1000, 500);

        // 将图片合并到画布
        $canvas->compositeImage($pic, Imagick::COMPOSITE_OVER, 0, 0);
        $draw->setFontSize(60);//设置字体大小
        $draw->annotation(10, 540, "计划号:" . $a_batch_info['batch_num']);
        $canvas->drawImage($draw);

        $canvas->rotateImage(new ImagickPixel(), 90);

        $canvas->writeimage($target_fix);

        //销毁对象
        $canvas->destroy();
        return $t2;
    }

    function makePalletBarcode($number)
    {
        //构建画布
        $t1 = "/public/temp/pallet_1d_t_" . $number . ".jpg";
        $t2 = "/public/temp/pallet_1d_" . $number . ".jpg";

        $target = APP_PATH . $t1;
        $target_fix = APP_PATH . $t2;


        $this->_barcode($target, $number, 400, "horizontal",
            "code128", false, 5);


        $canvas = new Imagick();
        $canvas->newimage(1000, 600, 'white');
        $canvas->setimageformat("jpg");

        $draw = new ImagickDraw();
        $draw->setFont(APP_PATH . "/public/fonts/simhei.ttf");
        $draw->setFontSize(60);//设置字体大小
        $draw->setTextUnderColor(new ImagickPixel ('white'));//设置背景色
        $draw->setFillColor(new ImagickPixel('black'));//设置字体颜色
        $draw->setGravity(Imagick::GRAVITY_NORTHWEST);//设置水印位置


        $pic = new Imagick();
        $pic->readImage($target);
        // $pic->scaleImage(200, 150);

        // 将图片合并到画布
        $canvas->compositeImage($pic, Imagick::COMPOSITE_OVER, 10, 100);
        $draw->setFontSize(100);//设置字体大小
        $draw->annotation(200, 500, $number);
        $canvas->drawImage($draw);

        $canvas->rotateImage(new ImagickPixel(), 90);

        $canvas->writeimage($target_fix);

        //销毁对象
        $canvas->destroy();
        return $t2;
    }


    protected function _barcode($filepath = "", $text = "0", $size = "20", $orientation = "horizontal",
                                $code_type = "code128", $print = false, $SizeFactor = 5)
    {
        $code_string = "";
        // Translate the $text into barcode the correct $code_type
        if (in_array(strtolower($code_type), array("code128", "code128b"))) {
            $chksum = 104;
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "\`" => "111422", "a" => "121124", "b" => "121421", "c" => "141122", "d" => "141221", "e" => "112214", "f" => "112412", "g" => "122114", "h" => "122411", "i" => "142112", "j" => "142211", "k" => "241211", "l" => "221114", "m" => "413111", "n" => "241112", "o" => "134111", "p" => "111242", "q" => "121142", "r" => "121241", "s" => "114212", "t" => "124112", "u" => "124211", "v" => "411212", "w" => "421112", "x" => "421211", "y" => "212141", "z" => "214121", "{" => "412121", "|" => "111143", "}" => "111341", "~" => "131141", "DEL" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "FNC 4" => "114131", "CODE A" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ($X = 1; $X <= strlen($text); $X++) {
                $activeKey = substr($text, ($X - 1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum = ($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211214" . $code_string . "2331112";
        } elseif (strtolower($code_type) == "code128a") {
            $chksum = 103;
            $text = strtoupper($text); // Code 128A doesn't support lower case
            // Must not change order of array elements as the checksum depends on the array's key to validate final code
            $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "NUL" => "111422", "SOH" => "121124", "STX" => "121421", "ETX" => "141122", "EOT" => "141221", "ENQ" => "112214", "ACK" => "112412", "BEL" => "122114", "BS" => "122411", "HT" => "142112", "LF" => "142211", "VT" => "241211", "FF" => "221114", "CR" => "413111", "SO" => "241112", "SI" => "134111", "DLE" => "111242", "DC1" => "121142", "DC2" => "121241", "DC3" => "114212", "DC4" => "124112", "NAK" => "124211", "SYN" => "411212", "ETB" => "421112", "CAN" => "421211", "EM" => "212141", "SUB" => "214121", "ESC" => "412121", "FS" => "111143", "GS" => "111341", "RS" => "131141", "US" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "CODE B" => "114131", "FNC 4" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
            $code_keys = array_keys($code_array);
            $code_values = array_flip($code_keys);
            for ($X = 1; $X <= strlen($text); $X++) {
                $activeKey = substr($text, ($X - 1), 1);
                $code_string .= $code_array[$activeKey];
                $chksum = ($chksum + ($code_values[$activeKey] * $X));
            }
            $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

            $code_string = "211412" . $code_string . "2331112";
        } elseif (strtolower($code_type) == "code39") {
            $code_array = array("0" => "111221211", "1" => "211211112", "2" => "112211112", "3" => "212211111", "4" => "111221112", "5" => "211221111", "6" => "112221111", "7" => "111211212", "8" => "211211211", "9" => "112211211", "A" => "211112112", "B" => "112112112", "C" => "212112111", "D" => "111122112", "E" => "211122111", "F" => "112122111", "G" => "111112212", "H" => "211112211", "I" => "112112211", "J" => "111122211", "K" => "211111122", "L" => "112111122", "M" => "212111121", "N" => "111121122", "O" => "211121121", "P" => "112121121", "Q" => "111111222", "R" => "211111221", "S" => "112111221", "T" => "111121221", "U" => "221111112", "V" => "122111112", "W" => "222111111", "X" => "121121112", "Y" => "221121111", "Z" => "122121111", "-" => "121111212", "." => "221111211", " " => "122111211", "$" => "121212111", "/" => "121211121", "+" => "121112121", "%" => "111212121", "*" => "121121211");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ($X = 1; $X <= strlen($upper_text); $X++) {
                $code_string .= $code_array[substr($upper_text, ($X - 1), 1)] . "1";
            }

            $code_string = "1211212111" . $code_string . "121121211";
        } elseif (strtolower($code_type) == "code25") {
            $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
            $code_array2 = array("3-1-1-1-3", "1-3-1-1-3", "3-3-1-1-1", "1-1-3-1-3", "3-1-3-1-1", "1-3-3-1-1", "1-1-1-3-3", "3-1-1-3-1", "1-3-1-3-1", "1-1-3-3-1");

            for ($X = 1; $X <= strlen($text); $X++) {
                for ($Y = 0; $Y < count($code_array1); $Y++) {
                    if (substr($text, ($X - 1), 1) == $code_array1[$Y])
                        $temp[$X] = $code_array2[$Y];
                }
            }

            for ($X = 1; $X <= strlen($text); $X += 2) {
                if (isset($temp[$X]) && isset($temp[($X + 1)])) {
                    $temp1 = explode("-", $temp[$X]);
                    $temp2 = explode("-", $temp[($X + 1)]);
                    for ($Y = 0; $Y < count($temp1); $Y++)
                        $code_string .= $temp1[$Y] . $temp2[$Y];
                }
            }

            $code_string = "1111" . $code_string . "311";
        } elseif (strtolower($code_type) == "codabar") {
            $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "$", ":", "/", ".", "+", "A", "B", "C", "D");
            $code_array2 = array("1111221", "1112112", "2211111", "1121121", "2111121", "1211112", "1211211", "1221111", "2112111", "1111122", "1112211", "1122111", "2111212", "2121112", "2121211", "1121212", "1122121", "1212112", "1112122", "1112221");

            // Convert to uppercase
            $upper_text = strtoupper($text);

            for ($X = 1; $X <= strlen($upper_text); $X++) {
                for ($Y = 0; $Y < count($code_array1); $Y++) {
                    if (substr($upper_text, ($X - 1), 1) == $code_array1[$Y])
                        $code_string .= $code_array2[$Y] . "1";
                }
            }
            $code_string = "11221211" . $code_string . "1122121";
        }

        // Pad the edges of the barcode
        $code_length = 20;
        if ($print) {
            $text_height = 30;
        } else {
            $text_height = 0;
        }

        for ($i = 1; $i <= strlen($code_string); $i++) {
            $code_length = $code_length + (integer)(substr($code_string, ($i - 1), 1));
        }

        if (strtolower($orientation) == "horizontal") {
            $img_width = $code_length * $SizeFactor;
            $img_height = $size;
        } else {
            $img_width = $size;
            $img_height = $code_length * $SizeFactor;
        }

        $image = imagecreate($img_width, $img_height + $text_height);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefill($image, 0, 0, $white);
        if ($print) {
            imagestring($image, 5, 31, $img_height, $text, $black);
        }

        $location = 10;
        for ($position = 1; $position <= strlen($code_string); $position++) {
            $cur_size = $location + (substr($code_string, ($position - 1), 1));
            if (strtolower($orientation) == "horizontal")
                imagefilledrectangle($image, $location * $SizeFactor, 0, $cur_size * $SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black));
            else
                imagefilledrectangle($image, 0, $location * $SizeFactor, $img_width, $cur_size * $SizeFactor, ($position % 2 == 0 ? $white : $black));
            $location = $cur_size;
        }

        // Draw barcode to the screen or save in a file
        if ($filepath == "") {
            header('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
        } else {
            imagepng($image, $filepath);
            imagedestroy($image);
        }
    }


    /**
     * 通过台子寻找堆垛机
     * @param $shelf_num
     * @return array
     */
    public function getConveyorStackerByShelfNum($shelf_num)
    {
        $conveyor = "";

        $stacker = "";

        list($i_total1, $a_list1) = Stacker::getList();
        list($i_total2, $a_list2) = Conveyor::getList();

        $a_temp1 = array();
        foreach ($a_list1 as $vv) {
            $r1 = substr($vv['number'], 2, 2);
            $a_temp1[$r1] = $vv['number'];
        }

        $a_temp2 = array();
        foreach ($a_list2 as $vv) {
            $r1 = substr($vv['number'], 2, 2);
            $a_temp2[$r1] = $vv['number'];
        }

        $r2 = substr($shelf_num, 2, 2);

        switch ($r2) {
            case "01":
            case "02":
                $stacker = $a_temp1["02"];
                $conveyor = $a_temp2["03"];
                break;

            case "03":
            case "04":
                $stacker = $a_temp1["05"];
                $conveyor = $a_temp2["06"];
                break;


            case "05":
            case "06":
                $stacker = $a_temp1["08"];
                $conveyor = $a_temp2["09"];
                break;


            case "07":
            case "08":
                $stacker = $a_temp1["11"];
                $conveyor = $a_temp2["10"];
                break;


            case "09":
            case "10":
                $stacker = $a_temp1["14"];
                $conveyor = $a_temp2["13"];
                break;


            case "11":
            case "12":
                $stacker = $a_temp1["17"];
                $conveyor = $a_temp2["16"];
                break;

        }
        return array($conveyor, $stacker);
    }


    protected function packBatchId($batch_id)
    {
        $batch_id = (int)$batch_id;
        $dd = "{$batch_id}";
        $d = strlen($dd);
        $diff = 9 - $d;
        for ($i = 0; $i < $diff; $i++) {
            $dd = "0{$dd}";
        }
        return "B{$dd}";
    }

    protected function unPackBatchId($bbid)
    {
        $b = substr($bbid, 1);
        $c = (int)$b;
        return $c;
    }


    /**
     * 获取rest提交的json数据
     * @return mixed
     */
    public function _getRestPostData()
    {
        $sss = file_get_contents("php://input");
        $aaa = json_decode($sss, true);
        if (!$aaa) {
            return array();
        }
        //var_dump($aaa);
        return $aaa;
    }

    /**
     * 获取rest提交的数据
     * @return mixed
     */
    public function _getRestBinData()
    {
        $sss = file_get_contents("php://input");
        return $sss;
    }


    //排序
    protected function _multiSort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
    {

        if (is_array($arrays)) {
            foreach ($arrays as $array) {
                if (is_array($array)) {
                    $key_arrays[] = $array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
        return $arrays;
    }

    /**
     * 通过Imagick创建图片的缩略图
     *
     * @access  public
     * @param string $img 原始图片的路径
     * @param string $target 指定生成图片的目录名
     * @param int $thumb_width 缩略图宽度
     * @param int $thumb_height 缩略图高度
     */
    protected function makeThumb($img, $target, $thumb_width = 0, $thumb_height = 0)
    {
        $im = new Imagick();
        $new_im = clone $im;
        $im->readImage($img);
        $im->setCompression(Imagick::COMPRESSION_JPEG);
        $im->setCompressionQuality(100); // 设置图片品质
        $srcImage = $im->getImageGeometry(); //获取源图片宽和高
        //图片等比例缩放宽和高设置 ，根据宽度设置等比缩放
        $scale_org = $srcImage['width'] / $srcImage['height'];
        if ($srcImage['width'] / $thumb_width > $srcImage['height'] / $thumb_height) {
            $newX = $thumb_width;
            $newY = $thumb_width / $scale_org;
        } else {
            /* 原始图片比较高，则以高度为准 */
            $newX = $thumb_height * $scale_org;
            $newY = $thumb_height;
        }

        $im->thumbnailImage($newX, $newY);
        //按照比例进行缩放
        // 按照缩略图大小创建一个有颜色的图片
        $new_im->newImage($thumb_width, $thumb_height, "", 'png');
        //合并图片
        $new_im->compositeImage($im, Imagick::COMPOSITE_OVER, ($thumb_width - $newX) / 2, ($thumb_height - $newY) / 2);

        /* 创建当月目录 */
        //生成JPG图片;
        $new_im->writeImage($target);
        //清空图片内存
        $im->clear();
        $new_im->clear();
        $im->destroy();
        $new_im->destroy();
    }

    //TODO 统一判断当前登陆的人有没有权限操作当前资源

    /**
     * 生成CSRF预防
     * @param $form_name
     * @return  void
     */
    protected function touchFormToken($form_name)
    {

    }

    /**
     * 检测 CSRF预防
     * @param string $form_name
     * @param string $s_token
     * @return boolean
     */
    protected function checkFormToken($form_name)
    {


        $s_token

        return false;
    }

}
