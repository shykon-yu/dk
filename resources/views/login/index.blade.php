<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="renderer" content="webkit">
    <title>后台登录</title>
    <!-- 静态资源路径已优化（Laravel标准写法） -->
    <link rel="stylesheet" href="/css/pintuer.css">
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="/css/font.css">
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/jquery.js"></script>
    <script src="/js/pintuer.js"></script>
    <script src="/js/jquery.form.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
    <style>
        body {
            text-align: center;
            background: #F7FAFC;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        .copyright {
            margin-top: 50px;
            font-size: 12px;
            color: #666;
        }
        /* 错误提示样式 */
        .error-msg {
            color: #ff4444;
            font-size: 12px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="line bouncein">
        <div class="xs6 xm4 xs3-move xm4-move">
            <div style="height:150px;"></div>
            <h3 class="login_tit">后台</h3>
            <div class="media media-y margin-big-bottom"></div>

            <!-- 核心优化：对接Breeze登录路由 + CSRF令牌 -->
            <form action="{{ route('login') }}" method="post" id="login">
                @csrf
                <div class="loginbox webdesigntuts-workshop">
                    <div class="panel-body" style="padding:30px; padding-bottom:10px; padding-top:10px;">
                        <!-- Breeze错误提示（登录失败自动显示） -->
                        @if ($errors->any())
                            <div class="error-msg">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="field field-icon-right">
                                <!-- 字段名改为 username 对接Breeze -->
                                <input type="text" class="input input-big" name="username" id="username" placeholder="登录账号" value="{{ old('username') }}">
                                <span class="icon icon-user margin-small"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="field field-icon-right">
                                <input type="password" class="input input-big" name="password" id="password" placeholder="登录密码">
                                <span class="icon icon-key margin-small"></span>
                            </div>
                        </div>
                    </div>
                    <div id="login_gif" class="hidden"><img src="/images/login.gif" width="20"/>验证提交中...</div>
                    <div style="padding:30px;">
                        <input type="submit" class="button button-block bg-main text-big input-big" value="登录" id="T">
                    </div>
                    @if (Route::has('register'))
                        <div><a href="{{ route('register') }}" style="color:green">注册新用户</a></div>
                    @endif

                </div>
            </form>
        </div>
    </div>
    <div class="copyright">
        版权所有©后台管理有限公司<br>
        技术支持：后台管理有限公司
    </div>
</div>
<canvas id="canvas" style="margin-top:-650px;">您的浏览器不支持canvas标签，请您更换浏览器</canvas>
</body>
</html>
<script src="/js/word.js"></script>
