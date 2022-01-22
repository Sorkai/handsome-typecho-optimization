# Handsome-Typecho-optimization

这是一个通过修改Typecho及其主题(Handsome)文件，将静态资源使用CDN加速来提高网站加载速度的项目。

项目地址：[Sorkai/handsome-typecho-optimization: 通过修改文件替换cdn及将静态资源使用cdn加速提升网站访问速度 (github.com)](https://github.com/Sorkai/handsome-typecho-optimization)

**当前正在实现部分功能，资源正在不断补充中....**

CDN开放所有人使用(非商业、不滥用)，采用境内外双地域的自同步多AZ对象存储作为源站，背靠GitHub仓库(若境内对象存储中无对应文件，将会向GitHub发起回源，后将文件存储至境内对象存储库并推送至境外对象存储)，境内使用大带宽全域覆盖的优质CDN对接境内对象存储源站，境外采用Cloudflare PRO版对接境外对象存储源站，全球访问速度优秀。

若采取申请分支合并方式提交了文件并通过了审核，文件被合并至主分支后，请务必使用**境内IP**通过加速域名访问添加好的文件，首次加载会比较缓慢(数据会将近绕地球一圈，通过至少10级不同存储及CDN)，要耐心等待，加载完成后后续访问都将有极快的速度。

此项目开放提交现有资源以外的其它Typecho静态资源、Handsome主题静态资源、常用插件静态资源、其它常用js、css、字体、svg等静态资源，如有需要请提出Issues。

**注意：提交rp和Issues请到上方项目地址(GitHub仓库)提交！！**

***

特性：

* 全球**IPv6**支持(2022/1/15 16:50)

* 境内外**全球**加速

* 拥有热备源站，可用性高

* 境内**腾讯云**境外**Cloudflare PRO**，CDN优质强悍

* HTTPS支持

* 支持OCSP装订

* 启用HSTS

* 境内支持HTTP/2境外支持**HTTP/3**

* 允许**全部**跨域访问

* 带宽十分充足

***

##   当前大事记

1.由于**国内CDN提供商**方面问题，**国内线路**出现短暂问题，现已将国内流量切至华为云CDN。(22/1/21 16:10)

2.正在于华为云协商IPv6的事宜，现在国内~~仅支持IPv4~~。(已经支持IPv6 22/01/21 16:34:10)

3.为快速响应单一服务商异常，正在准备CDP的配置。

***

常见资源使用方法

在未特殊说明的情况下均可以通过`https://jsd.sorkai.com/`+本项目中的路径进行使用

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

***

### 插件静态资源加速

**需要更新插件请提Issues**

#### DPlayer

当前版本：1.1.0

使用方法：

下载修改过的插件并安装其可自动启用，或自行修改插件中php文件内使用的静态资源地址

下载地址：[/web/plugins/1-package/DPlayer_1.1.0.zip](/web/plugins/1-package/DPlayer_1.1.0.zip)

#### JWPlayer

当前版本：1.0.9

使用方法：

下载修改过的插件并安装其可自动启用，或自行修改插件中php文件内使用的静态资源地址

下载地址：[/web/plugins/1-package/JWPlayer_1.0.9.zip](/web/plugins/1-package/JWPlayer_1.0.9.zip)

#### ColorfulTags

当前版本：1.6

使用方法：

下载修改过的插件并安装其可自动启用，或自行修改插件中php文件内使用的静态资源地址

下载地址：[/web/plugins/1-package/ColorfulTags_1.6.zip](/web/plugins/1-package/ColorfulTags_1.6.zip)

#### ColorfulTags

当前版本：1.1.5b2

使用方法：

下载修改过的插件并安装其可自动启用，或自行修改插件中php文件内使用的静态资源地址

下载地址：[/web/plugins/1-package/AccessoriesPro_1.1.5b2.zip](/web/plugins/1-package/AccessoriesPro_1.1.5b2.zip)

### 主题其他静态资源

#### 前言

**对主题中的文件进行修改需要每次更新主题均进行一次**

**推荐将主题公共CDN库设置为字节跳动CDN！！！**

方法：在 **控制台**-**外观**-**设置外观**-**速度优化**-**选择公共CDN库** 中选择 **字节跳动CDN**

![](https://img.kai233.top/picgo/202201151752328.png)

#### mathjax

当前版本：3.2.0

使用方法：

将`/usr/themes/handsome/libs/CDN.php`中你正在使用的公共CDN库中的`"mathjax_svg"`的值改为`"https://jsd.sorkai.com/web/npm/mathjax@3/es5/tex-mml-chtml.min.js"`如图第166行(如果使用其它公共CDN行号会不同，请自行判断)↓

![](https://img.kai233.top/picgo/202201151748133.png)

#### vditor

当前版本：3.8.10

历查看历史版本：[/web/npm](/web/npm)  中搜索 vditor

使用方法：

将`/usr/themes/handsome/libs/CDN.php`中你正在使用的公共CDN库中的`"vditor"`的值改为`"https://jsd.sorkai.com/web/npm/vditor@3.8.10"`如图第167行(如果使用其它公共CDN行号会不同，请自行判断)↓

![](https://img.kai233.top/picgo/202201151800526.png)

### 其他资源支持

#### Andela

Andela Website Project

所处位置：[/web/gh/Andela-master](/web/gh/Andela-master)

#### mikutap

A Mainland China friendly and indenpendent version extracted from https://aidn.jp/mikutap

所处位置：[/web/others/mikutap](/web/others/mikutap)

使用方法：

* 直接访问：[https://jsd.sorkai.com/web/others/mikutap/index.html](https://jsd.sorkai.com/web/others/mikutap/index.html)

* 私有部署：下载资源包部署至服务器，因为所有资源均可从本项目加载，所以理论只用部署`index.html`即可

  资源包下载：[/web/others/mikutap/mikutap.zip](/web/others/mikutap/mikutap.zip)

#### 一个好看的后台登录页面

不记得原项目在哪了，欢迎补充

![](https://img.kai233.top/picgo/202201151948366.png)

所处位置：[web/admin](web/admin)

使用方法：下载 [/web/admin/login.php](/web/admin/login.php) 放到 Typecho 的 `/admin` 目录中覆盖
