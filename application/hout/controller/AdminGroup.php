<?php
namespace app\hout\controller;
class AdminGroup extends Base
{
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new \app\hout\model\AdminGroup();
    }

    public function index()
    {
        $title="管理员组管理";
        $where = [];
        input('name') && $where['name'] = ['like', '%' . trim(input('name')) . '%'];
        $data = $this->model->getList($where);
        return view('index', compact('title', 'data'));
    }

    public function edit($id = "")
    {
        $title = $id ? '修改管理员组' : '添加管理员组';
        $data = $this->model->getOne(['id' => $id]);
        $addons_dir = APP_PATH;
        $addons = glob($addons_dir.'*', GLOB_ONLYDIR);
        $menus = [];
        foreach($addons as $v){
            try{
                $cfg = include $v."/config.php";
                if($cfg['menu']){
                    $menus[] = $cfg['menu'];
                }
            }catch (\Exception $e) {

            }

        }
        return view('edit', compact('title', 'data', 'menus'));
    }

    public function save($id = '')
    {
        $data = input("post.");
        $data['rules'] = isset($data['rules']) ? implode(',',$data['rules']) : '';
        //halt($data);
        $where = [];
        $id && $where['id'] = $id;
        $this->model->addOrUp($where, $data);
        return redirect('index');
    }

    public function del($id='')
    {
        if ($id != 1) {
            $this->model->del(['id' => $id]);
        }
        return redirect('index');
    }

    public function status($status='',$id='')
    {
        $data['status'] = $status;
        $this->model->update($data, ['id' => ['in', $id]]);
        return redirect('index');
    }
}
