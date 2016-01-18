@extends('layout')

@section('content')
    <link rel="stylesheet" href="assets/css/projects/profile.css">
    <script type="text/javascript">
        require(['jquery', 'bootstrap-datetimepicker'], function($) {

            $('.datetimepicker').datetimepicker({
                format: 'YYYY/MM/DD'
            });

            require(['css!../css/bootstrap-datetimepicker.min'], function() {});

        });
    </script>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">{{ $project->name }}</h1>
        </div>
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-user"> 基本信息</i>

                    @if (\Session::get('user')->can('项目信息管理'))
                        <span class="pull-right">
                            <a href="#" data-toggle="modal" data-target="#edit-project">
                                <i class="fa fa-fw fa-edit"></i>
                            </a>

                            <a href="{{ route('project.delete', ['id'=> $project->id]) }}">
                                <i class="fa fa-fw fa-times"></i>
                            </a>
                            <div class="clearfix"></div>

                            <div class="modal fade" id="edit-project" tabindex="-1" role="dialog" aria-labelledby="edit-project-modal-label">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="edit-server-modal-label">修改客户信息</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="edit-client-form" class="form-horizontal" method="post" action="{{ route('project.edit') }}">
                                                <input type="hidden" name="id" value="{{ $project->id }}">
                                                @include('projects/full_form', ['project'=> $project])
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                            <button type="submit" class="btn btn-primary" form="edit-client-form">修改</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </span>
                    @endif
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover">
                        <tr>
                            <td class="col-lg-2">客户名称</td>
                            <td><a href="{{ route('client.profile', ['id'=> $project->client->id]) }}">{{ $project->client->name }}</a></td>
                        </tr>
                        <tr>
                            <td>项目名称</td>
                            <td>
                                {{ $project->name }}

                                @if ($project->vip)
                                    <span class="label label-danger">重点项目</span>
                                @endif

                                @if ($project->official)
                                    <span class="label label-info">正式</span>
                                @else
                                    <span class="label label-default">试用</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>项目编号</td>
                            <td>{{ $project->ref_no }}</td>
                        </tr>
                        <tr>
                            <td>产品/产品类型</td>
                            <td><a href="{{ route('subproduct.profile', ['id'=> $project->product->id]) }}">{{ $project->product->name }} ({{ $project->product->product->name }})</a></td>
                        </tr>
                        <tr>
                            <td>签约时间</td>
                            <td>{{ $project->signed_time or '未设定' }}</td>
                        </tr>
                        <tr>
                            <td>合同到期时间</td>
                            <td>{{ $project->cancelled_time or '未设定' }}</td>
                        </tr>
                        <tr>
                            <td>联系人</td>
                            <td>{{ $project->contact_user }}</td>
                        </tr>
                        <tr>
                            <td>联系电话</td>
                            <td>{{ $project->contact_phone }}</td>
                        </tr>
                        <tr>
                            <td>联系邮箱</td>
                            <td>{{ $project->contact_email }}</td>
                        </tr>
                        <tr>
                            <td>销售负责人</td>
                            <td>{{ $project->seller }}</td>
                        </tr>
                        <tr>
                            <td>工程师负责人</td>
                            <td>{{  $project->engineer }}</td>
                        </tr>
                        <tr>
                            <td>部署地址</td>
                            <td>{{ $project->deploy_address }}</td>
                        </tr>
                        <tr>
                            <td>乘车路线</td>
                            <td>{{ $project->way }}</td>
                        </tr>
                        <tr>
                            <td>备注</td>
                            <td>{{ $project->description }}</td>
                        </tr>

                        @foreach($project->items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>
                                    {{ $item->value }}
                                    <span class="pull-right">
                                        <a href="{{ route('item.delete', ['id'=> $item->id]) }}"><i class="fa fa-times"></i></a>
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                        @if (\Session::get('user')->can('项目信息管理'))
                        <tr>
                            <td colspan="2">
                                <span class="pull-right">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#add-item"><i class="fa fa-plus"></i> 追加字段</button>
                                </span>

                                <div class="modal fade" id="add-item" tabindex="-1" role="dialog" aria-labelledby="add-item-modal-label">
                                    <div class="modal-dialog modal-sm" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="edit-server-modal-label">追加字段</h4>
                                            </div>
                                            <div class="modal-body">
                                                <form id="add-item-form" method="post" action="{{ route('item.add') }}">
                                                    <input type="hidden" name="object_type" value="{{ get_class($project) }}"/>
                                                    <input type="hidden" name="object_id" value="{{ $project->id }}" />

                                                    <div class="form-group">
                                                        <input name="name" type="text" class="form-control" placeholder="名称(前台页面)">
                                                    </div>

                                                    <div class="form-group">
                                                        <input name="value" type="text" class="form-control" placeholder="显示值(http://www.baidu.com)">
                                                    </div>

                                                    <div class="form-group">
                                                        <input name="key" type="text" class="form-control" placeholder="代码(web_fronted) 可不填">
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                                <button type="submit" class="btn btn-primary" form="add-item-form">添加</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-user"> 其他信息</i>
                </div>
                <div class="panel-body">
                    <div>
                        <!-- Nav tabs -->
                        <ul class="profile nav nav-tabs" role="tablist">

                            <li role="presentation" class="active">
                                <a data-type="softwares" href="#softwares-tab" role="tab">
                                    <i class="fa fa-bolt"></i> 软件信息
                                </a>
                            </li>

                            <li role="presentation">
                                <a data-type="hardwares" href="#hardwares-tab" role="tab">
                                    <i class="fa fa-archive"></i> 硬件信息
                                </a>
                            </li>

                            <li role="presentation">
                                <a data-type="servers" href="#servers-tab" role="tab">
                                    <i class="fa fa-linux"></i> 服务器信息
                                </a>
                            </li>


                            <li role="presentation">
                                <a data-type="trello" href="#trello-tab" role="tab">
                                    <i class="fa fa-wrench"></i> 部署情况
                                </a>
                            </li>

                            <li role="presentation">
                                <a data-type="informations" href="#informations-tab" role="tab">
                                    <i class="fa fa-info"></i> 信息变动
                                </a>
                            </li>

                            <li role="presentation" >
                                <a data-type="comments" href="#comments-tab" role="tab">
                                    <i class="fa fa-comment-o"></i> 备注信息
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="softwares-tab">
                                <div class="text-center loading">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="hardwares-tab">
                                <div class="text-center loading">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                </div>
                            </div>

                            <!-- server -->
                            <div role="tabpanel" class="tab-pane" id="servers-tab">
                                <div class="text-center loading">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                </div>
                            </div>
                            <!-- server end -->

                            <!-- trello -->
                            <div role="tabpanel" class="tab-pane" id="trello-tab">
                                <div class="text-center loading">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                </div>
                            </div>
                            <!-- trello end-->

                            <!-- info -->
                            <div role="tabpanel" class="tab-pane" id="informations-tab">
                                <div class="text-center loading">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                </div>
                            </div>
                            <!-- info end-->

                            <div role="tabpanel" class="tab-pane" id="comments-tab">
                                <div class="text-center loading">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        requirejs(['jquery', 'bootstrap'], function($) {

            $('ul.profile a').bind('click', function(e) {
                e.preventDefault();

                var type = $(this).data('type');

                var display_content = $('#'+ type + '-tab');

                display_content.load("{{ route('project.profile.item', ['id'=> $project->id]) }}", {
                    'type': type
                });

                display_content.addClass('active');

                $(this).tab('show');
            });

            var $active = $('ul.profile li.active a:eq(0)').trigger('click');
        });
    </script>

@endsection
