@extends('admin.layouts.app')
@section('extends_css')
    <style type="text/css">
        /* 完全和用户编辑页保持一致 */
        .form-item {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .form-item .label {
            width: 100px;
            text-align: right;
            padding-right: 10px;
            font-weight: 500;
            font-size: 14px;
            color: #333;
        }
        .form-item .field {
            flex: 1;
            max-width: 800px;
            font-size: 14px;
            color: #333;
            padding-top: 5px;
        }
        .panel-tit {
            font-size: 16px !important;
            font-weight: bold !important;
            color: #333 !important;
        }
        /* 详情展示样式 */
        .detail-text {
            padding: 6px 0;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-list-alt"></span>
            <span class="panel-tit">用户详情</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">

            <!-- 用户名 -->
            <div class="form-item">
                <label class="label">用户名：</label>
                <div class="field">
                    <div class="detail-text">{{ $user->username }}</div>
                </div>
            </div>

            <!-- 真实姓名 -->
            <div class="form-item">
                <label class="label">姓名：</label>
                <div class="field">
                    <div class="detail-text">{{ $user->name }}</div>
                </div>
            </div>

            <!-- 邮箱 -->
            <div class="form-item">
                <label class="label">邮箱：</label>
                <div class="field">
                    <div class="detail-text">{{ $user->email ?? '未填写' }}</div>
                </div>
            </div>

            <!-- 手机号 -->
            <div class="form-item">
                <label class="label">手机号：</label>
                <div class="field">
                    <div class="detail-text">{{ $user->phone_number ?? '未填写' }}</div>
                </div>
            </div>

            <!-- OpenID -->
            <div class="form-item">
                <label class="label">OpenID：</label>
                <div class="field">
                    <div class="detail-text">{{ $user->open_id ?? '未填写' }}</div>
                </div>
            </div>

            <!-- 角色 -->
            <div class="form-item">
                <label class="label">所属角色：</label>
                <div class="field">
                    <div class="detail-text">{{ $user->roles->first()?->name ?? '未分配角色' }}</div>
                </div>
            </div>

            <!-- 创建时间 -->
            <div class="form-item">
                <label class="label">创建时间：</label>
                <div class="field">
                    <div class="detail-text">{{ $user->created_at }}</div>
                </div>
            </div>

            <!-- 按钮 -->
            <div class="form-item">
                <label class="label"></label>
                <div class="field">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-warning btn-sm">
                        <span class="glyphicon glyphicon-arrow-left"></span> 返回列表
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
@endsection
