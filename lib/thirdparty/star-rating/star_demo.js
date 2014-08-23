$(function () {
         InitStarRating("div_StarRating",0);
     });
     //初始化评分插件
    function InitStarRating(divid,selfRaty)
    {
        var RatyReadOnly = true;
        if (selfRaty == 0) {
               RatyReadOnly = false;
        }
        InitDiv(divid,selfRaty);
        $('input.star').rating({
             readOnly:RatyReadOnly,
             callback: function (value,link) {
                     //保存评分
                     SaveRating(value);
                     //设置只评分一次
                     ClickRating(divid,value);
                }
         });
    }
    //保存评分
    function SaveRating(selfRaty) {
        //alert(selfRaty);
    }
    //初始化Star Rating
    function InitDiv(id,selfRaty)
    {
        $("#"+id).html("");
        var input="";
        if(id)
        {
             for(var i=1;i<=5;i++)
             {
                 if(i==selfRaty)
                 {
                     input+="<input name=\""+id+"\" type=\"radio\" class=\"star\" checked=\"checked\" value=\""+i+"\" title=\""+i+"分\" />";
                 }
                 else
                 {
                     input+="<input name=\""+id+"\" type=\"radio\" class=\"star\" value=\""+i+"\" title=\""+i+"分\"/>";
                 }
             }
        }
        $(input).appendTo($("#"+id));
    }
    //设置不可重复评分
    function ClickRating(id,selfRaty)
    {
        InitDiv(id,selfRaty);
        $('input.star').rating({
             readOnly:true
        });
    }