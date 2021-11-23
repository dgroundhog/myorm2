一款超好用的开源的图形验证码：EasyCaptcha
https://gitee.com/ele-admin/EasyCaptcha
鼠色猫 2019-12-26 09:30:46  4748  收藏 12
分类专栏： Captcha 学习历程 文章标签： java
版权

Captcha
同时被 2 个专栏收录
1 篇文章0 订阅
订阅专栏

学习历程
22 篇文章0 订阅
订阅专栏
EasyCaptcha
github地址: https://github.com/whvcse/EasyCaptcha

简介
Java图形验证码，支持gif、中文、算术等类型，可用于Java Web、JavaSE等项目。

效果展示(部分验证码闪动，截图无法展示)
在这里插入图片描述

导入方式
1、 maven

<dependencies>
 <dependency>
 	<groupId>com.github.whvcse</groupId>
 	<artifactId>easy-captcha</artifactId>
 	<version>1.6.2</version>
 </dependency>
 </dependencies>
1
2
3
4
5
6
7
2、 gradle

dependencies {
 compile 'com.github.whvcse:easy-captcha:1.6.2'
 }
1
2
3
jar包

下载地址 EasyCaptcha.jar

在SpringMVC中使用
@Controller
public class CaptchaController {undefined

@RequestMapping("/captcha")
public void captcha(HttpServletRequest request, HttpServletResponse response) throws Exception {
    CaptchaUtil.out(request, response);
}
1
2
3
4
}

前端HTML

<img src="/captcha" width="130px" height="48px" />
1
如果使用了安全框架之类的拦截功能，要把/captcha路径排除登录拦截。

有些同学可能需要在Servlet中使用如下
web.xml中配置servlet：

<web-app>
 <!-- 图形验证码servlet -->
 <servlet>
 	<servlet-name>CaptchaServlet</servlet-name>
 	<servlet-class>com.wf.captcha.servlet.CaptchaServlet</servlet-class>
 </servlet>
 <servlet-mapping>
 	<servlet-name>CaptchaServlet</servlet-name>
 	<url-pattern>/captcha</url-pattern>
 </servlet-mapping>
 </web-app>
1
2
3
4
5
6
7
8
9
10
11
前端html代码：

<img src="/captcha" width="130px" height="48px" />
1
比较验证码
@Controller
public class LoginController {
	@PostMapping("/login")
	public JsonResult login(String username,String password,String verCode){
    	if (!CaptchaUtil.ver(verCode, request)) {
        	CaptchaUtil.clear(request);  // 清除session中的验证码
        	return JsonResult.error("验证码不正确");
    }
}
1
2
3
4
5
6
7
8
9
设置宽高和位数
@Controller
public class CaptchaController {
    @RequestMapping("/captcha")
    public void captcha(HttpServletRequest request, HttpServletResponse response) throws Exception {
        // 设置位数
        CaptchaUtil.out(5, request, response);
        // 设置宽、高、位数
        CaptchaUtil.out(130, 48, 5, request, response);

        // 使用gif验证码
        GifCaptcha gifCaptcha = new GifCaptcha(130,48,4);
        CaptchaUtil.out(gifCaptcha, request, response);
    }
}
1
2
3
4
5
6
7
8
9
10
11
12
13
14
还有更多参数，可到作者github查看