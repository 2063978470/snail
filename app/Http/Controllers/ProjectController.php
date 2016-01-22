<?php

namespace App\Http\Controllers;

use App\Hardware;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\SubProduct;
use App\Project;
use App\Client;
use App\Server;
use App\Module;
use App\Param;

use App\Clog;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects/index', [
            'projects'=> Project::all(),
        ]);
    }

    public function add(Request $request) {

        if (! \Session::get('user')->can('项目签约')) abort(401);

        $sub = SubProduct::find($request->input('product_id'));
        $client = Client::find($request->input('client_id'));

        $project = new Project();
        $project->product()->associate($sub);
        $project->client()->associate($client);

        $project->vip = (bool) ($request->input('vip') == 'on');
        $project->official = (bool) ($request->input('official') == 'on');

        $project->ref_no = $request->input('ref_no');
        $project->name = $request->input('name');
        $project->seller = $request->input('seller');
        $project->contact_user = $request->input('contact_user');

        $signed_time = $request->input('signed_time');

        if (!$signed_time) $signed_time = NULL;
        $project->signed_time = $signed_time;     // 签约时间


        $cancelled_time =  $request->input('cancelled_time');

        if (!$cancelled_time) $cancelled_time = NULL;
        $project->cancelled_time = $cancelled_time;   // 服务到期时间


        $project->description = $request->input('description');

        if ($project->save()) {

            foreach($sub->modules as $module) {
                $project->modules()->save($module);
            }

            foreach($sub->params as $param) {

                $project->params()->save($param, [
                    'value'=> $param->pivot->value,
                ]);
            }

            Clog::add($project, '签约项目');
            Clog::add($project->client, '签约项目', [
                $project->name,
            ]);

            return redirect(route('project.profile', ['id'=> $project->id]))
                ->with('message_content', '签约成功!')
                ->with('message_type', 'info');
        }
    }

    public function edit(Request $request) {

        if (! \Session::get('user')->can('项目信息管理')) abort(401);



        $project = Project::find($request->input('id'));
        $product = SubProduct::find($request->input('product_id'));

        $old_attributes = $project->attributesToArray();

        $project->ref_no = $request->input('ref_no');               // 项目编号
        $project->name = $request->input('name');                   // 项目名称

        $project->vip = (bool) ($request->input('vip') == 'on');
        $project->official = (bool) ($request->input('official') == 'on');

        $old_product_id = $project->product->id;
        $new_product_id = $product->id;

        $project->product()->associate($product);                   // 产品类型
        $project->contact_user = $request->input('contact_user');   // 联系人
        $project->contact_phone = $request->input('contact_phone'); // 联系电话
        $project->contact_email = $request->input('contact_email'); // 联系邮箱

        $project->login_url = $request->input('login_url');     //登录地址

        $signed_time = $request->input('signed_time');

        if (!$signed_time) $signed_time = NULL;
        $project->signed_time = $signed_time;     // 签约时间

        $cancelled_time =  $request->input('cancelled_time');

        if (!$cancelled_time) $cancelled_time = NULL;

        $project->cancelled_time = $cancelled_time;   // 服务到期时间

        $project->seller = $request->input('seller');               // 销售人员
        $project->engineer = $request->input('engineer');           // 工程师
        $project->description = $request->input('description'); //
        $project->deploy_address = $request->input('deploy_address');
        $project->way = $request->input('way');

        if ($project->save()) {

            //修改了签约类型
            if ($old_product_id != $new_product_id) {

                //清空所有的 module, 重新关联 module
                $connected_modules = $project->modules()->lists('id')->all();

                if (count($connected_modules)) $project->modules()->detach($connected_modules);

                foreach($product->modules as $module) {
                    $project->modules()->save($module);
                }

                //清空所有的 params, 重新关联 params
                $connected_params = $project->params()->lists('id')->all();

                if (count($connected_params)) $project->params()->detach($connected_params);

                foreach($product->params as $param) {
                    $project->params()->save($param, [
                        'value'=> $param->pivot->value,
                    ]);
                }
            }

            $new_attributes = $project->attributesToArray();

            $change = [];
            $helper = [
                'ref_no'=> '项目编号',
                'name'=> '项目名称',
                'product_id'=> '产品类型(编号显示)',
                'contact_user'=> '联系人',
                'contact_phone' => '联系人电话',
                'contact_email'=> '联系人邮箱',
                'engineer'=> '工程师负责人',
                'deploy_address'=> '客户地址',
                'seller'=> '销售负责人',
                'description'=> '备注',
                'way'=> '乘车路线',
                'signed_time'=> '签约时间',
                'cancelled_time'=> '合同到期时间',
                'vip'=> '重点项目状态(1 为重点项目, 空为普通项目)',
                'official'=> '签约状态(1 为正式, 空为试用)',
                'login_url'=> '登录地址',
            ];

            foreach(array_diff_assoc($old_attributes, $new_attributes) as $key => $value) {
                $change[$key] = [
                    'old'=> $old_attributes[$key],
                    'new'=> $new_attributes[$key],
                    'title'=> $helper[$key],
                ];
            }

            if (count($change)) Clog::add($project, '修改基本信息', $change);

            return redirect(route('project.profile', ['id'=> $project->id]))
                ->with('message_content', '修改成功!')
                ->with('message_type', 'info');
        }
    }

    public function profile($id) {

        if (! \Session::get('user')->can('项目查看')) abort(401);

        $project = Project::find($id);

        return view('/projects/profile', [
            'project'=> $project,
        ]);
    }

    public function delete($id) {

        if (! \Session::get('user')->can('项目信息管理')) abort(401);

        $project = Project::find($id);

        Clog::add($project, '解约项目');

        $project->delete();
        return redirect(route('projects'))
            ->with('message_content', '已解约该项目!')
            ->with('message_type', 'danger');
    }

    public function servers($id, Request $request) {

        if (!\Session::get('user')->can('项目服务器管理')) abort(401);

        $project = Project::find($id);

        $server = Server::find($request->input('server_id'));

        $deploy_time = $request->input('deploy_time');
        if (!$deploy_time) $deploy_time = NULL;

        if (! $project->servers()->find($request->input('server_id'))) {
            $project->servers()->save($server, [
                'deploy_time'=> $deploy_time,
            ]);

            Clog::add($project, '关联服务器', [
                $server->name,
            ]);

            return redirect(route('project.profile', ['id'=> $project->id]))
                ->with('message_content', '关联成功!')
                ->with('message_type', 'info')
                ->with('tab', 'servers');
        }

        return redirect(route('project.profile', ['id'=> $project->id]))
            ->with('message_content', '已关联服务器, 无法再次关联')
            ->with('message_type', 'danger')
            ->with('tab', 'servers');
    }

    public function server_disconnect($id, $server_id, Request $request) {

        if (!\Session::get('user')->can('项目服务器管理')) abort(401);

        $project = Project::find($id);
        $server = Server::find($server_id);

        if ($project->servers()->find($server->id)) {
            $project->servers()->detach($server);

            Clog::add($project, '解除关联服务器', [
                $server->name,
            ]);

            return redirect()->to(route('project.profile', ['id'=> $project->id]))
                ->with('message_content', '解除关联成功')
                ->with('message_type', 'info')
                ->with('tab', 'servers');
        }
    }

    public function server_edit($id, Request $request) {

        if (!\Session::get('user')->can('项目服务器管理')) abort(401);

        $project = Project::find($id);
        $server = Server::find($request->input('server_id'));

        if ($project->servers()->find($server->id)) {

            $deploy_time = $request->input('deploy_time');
            if (!$deploy_time) $deploy_time = NULL;

            $old_deploy_time = $project
                ->servers()
                ->where('server_id', $server->id)
                ->first()
                ->pivot
                ->deploy_time;

            $project->servers()->updateExistingPivot($server->id, [
                'deploy_time'=> $deploy_time,
            ]);

            Clog::add($project, '修改服务器部署时间', [
                [
                    'old'=> (new \DateTime($old_deploy_time))->format('Y/m/d'),
                    'new'=> (new \DateTime($deploy_time))->format('Y/m/d'),
                    'title'=> '部署时间',
                ],
            ]);


            return redirect()->to(route('project.profile', ['id'=> $project->id]))
                ->with('message_content', '修改成功')
                ->with('message_type', 'info')
                ->with('tab', 'servers');
        }
    }


    public function modules($id, Request $request) {

        if (!\Session::get('user')->can('项目模块管理')) abort(401);

        $project = Project::find($id);

        $connected_modules = $project->modules()->where(
                'project_id',
                $project->id
            )->lists('id')->all();

        if (count($connected_modules)) {
            $project->modules()->detach($connected_modules);
        }

        $new_modules = $request->input('modules', []);

        //重新对选定的 module 进行 link, 类型为 type
        foreach($new_modules as $module_id) {

            $module = Module::find($module_id);

            $project->modules()->save($module);
        }

        $d1 = array_diff($new_modules, $connected_modules);
        $d2 = array_diff($connected_modules, $new_modules);

        if (count($d1)) {
            //新加的模块
            Clog::add($project, '添加模块', [
                join(',', \App\Module::whereIn('id', $d1)->lists('name')->all()),
            ]);
        }

        if (count($d2)) {
            //删除的模块
            Clog::add($project, '删除模块', [
                join(',', \App\Module::whereIn('id', $d2)->lists('name')->all()),
            ]);
        }

        return redirect()->back()
            ->with('message_content', '模块设置成功!')
            ->with('message_type', 'info')
            ->with('tab', 'softwares');
    }

    public function param_edit($id, Request $request) {

        if (!\Session::get('user')->can('项目参数管理')) abort(401);

        $param_id = $request->input('param_id');

        $param = Param::find($param_id);

        $project = Project::find($id);

        $old_vaule = $project->params()->where('param_id', $param->id)->first()->pivot->value;

        //如果设定了需要重置,
        if ($request->input('reset') == 'on') {

            $value = $project->product->params()->where('param_id', $param->id)->first()->pivot->value;

            $project->params()->detach($param_id);

            $project->params()->save($param, [
                'value'=> $value,
            ]);

            Clog::add($project, '重置参数', [
                [
                    'old'=> $old_vaule,
                    'new'=> $value,
                    'title'=> $param->name,
                ]
            ]);

        } else {

            $project->params()->detach($param_id);

            $project->params()->save($param, [
                'value' => $request->input('value'),
                'manual'=> true,
            ]);

            Clog::add($project, '更新参数', [
                [
                    'old'=> $old_vaule,
                    'new'=> $request->input('value'),
                    'title'=> $param->name,
                ]
            ]);
        }

        return redirect()->back()
            ->with('message_content', '参数修改成功!')
            ->with('message_type', 'info')
            ->with('tab', 'softwares');
    }

    public function hardwares($id, Request $request) {

        if (!\Session::get('user')->can('项目硬件管理')) abort(401);

        $project = Project::find($id);

        $data = $project->hardwares()->lists('id')->all();

        //$data 为已关联的

        $hardwares = $request->input('hardwares');

        //拆分算法如下

        //1. 获取 $data 和 $params 的交集
        //2. 获取 $data 和 1.中交集的差集
        //3. 对差集进行 detach 即可
        //4. 获取 $param 和 1.中交集的差集, 进行 save


        //1. 获取 $data 和 $params 的交集
        $intersect = array_intersect($data, (array) $hardwares);


        //2. 获取 $data 和 1.中交集的差集

        $detach = array_diff($data, $intersect);

        //3. detach
        if (count($detach)) {
            $project->hardwares()->detach($detach);

            Clog::add($project, '取消关联硬件', [
                join(',', \App\Hardware::whereIn('id', $detach)->lists('name')->all()),
            ]);
        }

        //4. 获取 $param 和 1.中交集的差集, 进行 save
        $save = array_diff((array) $hardwares, $intersect);

        if (count($save)) {
            $hsn = [];
            foreach($save as $hardware_id) {

                $hardware = Hardware::find($hardware_id);

                $project->hardwares()->save($hardware);
                $hsn[] = $hardware->name;

            }

            Clog::add($project, '关联硬件', [
                join(',', $hsn),
            ]);
        }

        return redirect()->back()
            ->with('message_content', '硬件设置成功!')
            ->with('message_type', 'info')
            ->with('tab', 'hardwares');
    }


    public function hardware_edit($id, Request $request) {

        if (!\Session::get('user')->can('项目硬件管理')) abort(401);

        $hardware_id = $request->input('hardware_id');

        $hardware = Hardware::find($hardware_id);

        $project = Project::find($id);

        $h = $project->hardwares()->where('hardware_id', $hardware_id)->first();

        $old = [
            'description'=> $h->pivot->description,
            'deployed_count'=> $h->pivot->deployed_count,
            'plan_count'=> $h->pivot->plan_count,
        ];

        $new = [
            'description'=> $request->input('description'),
            'deployed_count'=> $request->input('deployed_count'),
            'plan_count'=> $request->input('plan_count'),
        ];

        $project->hardwares()->detach($hardware_id);

        $project->hardwares()->save($hardware, $new);

        $change = [];

        $diff_helper = [
            'description' => '描述',
            'deployed_count'=> '部署数量',
            'plan_count'=> '签约数量',
        ];

        foreach(array_keys($diff_helper) as $item) {
            if ($old[$item] != $new[$item]) {
                $change[] = [
                    'old'=> $old[$item],
                    'new'=> $new[$item],
                    'title'=> $diff_helper[$item],
                ];
            }
        }

        Clog::add($project, '关联硬件基本信息修改', $change);

        return redirect()->back()
            ->with('message_content', '硬件修改成功!')
            ->with('message_type', 'info')
            ->with('tab', 'hardwares');
    }

    //profile 信息 start
    public function profile_item($id, Request $request) {

        if (! \Session::get('user')->can('项目查看')) abort(401);

        $project = Project::find($id);
        $type = $request->input('type');

        $method = strtr('_profile_{type}', ['{type}'=> $type]);

        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], $project);
        }
    }

    private function _profile_comments($project) {
        return view('projects/profile/comments', ['project'=> $project]);
    }

    private function _profile_servers($project) {
        return view('projects/profile/servers', ['project'=> $project]);
    }

    private function _profile_hardwares($project) {
        return view('projects/profile/hardwares', ['project'=> $project]);
    }

    private function _profile_softwares($project) {
        return view('projects/profile/softwares', ['project'=> $project]);
    }

    private function _profile_informations($project) {
        return view('projects/profile/informations', ['project'=> $project]);
    }

    private function _profile_trello($project) {
        return view('projects/profile/trello', ['project'=> $project]);
    }
    //profile 信息 end

}