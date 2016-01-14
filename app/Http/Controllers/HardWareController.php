<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Hardware;

class HardWareController extends Controller
{

    public function index() {
        return view('hardwares/index', ['hardwares'=> Hardware::all()]);

    }
    public function add(Request $request) {

        $hardware = new Hardware();
        $hardware->name = $request->input('name');
        $hardware->description = $request->input('description');
        $hardware->model = $request->input('model');
        $hardware->self_produce = (bool) ($request->input('self_produce') == 'on');

        $hardware->save();

        return redirect()->back()
            ->with('message_content', '添加成功!')
            ->with('message_type', 'info');
    }

    public function delete($id)
    {
        //
        $hardware = Hardware::find($id);

        $hardware->delete();

        return redirect()->back()
            ->with('message_content', '删除成功!')
            ->with('message_type', 'info');
    }

    public function edit(Request $request) {

        $hardware = Hardware::find($request->input('id'));

        $hardware->name = $request->input('name');
        $hardware->description = $request->input('description');
        $hardware->model = $request->input('model');
        $hardware->self_produce = (bool) ($request->input('self_produce') == 'on');

        $hardware->save();

        return redirect()->back()
            ->with('message_content', '修改成功!')
            ->with('message_type', 'info');
    }
}
