<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="renderer" content="webkit">
    <title>后台管理中心</title>
    <script src="/js/jquery-1.9.1.min.js"></script>
    <script src="/js/jquery.fix.clone.js"></script>
    <script src="/js/jquery.validate.js"></script>
    <script src="/js/jquery-ui-1.9.2.custom.js"></script>
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.ui.datepicker.js"></script>
    <script src="/js/jquery.ui.datepicker-zh-CN.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/js/jquery.form.js"></script>
    <script src="/js/jquery.mousewheel.min.js"></script>
    <script src="/js/bootstrap-select.min.js"></script>

    <link rel="stylesheet" href="/css/jquery-ui-1.9.2.custom.min.css" />
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/bootstrap-select.min.css">

    <link rel="stylesheet" href="/css/pintuer.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/admin.css">
    <style type="text/css">
        .rota {
            transform: rotate(180deg);
            -ms-transform: rotate(180deg);
            /* IE 9 */
            -moz-transform: rotate(180deg);
            /* Firefox */
            -webkit-transform: rotate(180deg);
            /* Safari 和 Chrome */
            -o-transform: rotate(180deg);
            /* Opera */
            -webkit-animation: 2s infinite linear;
            -moz-animation: 2s infinite linear;
            -ms-animation: 2s infinite linear;
            animation: 2s infinite linear;
            display: inline-block;
        }
        button{
            padding: 5px 10px !important;
        }
        .body-tit{
            padding: 0 15px;
        }
        .no_order_list{
            height: 232px;
            width: 100%;
            overflow: hidden;
            padding: 0 20px;
        }
        .no_order_list li{
            list-style: circle;
            height: 25px;
            line-height: 25px;
            display: flex;
            cursor: pointer;
        }
        .warning-text{
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .warning-time {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<script>
    $(document).ready(function (e) {
        $("#start_date").datepicker({
            maxDate: '+0y +0m +0d',//最大日期
            onSelect: function (dateText, inst) {
                $("#end_date").datepicker("option", "minDate", dateText);
            }

        });
        $("#end_date").datepicker({
            maxDate: '+0y +0m +0d',//最大日期
            onSelect: function (dateText, inst) {
                $("#start_date").datepicker("option", "maxDate", dateText);
            }

        });
    })
    $(function () {
        $(".leftnav h2").click(function () {
            $(this).next().slideToggle(200);
            $(this).toggleClass("on");
            $(this).children(".icon-caret-down").toggleClass("rota");
        })
        $(".leftnav ul li a").click(function () {
            $(this).addClass("on");
            $(".default-body").remove();
            $("iframe").show();
        })
    });
</script>
<body>
<!-- 左侧导航栏 -->
<div class="leftnav">
    <div class="leftnav-title">
        <!--                <span class="logo"></span>-->
        <span class="logo-tit">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;后台管理系统111</span>
    </div>
    @foreach($menu as $key=>$value)
        <h2><span class="icon-caret-down"></span>{{$value['title']}}</h2>
        <ul>
        @foreach($value['children'] as $subkey => $subvalue)
                <li>
                    <!-- target="right" -->
                    <a href="{:U($sons['url'],array('column_id'=>$sons['id']))}" target="right"  >
                        <span class="icon-caret-right"></span>{{$subvalue['title']}}
                    </a>
                </li>
        @endforeach
        </ul>
    @endforeach
</div>

<ul class="nav navbar-nav bread">
    <li>{$name},你好。欢迎登录后台管理系统</li>
    <li <if condition="$section_id eq 0 || $section_id eq ''">class="active"</if>><a href="{:U('Home/Index/index/',array('section_id'=>0))}">首页</a></li>
    <volist name="section_list" id="vo">
        <li <if condition="$vo['id'] eq $section_id">class="active"</if>><a href="{:U('Home/Index/index/',array('section_id'=>$vo['id']))}">{$vo.name}</a></li>
    </volist>
    <li class=""><a href="{:U('Index/logout')}" id="a_leader_txt">退出</a></li>
</ul>

<div class="admin">
    <div class="default-body">
        <div class="xb6">
            <div class="xb12 body-tit">
                <h4>基本信息</h4>
            </div>
            <div class="xb6 xm6 xs12">
                <div class="info-bg">
                    <div class="info-tit">
                        <a href="#">
                            <h4>近七日入库数量</h4>
                        </a>
                    </div>
                    <div class="icon-tree info-ico "></div>
                    <span class="info-num">
							<span id="log_out"></span>
						</span>
                </div>
                					<div class="info-bg pink">
                                        <div class="info-tit">
                                            <a href="#">
                                                <h4>昨日生产成材</h4>
                                            </a>
                                        </div>
                                        <div class="icon-cubes info-ico "></div>
                                        <span class="info-num">
                                            <span></span>m
                                            <sup>3</sup>
                                        </span>
                                    </div>
            </div>

            <div class="xb6 xm6 xs12">
                <div class="info-bg yellow">
                    <div class="info-tit">
                        <a href="#">
                            <h4>近七日帽子出库数量</h4>
                        </a>
                    </div>
                    <div class="info-ico icon-external-link "></div>
                    <span class="info-num">
                        <!--						<sup>3</sup>-->
						</span>
                </div>
                					<div class="info-bg green">
                                        <div class="info-tit">
                                            <a href="#">
                                                <h4>昨日综合出材率</h4>
                                            </a>
                                        </div>
                                        <div class="info-ico icon-stack-overflow "></div>
                                        <span class="info-num"></span>>
                                    </div>
            </div>
        </div>
        <table class="table table-bordered table-striped s-table">
            <thead>
            <form method="get" action="__URL__/index">
                <tr>
                    <td colspan="18">
                        <div class="xb8 s-table-thead">产品出库报表</div>
                        <div class="input-group xb4 form-inline">
                            <div class="input-group">
                                <input id="start_date" type="text" name="start_date" autocomplete="off"placeholder="开始日期" value="{$choose.start_date}"	class="form-control input-sm">
                                <span class="input-group-addon"><span class="icon-calendar"></span></span>
                            </div>
                            <div class="input-group">
                                <input id="end_date" type="text" name="end_date" autocomplete="off" placeholder="结束日期" value="{$choose.end_date}"	class="form-control input-sm">
                                <span class="input-group-addon"><span class="icon-calendar"></span></span>
                            </div>
                            <button  class="button button-small bg-blue">搜索</button>
                        </div>
                    </td>
                </tr>
            </form>
            <tr>
                <th>序号</th>
                <th>客户</th>
                <th>类目</th>
                <th>数量</th>
                <th>金额</th>
            </tr>
            </thead>
            <tbody>
            <volist name="outbound_list" id="vo" key="k">
                <tr>
                    <td>{$k}</td>
                    <td>{$vo.custom_name}</td>
                    <td>{$vo.products_category_name}</td>
                    <td>{$vo.sum_outbound_number}</td>
                    <td>{$vo.currency_icon}&nbsp;{$vo.sum_money}</td>
                </tr>
            </volist>


            </tbody>
            <!--				<tfoot>
                                <tr>
                                    <td colspan="18" class="text-center pagelist">{$page}</td>
                                </tr>
                            </tfoot>-->
        </table>
    </div>
    <iframe scrolling="auto" rameborder="0" src="{:U('Index/info')}" name="right" width="100%" height="100%"></iframe>
</div>
</body>
</html>
