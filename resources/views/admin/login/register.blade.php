<!DOCTYPE html>
<html lang="zh-cn">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="renderer" content="webkit">
	<title>账号注册</title>
	<link rel="stylesheet" href="/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/font-awesome.min.css">
	<link rel="stylesheet" href="/css/pintuer.css">
	<link rel="stylesheet" href="/css/admin.css">
	<script src="/js/jquery.js"></script>
	<script src="/js/bootstrap.min.js"></script>
	<script src="/js/distpicker.data.js"></script>
	<script src="/js/distpicker.js"></script>
	<script src="/js/bootstrap-select.min.js"></script>
        <script src="/js/jquery.form.js"></script>
        <script src="/js/jquery.validate.min.js"></script>
	<style type="text/css">
		.label {
			font-size: 100%;
		}

		.body-content {
			overflow: visible;
		}

		.white {
			color: white;
		}
		.list-group li, .list-link a {
			padding: 0 0 0 40px;
			border-bottom: none;
			/* transition: all 1s cubic-bezier(0.175,0.885,0.32,1) 0s; */
		}
	</style>
</head>

<body>
	<div class="panel admin-panel">
		<div class="panel-head"><strong><span class="icon-key"></span> 账号注册</strong></div>
		<div class="body-content">
			<form method="post" class="form-x" action="" id="register">
				<div class="form-group">
					<div class="label">
						<label for="sitename">用户名：</label>
					</div>
					<div class="field field-icon-right">
						<input type="sitename" class="input w50" id="user_name" name="user_name" size="50" placeholder="请输入账号" />
					</div>
				</div>
				<div class="form-group">
					<div class="label">
						<label for="sitename">密码：</label>
					</div>
					<div class="field">
						<input type="password" class="input w50" id="password" name="password" size="50" placeholder="请输入密码" />
					</div>
				</div>
				<div class="form-group">
					<div class="label">
						<label for="sitename">确认密码：</label>
					</div>
					<div class="field">
						<input type="password" class="input w50" id="confirm_password" name="confirm_password" size="50" placeholder="请再次输入新密码" />
					</div>
				</div>

<!--				<div class="form-group showArea">
					<div class="label">
						<label for="sitename">地域：</label>
					</div>
					<div class="field">
						<div id="distpicker">
							<select class="input w16" id="province"></select>
							<select class="input w16" id="city"></select>
							<select class="input w16" id="district"></select>
						</div>
					</div>
				</div>-->

				<div class="form-group">
					<div class="label">
						<label for="sitename">姓名：</label>
					</div>
					<div class="field">
						<input type="text" class="input w50" name="name" id="name" size="50" placeholder="请输入姓名" />

					</div>
				</div>

<!--                                <div class="form-group showArea">
					<div class="label">
						<label for="sitename">证件类型：</label>
					</div>
					<div class="field">
						<div id="distpicker">
                                                    <select name="id_type" class="input w50" id="id_type">
                                                        <option value="">--请选择证件类型--</option>
                                                        <option value="1">--身份证--</option>
                                                        <option value="2">--护照--</option>
                                                    </select>
						</div>
					</div>
				</div>-->
                                <script>
//                                    $('#register').on('change', '#id_type', function () {
//                                        var id_type = $("#id_type").val();
//                                        str = '<div class="label">';
//                                        if( id_type == 1 ){
//                                            str += '        <label for="sitename">身份证号：</label>';
//                                        }else{
//                                            str += '        <label for="sitename">护照号码：</label>';
//                                        }
//
//                                        str += '</div>';
//                                        str += '<div class="field">';
//                                        if( id_type == 1 ){
//                                            str += '        <input type="text" class="input w50" name="id_number" id="id_number" size="50" placeholder="请输入身份证号码" />';
//                                        }else{
//                                            str += '        <input type="text" class="input w50" name="id_number" id="id_number" size="50" placeholder="请输入护照号码" />';
//                                        }
//
//                                        str += '</div>';
//                                    })
//                                    <div class="form-group">
//                                            <div class="label">
//                                                    <label for="sitename">证件号码：</label>
//                                            </div>
//                                            <div class="field">
//                                                    <input type="text" class="input w50" name="id_number" id="id_number" size="50" placeholder="请输入证件号码" />
//                                            </div>
//                                    </div>
                                </script>
<!--                                    <div class="form-group" id="card_number">
                                        <div class="label">
                                                <label for="sitename">身份证号：</label>
                                        </div>
                                        <div class="field">
                                                <input type="text" class="input w50" name="id_number" id="id_number" size="50" placeholder="请输入身份证号" />
                                        </div>
                                    </div>-->
				<div class="form-group">
					<div class="label">
						<label></label>
					</div>
					<div class="field">
<!--						<button class="button bg-main icon-check-square-o" id="T" type="button"> 提交</button>-->
                                                <input type="submit" class="button bg-main icon-check-square-o" value="注册" id="T">
					</div>
				</div>
			</form>
		</div>
	</div>
</body>

</html>
<script type="text/javascript">
	$(document).ready(function () {
            $("#register").validate({
                             //debug:true,
                        rules: {
                            user_name :{
                                    required:true,
                                    },
                            password :{
                                    required:true,
                                  },
                            confirm_password :{
                                    required:true,
                                  },
                            name :{
                                    required:true,
                                  },
//                            id_type :{
//                                    required:true,
//                                    number: true,
//                                  },
//                            id_number :{
//                                    required:true,
//                                  },
                            },
                            messages: {
                              user_name :{
                                      required:"请输入用户名",
                                      },
                              password :{
                                      required:"请输入密码",
                                      },
                              confirm_password :{
                                      required:"请输入确认密码",
                                      },
                              name :{
                                      required:"请输入姓名",
                                      },
//                              id_type :{
//                                      required:"请选择证件类型",
//                                      number:"请选择证件类型",
//                                      },
//                              id_number :{
//                                      required:"请输入证件号码",
//                                      },
                            },
                            submitHandler: function(form) {
                                    var user_name =$("#user_name").val();
                                    var password = $("#password").val();
                                    var confirm_password = $("#confirm_password").val();
                                    var name = $("#name").val();
//                                    var id_type = $("#id_type").val();
                                    //var id_number = $("#id_number").val();
                                    $(form).ajaxSubmit({
                                        url:"{:U('Login/add_user')}",
                                        type:"post",
                                        dataType: "json",
                                        data:{
                                                user_name : user_name , password : password ,
                                                confirm_password : confirm_password , name : name ,
                                                /*id_type : id_type , id_number : id_number ,*/
                                                },
                                        error:function(t){
                                                console.log(t)
                                                alert("数据添加失败");
                                                },
                                        beforeSubmit:function(){
                                                //$("#login_gif").removeClass("hidden");
                                        },
                                        success: function( data ){
                                            alert(data.msg);
                                            if( data.code == 200 ){
                                                location.href ="{:U('Login/login')}";
                                            }
                                        },
                                    });
                            },
                            errorPlacement: function (error, element) {
                                var eid = element.attr('name');
                                error.insertAfter(element);
                       },

            });
	})
</script>
