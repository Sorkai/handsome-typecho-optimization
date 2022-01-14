# Handsome-Typecho-optimization

这是一个通过修改Typecho及其主题(Handsome)文件，将静态资源使用CDN加速来提高网站加载速度的项目。

**当前正在实现部分功能，资源正在不断补充中....**

CDN开放所有人使用(非商业、不滥用)，采用境内外双地域的自同步多AZ对象存储作为源站，境内使用大带宽全域覆盖的优质CDN对接境内对象存储源站，境外采用Cloudflare PRO版对接境外对象存储源站，全球访问速度优秀。

此项目开放提交现有资源以外的其它Typecho静态资源、Handsome主题静态资源、常用插件静态资源、其它常用js、css、字体、svg等静态资源，如有需要请提出Issues。

## 目前资源列表

### Handsome主题标准静态文件(主题assets文件夹)

所处项目位置：/**kaiassets**/

目前包含版本：8.3.0及部分历史版本

使用方法：

打开你的网站后台，在 控制台-外观-设置外观 中找到"主题本地静态资源自定义cdn加速"，填写加速链接，如图↓

![](https://img.kai233.top/picgo/202201150513195.png)

```html
填写地址
https://jsd.sorkai.com/kaiassets/版本号/
如：
https://jsd.sorkai.com/kaiassets/8.2.1/
```

