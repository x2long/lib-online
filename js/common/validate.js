//this.optional(element) ||
jQuery.validator.addMethod("scriptCheck", function(value, element) {
return !(/<{1,}/.test(value));
}, "含有非法字符");
/* */
//添加自定义验证方法scriptCheck，保证输入中不能含有<，防止代码的恶意注入
//edit by LiZhenyu,2012/3/10
$(document).ready(function() { 
	var url = $('form#commentform a#checkBookUrl').attr("href");
	
	$("#commentform").validate({ 
		
		rules: { 
			book_name: {
				required: true,
				scriptCheck:true
			}, 
			isbn: {
				required: true,
				digits: true,
				rangelength:[13,13],
				
				remote:{
						  url: url,     //后台处理程序 
						  type: "post",               //数据发送方式
						  //dataType: "json",           //接受数据格式   
						  data: {                  //要传递的数据
									isbn:function() {
								    return $("#isbn").val();
									}
								}
						}			
				},
				
			author: {
				required: true,
				scriptCheck:true
			},
			publisher: {
				required: true,
				scriptCheck:true
			},
			price: {
				required: true,
				number: true
			},
			recommender: {
				scriptCheck:true
			},
			recommender_email: { 
				required: true, 
				email: true
			},
			description: {
				scriptCheck:true
			}
		},
		messages: { 
			book_name: {
				required: "图书名称不能为空"
			},
			isbn: {
				required: "isbn不能为空",
				digits: "isbn只允许为整数",
				rangelength: "isbn为13位整数",
				remote: "该图书已存在"
			},
			author: {
				required: "作者不能为空"
			},
			publisher: {
				required: "出版社不能为空"
			},
			price: {
				required: "图书价格不能为空",
				number: "只能输入数字"
			},
			recommender_email: { 
				required: "Email不能为空", 
				email: "Email格式有误" 
			}
		} 
	}); 
	
	$("#userForm").validate({ 
		rules: {
            userid: {
				required: true,
				digits:true
			},
            password: {
				required: true
			}
		},
		messages: {
            userid: {
				required: "该项不能为空",
				digits: "工号只能为数字"
			},
            password: {
				required: "该项不能为空"
			}
		} 
	});

    $("#changePasswordForm").validate({
        rules: {
            pre_password: {
                required: true
            },
            password: {
                required: true
            },
            password2: {
                required: true,
                equalTo:"#password"
            }
        },
        messages: {
            pre_password: {
                required: "该项不能为空"
            },
            password: {
                required: "该项不能为空"
            },
            password2: {
                required: "该项不能为空",
                equalTo:"两次输入必须一致！"
            }
        }
    });

    $("#recommendBookForm").validate({
        rules: {
            douban_url: {
                required: true,
                url:true
            },
            book_name: {
                required: true
            },
            isbn: {
                required: true,
                digits: true,
                rangelength:[13,13]
            }
        },
        messages: {
            douban_url: {
                required: "该项不能为空",
                url:"输入合法的网址！"
            },
            book_name: {
                required: "图书名称不能为空"
            },
            isbn: {
                required: "isbn不能为空",
                digits: "isbn只允许为整数",
                rangelength: "isbn为13位整数",
            }
        }
    });

    $("#commentform").validate({
        rules: {
            contents: {
                required: true
            }
        },
        messages: {
            contents: {
                required: "该项不能为空"
            }
        }
    });
}); 