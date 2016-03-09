@extends('layout')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">{{ $hardware->name }}</h1>
        </div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-user"> 基本信息</i>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover">
                        <tr>
                            <td class="col-md-2">硬件名称</td>
                            <td>
                                {{ $hardware->name }}
                            </td>
                        </tr>
                        <tr>
                            <td>型号/规格</td>
                            <td>{{ $hardware->model }}</td>
                        </tr>
                        <tr>
                            <td>生产状态</td>
                            <td>
                                @if ($hardware->self_produce)
                                    自产
                                @else
                                    外采
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>备注</td>
                            <td>{{ $hardware->description }}</td>
                        </tr>

                        @foreach($hardware->items as $item)
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
                                                    <input type="hidden" name="object_type" value="{{ get_class($hardware) }}"/>
                                                    <input type="hidden" name="object_id" value="{{ $hardware->id }}" />

                                                    <div class="form-group">
                                                        <input name="name" type="text" class="form-control" placeholder="名称(易燃易爆性)">
                                                    </div>

                                                    <div class="form-group">
                                                        <input name="value" type="text" class="form-control" placeholder="显示值(遇空气可燃)">
                                                    </div>

                                                    <div class="form-group">
                                                        <input name="key" type="text" class="form-control" placeholder="代码(attributes) 可不填">
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
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-user"> 部署表单信息</i>
        </div>
        <div class="panel-body">
            <div class="row">

                <div class="col-md-6">

                    <table class="table table-hover">
                        <tr>
                            <td>名称</td>
                        </tr>
                        @foreach($hardware->fields as $field)
                            <tr>
                                <td>
                                    {{ $field->name }}
                                    <span class="pull-right">

                                        <span class="edit edit-hardware-field" data-id="{{ $field->id }}" data-name="{{ $field->name }}">
                                            <i class="fa fa-fw fa-edit"></i>
                                        </span>

                                        <a href="{{ route('hardware_field.delete', ['id'=> $field->id]) }}">
                                            <i class="fa fa-fw fa-times"></i>
                                        </a>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="col-md-6">

                    <form method="post" action="{{ route('hardware_field.add') }}">

                        <input type="hidden" name="hardware_id" value="{{ $hardware->id }}">
                        <div class="form-group">
                            <input name="name" type="text" placeholder="表单名称" class="form-control" />
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-fw fa-plus"></i> 添加
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-user"> 已部署硬件</i>
        </div>
        <div class="panel-body">
            <div class="row">

                <div class="col-md-12">

                    <table class="table table-hover">
                        <tr>
                            <td>项目名称</td>
                            <td>仪器名称</td>
                            <td>仪器 ID</td>
                            <td>状态</td>
                        </tr>
                        @foreach($hardware->hardware_items as $i)
                            <tr>
                                <td>
                                    <a href="{{ route('project.profile', ['id'=> $i->project->id]) }}">
                                        {{ $i->project->name }}
                                    </a>
                                </td>
                                <td>{{ $i->equipment_name }}</td>
                                <td>{{ $i->equipment_id }}</td>
                                <td>
                                    {{--*/
                                    $status_label_class = [
                                        \App\HardwareItem::STATUS_ON_THE_WAY => 'warning',
                                        \App\HardwareItem::STATUS_DELIVERED => 'default',
                                        \App\HardwareItem::STATUS_DEPLOYED => 'success',
                                        \App\HardwareItem::STATUS_WASTED => 'danger',
                                    ];
                                    /*--}}

                                    <span class="label label-{{$status_label_class[$i->status]}}">
                                        {{ \App\HardwareItem::$status[$i->status] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="edit-hardware-field" tabindex="-1" role="dialog" aria-labelledby="edit-hardware-field-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="edit-hardware-field-modal-label">修改部署表单信息</h4>
                </div>
                <div class="modal-body">
                    <form id="edit-hardware-field-form" class="form-horizontal" method="post" action="{{ route('hardware_field.edit') }}">
                        <input type="hidden" name="id">


                        <div class="form-group">
                            <label for="hardware-item-name" class="col-md-2 control-label">名称</label>
                            <div class="col-md-10">
                                <input name="name" type="text" class="form-control" id="hardware-item-name">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary" form="edit-hardware-field-form">修改</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        require(['jquery'], function($) {

            $('.edit-hardware-field').bind('click', function() {

                var $modal = $('#edit-hardware-field');

                $modal.find(':input[name=name]').val($(this).data('name'));
                $modal.find(':input[name=id]').val($(this).data('id'));

                $modal.modal();
            });
        });
    </script>

@endsection
