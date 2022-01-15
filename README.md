# Handsome-Typecho-optimization

这是一个通过修改Typecho及其主题(Handsome)文件，将静态资源使用CDN加速来提高网站加载速度的项目。

项目地址：[Sorkai/handsome-typecho-optimization: 通过修改文件替换cdn及将静态资源使用cdn加速提升网站访问速度 (github.com)](https://github.com/Sorkai/handsome-typecho-optimization)

**当前正在实现部分功能，资源正在不断补充中....**

CDN开放所有人使用(非商业、不滥用)，采用境内外双地域的自同步多AZ对象存储作为源站，背靠GitHub仓库(若境内对象存储中无对应文件，将会向GitHub发起回源，后将文件存储至境内对象存储库并推送至境外对象存储)，境内使用大带宽全域覆盖的优质CDN对接境内对象存储源站，境外采用Cloudflare PRO版对接境外对象存储源站，全球访问速度优秀。

若采取申请分支合并方式提交了文件并通过了审核，文件被合并至主分支后，请务必使用**境内IP**通过加速域名访问添加好的文件，首次加载会比较缓慢(数据会将近绕地球一圈，通过至少10级不同存储及CDN)，要耐心等待，加载完成后后续访问都将有极快的速度。

此项目开放提交现有资源以外的其它Typecho静态资源、Handsome主题静态资源、常用插件静态资源、其它常用js、css、字体、svg等静态资源，如有需要请提出Issues。

**注意：提交rp和is请到上方项目地址(GitHub仓库)提交！！**

***

常见资源使用方法

在为特殊说明的情况下均可以通过`https://jsd.sorkai.com/`+本项目中的路径进行使用

例如：`https://jsd.sorkai.com/web/3RVidO.th.jpg` 对应web文件夹下的3RVidO.th.jpg图片

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

