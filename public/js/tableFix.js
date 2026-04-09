// JavaScript Document

function tableFix(t) {
	$(document).ready(function (e) {
		//var leftSet = 0;
		var isLeftFirst = true;
		$("#"+t).find("thead").find("tr:last").find("th").click(function(){
			var index = $(this).index();
			$(this).toggleClass("cloumnSelect").attr("data-cloumn","cloumn"+index)
			$("#"+t).find("tbody").find("tr").each(function(){
				$(this).find("td,th").eq(index).toggleClass("cloumn"+index+"");
			});
			if(isLeftFirst){
				var trLen = $("#"+t).find("tbody").find("tr").length;
				trHtml = "";
				for(var i = 0; i < trLen; i++){
					trHtml += "<tr></tr>"
				}
				$("#"+t).before('<table class="table table-bordered table-condensed table-hover table-striped" style="margin-bottom:0; margin-left:-16px; width:1px; position:fixed; background-color:white;" id="cloumn'+t+'"><thead><tr style=""></tr></thead><tbody class="text-center">'+trHtml+'</tbody></table>');
				isLeftFirst = false;
			}
		})
		var isFirst = true;
		var topSet = $("#" + t).offset().top;
		$(window).scroll(function (e) {
			var scrollTop = $(window).scrollTop();
			var scrollLeft = $(window).scrollLeft();
			if (topSet < scrollTop) {
				if (isFirst) {
					$(".fix-thead").remove();
					$("#" + t).before("<div class='fix-thead'><table class='table table-bordered text-center table-condensed' id='new" + t + "' style='background-color:white;margin-bottom:0;'></table></div>");
					$("#" + t).find("thead").clone(false).prependTo("#new" + t);
					isFirst = false;
					$("#" + t).find("tbody").find("tr:first").find("td").each(function (index, element) {
						var tdWidth = $(this).outerWidth();
						$("#new" + t).find("thead").find("tr:last").find("th").eq(index).css({ "min-width": "" + tdWidth + "px","background-color":"white" });
					});
				}
			} else {
				$(".fix-thead").remove();
				isFirst = true;
			}
			if (scrollLeft >= 0) {
				$("#new" + t).css({ "margin-left": "-" + scrollLeft + "px" });
			}
			var cloumnSelectLen = $("#"+t).find(".cloumnSelect").length;
			if(cloumnSelectLen > 0){	
				var cloneWidth = 0
				$("#"+t).find("thead").find("tr").last().find(".cloumnSelected").each(function(){
					cloneWidth += $(this).outerWidth();
				})	
				var leftSet = $("#"+t).find(".cloumnSelect").first().offset().left;
				if((scrollLeft + cloneWidth) >= leftSet){
					var cloumnClone = $("#"+t).find(".cloumnSelect").first().attr("data-cloumn");
					var thWidth = $("#"+t).find(".cloumnSelect").first().outerWidth();
					var thHeight = $("#"+t).find(".cloumnSelect").first().outerHeight()-1;
					//console.log(cloumnClone);
					$("#cloumn"+t).find("thead").find("tr").append($("#"+t).find(".cloumnSelect").first().clone().width(thWidth).height(thHeight));
					$("#"+t).find(".cloumnSelect").first().removeClass("cloumnSelect").addClass("cloumnSelected");
					$("#"+t).find("."+cloumnClone).each(function(i,n){
						var outerHeight = $(this).outerHeight();
						$("#cloumn"+t).find("tbody").find("tr").eq(i).append($(this).clone().height(outerHeight));
					})
									
				}
			}
			var cloumnSelectedLen = $("#"+t).find(".cloumnSelected").length;
			if(cloumnSelectedLen > 0){
				var leftSet = $("#"+t).find(".cloumnSelected").last().offset().left;
				var lastWidth = $("#"+t).find(".cloumnSelected").last().outerWidth();
				var cloneWidth = 0
				$("#"+t).find("thead").find("tr").last().find(".cloumnSelected").each(function(){
					cloneWidth += $(this).outerWidth();
				})
				if((scrollLeft + cloneWidth -lastWidth) <= leftSet){
					var cloumnClone = $("#"+t).find(".cloumnSelected").last().attr("data-cloumn");
					$("#cloumn"+t).find("."+cloumnClone).remove();
					$("#cloumn"+t).find(".cloumnSelect").last().remove();	
					$("#"+t).find(".cloumnSelected").last().removeClass("cloumnSelected").addClass("cloumnSelect");			
				}
			}
			if(scrollTop >= 0){
				var theadHeight = $("#"+t).find("thead").outerHeight() - $("#"+t).find("thead").find("tr").last().outerHeight();
				//console.log(theadHeight);
				var topHeight = parseInt($("#"+t).css("marginTop")) + theadHeight - scrollTop + 1;
				$("#cloumn"+t).css({"margin-top":topHeight +"px"});
			}
		});
		$(document).on("click","#new"+t+" tr:last() th",function(){
			var index = $(this).index();
			$(this).toggleClass("cloumnSelect");
			$("#"+t).find("thead").find("tr:last").find("th").eq(index).trigger("click");
		})
		//console.log(topSet);
		//表格点击行进行固定颜色标记
		$("tbody").on("click", "tr", function () {
			$(this).toggleClass("trBg");
			$(this).find("tr").toggleClass("trBg");
		})
		//处理右侧按钮显示
		var height = $(window).height();
		var fix_box_height = $(".fix-right-box").height();
		var fix_margin_height = (height - fix_box_height)/2;
		$(".fix-right-box").css({"top":fix_margin_height + "px"});
		//订单管理及管理页面右侧导出按钮显示
		$(".fix-right-box").find("input[type='button']").each(function(){
			var width = $(this).outerWidth();
			var margin_right = width - 25;
			$(this).css({"marginRight":-margin_right + "px"});
		})
		$(".fix-right-box input[type='button']").hover(function(){
			$(this).delay(300).animate({marginRight:"0px"});
		},function(){
			$(this).stop(true,false);
			var width = $(this).outerWidth();
			var margin_right = width - 25;
			$(this).animate({marginRight:-margin_right + "px"});
		})
	});
}
