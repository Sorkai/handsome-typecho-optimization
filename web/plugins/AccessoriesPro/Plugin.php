<?php

/**
 * 附件下载插件 <span style="color: #fff; background-color: red; font-weight: bold; padding: 3px 5px; margin: 0 5px;">Pro</span>
 *
 * @package AccessoriesPro
 * @author Ryan
 * @version 1.1.5b2
 * @dependence 9.9.2-*
 * @link https://xiamp.net/archives/accessories-pro.html
 *
 *
 */
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
class AccessoriesPro_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('admin/write-post.php')->bottom = array(__CLASS__, 'bottomJS');
        Typecho_Plugin::factory('admin/write-page.php')->bottom = array(__CLASS__, 'bottomJS');
        Typecho_Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'footer');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->filter = array(__CLASS__, 'disableComment');
        AccessoriesPro_Util::activate();
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        AccessoriesPro_Util::deactivate();
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {

        $db = Typecho_Db::get();
        $notice = Typecho_Widget::widget('Widget_Notice');
        $response = Typecho_Response::getInstance();
        $plugin = 'AccessoriesPro';
        // 查询主题数据
        $pluginDataRow = $db->fetchRow($db->select()->from('table.options')->where('name = ?', "plugin:${plugin}"));
        $pluginDataBackupRow = $db->fetchRow($db->select()->from('table.options')->where('name = ?', "plugin:${plugin}Backup"));
        $pluginData = empty($pluginDataRow) ? null : $pluginDataRow['value'];
        $pluginDataBackup = empty($pluginDataBackupRow) ? null : $pluginDataBackupRow['value'];

        // 备份还原数据
        if (isset($_POST['type'])) {
            if ($_POST["type"] == "备份插件数据") {
                if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', "plugin:${plugin}Backup"))) {
                    $updateQuery = $db->update('table.options')->rows(array('value' => $pluginData))->where('name = ?', "plugin:${plugin}Backup");
                    $db->query($updateQuery);
                    $notice->set('备份已更新!', 'success');
                    $response->goBack();
                } else {
                    if ($pluginData) {
                        $insertQuery = $db->insert('table.options')->rows(array('name' => "plugin:${plugin}Backup", 'user' => '0', 'value' =>  $pluginData));
                        $db->query($insertQuery);
                        $notice->set('备份完成!', 'success');
                        $response->goBack();
                    }
                }
            } elseif ($_POST["type"] == "还原插件数据") {
                if ($pluginDataBackup) {
                    $updateQuery = $db->update('table.options')->rows(array('value' => $pluginDataBackup))->where('name = ?', "plugin:${plugin}");
                    $db->query($updateQuery);
                    $notice->set('检测到插件备份数据，恢复完成', 'success');
                    $response->goBack();
                } else {
                    $notice->set('没有插件备份数据，恢复不了哦！', 'error');
                    $response->goBack();
                }
            } elseif ($_POST["type"] == "删除备份数据") {
                if ($pluginDataBackup) {
                    $deleteQuery = $db->delete('table.options')->where('name = ?', "plugin:${plugin}Backup");
                    $db->query($deleteQuery);
                    $notice->set('删除成功！！！', 'success');
                    $response->goBack();
                } else {
                    $notice->set('不用删了！备份不存在！！！', 'error');
                    $response->goBack();
                }
            }
        }

        /**
         *  设置样式+面板
         */

        $form->addItem(new AcLabel('<style>body{background-color:#eee !important;}.container{width:100%;max-width:unset;padding:0;}.main{width:100%;margin:0;padding:0;overflow:hidden;}pre{padding:1em;color:#000;background-color:#eee;font-size:12px;word-wrap:break-word;white-space:normal;}code{padding:2px;margin:0 5px;}.typecho-page-title,.typecho-option-tabs.fix-tabs{display:none;}.theme-settings{margin:1em 0;background-color:#fff;border-radius:5px;}.theme-settings h2{margin:0;background-color:#9c4dff;background-image:-webkit-linear-gradient(0,#9c4dff 0%,#42a7ff 100%);background-image:-o-linear-gradient(0,#9c4dff 0%,#42a7ff 100%);background-image:-moz-linear-gradient(0,#9c4dff 0%,#42a7ff 100%);background-image:linear-gradient(90deg,#9c4dff 0%,#42a7ff 100%);border-radius:5px;}.theme-settings h2 span{display:inline-block;padding:0.5em;position:relative;color:#fff;}.theme-settings h3{margin:15px;font-size:1.3em;}.xiamp-pannel{background:#0185ba;padding:28px 30px;margin:40px 0;margin-top:10px;color:#fff;box-shadow:0 2px 8px #ddd;-moz-box-shadow:0 2px 8px #ddd;-webkit-box-shadow:0 2px 8px #ddd;}.xiamp-pannel h1{margin-bottom:10px;}.xiamp-pannel hr{border:none;border-bottom:1px solid #fff;margin:10px 0;}.xiamp-pannel p{margin:5px 0;font-size:14px;letter-spacing:1px;}.xiamp-pannel .protected{margin:10px 0 10px 0;}.xiamp-pannel a{color:#fafafa;position:relative}.xiamp-pannel a:hover{text-decoration:none}.xiamp-pannel a::before{content:"";position:absolute;bottom:0;left:0;right:0;height:2px;background-color:#fafafa;transform-origin:bottom right;transform:scaleX(0);transition:transform .5s ease}.xiamp-pannel a:hover::before{transform-origin:bottom left;transform:scaleX(1)}.xiamp-backup-alert{transition:all .2s;position:fixed;top:0;left:0;right:0;padding:15px 0;background:#4d90fe;text-align:center}.xiamp-backup-alert,.xiamp-backup-alert a{color:#fff}.xiamp-pannel li{list-style-type:none;border-bottom:1px dashed #eee;font-size:13px;line-height:30px;}.xiamp-backup-button{margin:15px 0 15px 15px;padding:6px 10px;border-radius:2px;color:#fff;background:#020202;box-shadow:0 1px 1px #000;-moz-box-shadow:0 1px 1px #000;-webkit-box-shadow:0 1px 1px #000;border:none;cursor:pointer;}[action="?xiampBackup"]{margin-top:-10px}.xiamp-notice{display:block;}.xiamp-para-attention{padding:10px 12px;background:red;color:#fff;border-radius:3px}.xiamp-para-success{padding:10px 12px;background:green;color:#fff;border-radius:3px}.typecho-option{margin:0;padding:1em;}.theme-settings h3+.group2>ul{padding-top:0;}.group2{display:block;}.typecho-option .typecho-label{display:block;margin:0;margin-bottom:0px;font-weight:bold;padding:0;margin-bottom:0.5em;font-size:1.2em;font-weight:500 !important;}.typecho-option .description{font-size:1em;clear:both;}.typecho-option.checkbox .multiline{width:50%;float:left;}@media (min-width:48em){.typecho-option.oneline li{display:flex;align-items:center;}.typecho-option.oneline li .typecho-label,.typecho-option.oneline li .description{white-space:nowrap;margin:0;margin-right:5px;padding:0;}.typecho-option.oneline li input{margin:0 0.5em;border:0;border-bottom:1px solid #d9d9d6;border-radius:0;}}@media (min-width:60em){.group2{display:flex;}.group2 .typecho-option{width:50%;}}</style><div class="xiamp-pannel">
        <h1>AccessoriesPro 设置面板</h1>
        <p>AccessoriesPro 为 Accessories 的付费增强版本，是资源类博客最佳助手，功能持续更新。</p>
        <hr>' . ($pluginDataBackup ? '<span class="xiamp-notice xiamp-para-success">存在备份数据' : '<span class="xiamp-notice xiamp-para-attention">无备份数据') . '
            <input type="submit" name="type" class="xiamp-backup-button backup" value="备份插件数据" />
            <input type="submit" name="type" class="xiamp-backup-button recover" value="还原插件数据" />
            <input type="submit" name="type" class="xiamp-backup-button delete" value="删除备份数据" />
          </span>
        </div>'));

        $form->addItem(new AcLabel('<div class="theme-settings"><h2 id="div-1"><span>常规设置</span></h2>'));

        $radio = new Typecho_Widget_Helper_Form_Element_Radio('enableImageAttachPage', array('1' => _t('开启'), '0' => _t('关闭')), '1', _t('开启图片附件页面'), _t('关闭后图片附件页面将无法访问'));
        $radio->setAttribute('class', 'typecho-option oneline');
        $form->addInput($radio);

        $radio = new Typecho_Widget_Helper_Form_Element_Radio('enableComments', array('1' => _t('开启'), '0' => _t('关闭')), '0', _t('是否开启附件页面评论功能'), _t('默认为关闭'));
        $radio->setAttribute('class', 'typecho-option oneline');
        $form->addInput($radio);

        $textarea = new Typecho_Widget_Helper_Form_Element_Textarea('attachAdvertisement', null, null, _t('默认广告位'), _t('HTML代码'));
        $form->addInput($textarea);

        $textarea = new Typecho_Widget_Helper_Form_Element_Textarea('attachNetdiskList', null, "baidu.com=>百度网盘\ncloud.189.cn=>天翼云盘\nlanzou.com=>蓝奏云盘\ncowtransfer.com=>奶牛快传", _t('网盘列表'), _t('不在此列表的网盘显示为其他网盘'));
        $textarea->setAttribute('style', 'display:none');
        $form->addInput($textarea);

        $form->addItem(new AcLabel('<h2><span>模板设置</span></h2>'));

        $radio = new Typecho_Widget_Helper_Form_Element_Radio('enableBuildInCss', array('1' => _t('开启'), '0' => _t('关闭')), '1', _t('加载插件自带附件 CSS 样式'), _t('关闭后需要自行增加附件 CSS 样式'));
        $radio->setAttribute('class', 'typecho-option oneline');
        $form->addInput($radio);

        $form->addItem(new AcLabel('<div class="group2">'));

        $textarea = new Typecho_Widget_Helper_Form_Element_Textarea('attachTemplateInPost', null, '<div class="accessories-block"><div class="accessories-notice {attachmentType}" title="AccessoriesPro">{attachmentTypeTitle}</div><div class="accessories-promo">{advertisement}</div><div class="accessories-content"><div class="accessories-filename"><div class="img" title="AccessoriesPro"></div>附件名称：{attachmentName}</div><div class="accessories-filesize"><div class="img"></div>文件大小：{attachmentSize} KB</div><div class="accessories-count"><div class="img"></div>下载次数：{attachmentDownloadsCount}</div><div class="accessories-filemodified"><div class="img"></div>上次修改：{attachmentModified}</div><div class="accessories-button-group"><a class="accessories-button" href="{permalink}">浏览详情</a></div></div></div>', _t('普通文章正文普通/外链/网盘附件HTML结构'), _t('在这里设置普通正文中插入的附件样式代码，<code>{关键字}</code>是替换字段'));
        $form->addInput($textarea);

        $textarea = new Typecho_Widget_Helper_Form_Element_Textarea('errorAttachTemplateInPost', null, '<div class="accessories-block"><div class="accessories-notice error" title="AccessoriesPro">错误</div><div class="accessoreis-error"><div class="img"></div>附件 ID 错误</div></div>', _t('普通文章正文错误附件ID HTML结构'), _t('在这里设置普通正文中插入的附件样式代码，<code>{关键字}</code>是替换字段'));
        $form->addInput($textarea);

        $form->addItem(new AcLabel('</div>'));
        $form->addItem(new AcLabel('<div class="group2">'));

        $textarea = new Typecho_Widget_Helper_Form_Element_Textarea('attachTemplateImage', null, '<div class="accessories-block"><div class="accessories-notice" title="AccessoriesPro">图片</div><div class="accessories-promo">{advertisement}</div><div class="accessories-content"><div class="accessories-filename"><div class="img" title="AccessoriesPro"></div>附件名称：{attachmentName}</div><div class="accessories-filesize"><div class="img"></div>文件大小：{attachmentSize} KB</div><div class="accessories-count"><div class="img"></div>下载次数：{attachmentDownloadsCount}</div><div class="accessories-filemodified"><div class="img"></div>上次修改：{attachmentModified}</div><div class="accessories-button-group"><a class="accessories-button" href="{attachmentDownloadLink}">点击下载</a></div></div><div class="accessories-image"><img title="{attachmenTitle}" src="{attachmentUrl}" /></div></div>', _t('附件文章正文图片附件HTML结构'), _t('在这里设置附件正文中图片附件样式HTML代码，<code>{关键字}</code>是替换字段'));
        $form->addInput($textarea);

        $textarea = new Typecho_Widget_Helper_Form_Element_Textarea('attachTemplateNonImage', null, '<div class="accessories-block"><div class="accessories-notice {attachmentType}" title="AccessoriesPro">{attachmentTypeTitle}</div><div class="accessories-promo">{advertisement}</div><div class="accessories-content"><div class="accessories-filename"><div class="img" title="AccessoriesPro"></div>附件名称：{attachmentName}</div><div class="accessories-filesize"><div class="img"></div>文件大小：{attachmentSize} KB</div><div class="accessories-count"><div class="img"></div>下载次数：{attachmentDownloadsCount}</div><div class="accessories-filemodified"><div class="img"></div>上次修改：{attachmentModified}</div><div class="accessories-button-group"><a class="accessories-button" href="{attachmentDownloadLink}">点击下载</a></div></div></div>', _t('附件文章正文非图片附件HTML结构'), _t('在这里设置附件正文中非图片附件样式HTML代码，<code>{关键字}</code>是替换字段'));
        $form->addInput($textarea);
        $form->addItem(new AcLabel('</div>'));
        $form->addItem(new AcLabel('<div class="group2">'));
        $textarea = new Typecho_Widget_Helper_Form_Element_Textarea('attachTemplateNetdisk', null, '<div class="accessories-block"><div class="accessories-notice {attachmentType}" title="AccessoriesPro">{attachmentTypeTitle}</div><div class="accessories-promo">{advertisement}</div><div class="accessories-content"><div class="accessories-filename"><div class="img" title="AccessoriesPro"></div>附件名称：{attachmentName}</div><div class="accessories-count"><div class="img"></div>下载次数：{attachmentDownloadsCount}</div><div class="accessories-filemodified"><div class="img"></div>上次修改：{attachmentModified}</div><div class="accessories-password"><div class="img"></div>提取码：{attachmentNetdiskPassword}</div><div class="accessories-button-group"><a class="accessories-button" href="{attachmentDownloadLink}">点击下载</a></div></div></div>', _t('附件文章正文网盘HTML结构'), _t('在这里设置附件正文中网盘附件样式HTML代码，<code>{关键字}</code>是替换字段'));
        $form->addInput($textarea);

        $textarea = new Typecho_Widget_Helper_Form_Element_Textarea('attachReplacement', null, null, _t('辅助功能：特殊替换字段'), _t('一行一个。用 Widget_Options 对象的参数值替换模板中的字段，比如填写了<code>logoUrl</code>，将会替换<code>{logoUrl}</code>为主题设置的Logo地址。主要用于主题适配'));
        $form->addInput($textarea);

        $form->addItem(new AcLabel('</div>'));
        $form->addItem(new AcLabel('</div>'));
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }
    /**
     * 获取安全的文件名
     *
     * @param string $name
     * @static
     * @access public
     * @return string
     */
    public static function getSafeName($name)
    {
        $name = str_replace(array('"', '<', '>'), '', $name);
        $name = str_replace('\\', '/', $name);
        $name = false === strpos($name, '/') ? ('a' . $name) : str_replace('/', '/a', $name);
        $info = pathinfo($name);
        return substr($info['basename'], 1);
    }
    /**
     * 获取安全的后缀
     *
     * @param string $name
     * @static
     * @access public
     * @return string
     */
    public static function getExtension($name)
    {
        $name = str_replace(array('"', '<', '>'), '', $name);
        $name = str_replace('\\', '/', $name);
        $name = false === strpos($name, '/') ? ('a' . $name) : str_replace('/', '/a', $name);
        $info = pathinfo($name);
        return isset($info['extension']) ? strtolower($info['extension']) : '';
    }
    /**
     * 获取 MimeType
     *
     * @param string $fileName
     * @static
     * @access public
     * @return string
     */
    public static function getMimeType($fileName)
    {
        $mimeTypes = array(
            'ez' => 'application/andrew-inset',
            'csm' => 'application/cu-seeme',
            'cu' => 'application/cu-seeme',
            'tsp' => 'application/dsptype',
            'spl' => 'application/x-futuresplash',
            'hta' => 'application/hta',
            'cpt' => 'image/x-corelphotopaint',
            'hqx' => 'application/mac-binhex40',
            'nb' => 'application/mathematica',
            'mdb' => 'application/msaccess',
            'doc' => 'application/msword',
            'dot' => 'application/msword',
            'bin' => 'application/octet-stream',
            'oda' => 'application/oda',
            'ogg' => 'application/ogg',
            'prf' => 'application/pics-rules',
            'key' => 'application/pgp-keys',
            'pdf' => 'application/pdf',
            'pgp' => 'application/pgp-signature',
            'ps' => 'application/postscript',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'rss' => 'application/rss+xml',
            'rtf' => 'text/rtf',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'wp5' => 'application/wordperfect5.1',
            'xht' => 'application/xhtml+xml',
            'xhtml' => 'application/xhtml+xml',
            'zip' => 'application/zip',
            'cdy' => 'application/vnd.cinderella',
            'mif' => 'application/x-mif',
            'xls' => 'application/vnd.ms-excel',
            'xlb' => 'application/vnd.ms-excel',
            'cat' => 'application/vnd.ms-pki.seccat',
            'stl' => 'application/vnd.ms-pki.stl',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pps' => 'application/vnd.ms-powerpoint',
            'pot' => 'application/vnd.ms-powerpoint',
            'sdc' => 'application/vnd.stardivision.calc',
            'sda' => 'application/vnd.stardivision.draw',
            'sdd' => 'application/vnd.stardivision.impress',
            'sdp' => 'application/vnd.stardivision.impress',
            'smf' => 'application/vnd.stardivision.math',
            'sdw' => 'application/vnd.stardivision.writer',
            'vor' => 'application/vnd.stardivision.writer',
            'sgl' => 'application/vnd.stardivision.writer-global',
            'sxc' => 'application/vnd.sun.xml.calc',
            'stc' => 'application/vnd.sun.xml.calc.template',
            'sxd' => 'application/vnd.sun.xml.draw',
            'std' => 'application/vnd.sun.xml.draw.template',
            'sxi' => 'application/vnd.sun.xml.impress',
            'sti' => 'application/vnd.sun.xml.impress.template',
            'sxm' => 'application/vnd.sun.xml.math',
            'sxw' => 'application/vnd.sun.xml.writer',
            'sxg' => 'application/vnd.sun.xml.writer.global',
            'stw' => 'application/vnd.sun.xml.writer.template',
            'sis' => 'application/vnd.symbian.install',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'wmlsc' => 'application/vnd.wap.wmlscriptc',
            'wk' => 'application/x-123',
            'dmg' => 'application/x-apple-diskimage',
            'bcpio' => 'application/x-bcpio',
            'torrent' => 'application/x-bittorrent',
            'cdf' => 'application/x-cdf',
            'vcd' => 'application/x-cdlink',
            'pgn' => 'application/x-chess-pgn',
            'cpio' => 'application/x-cpio',
            'csh' => 'text/x-csh',
            'deb' => 'application/x-debian-package',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dxr' => 'application/x-director',
            'wad' => 'application/x-doom',
            'dms' => 'application/x-dms',
            'dvi' => 'application/x-dvi',
            'pfa' => 'application/x-font',
            'pfb' => 'application/x-font',
            'gsf' => 'application/x-font',
            'pcf' => 'application/x-font',
            'pcf.Z' => 'application/x-font',
            'gnumeric' => 'application/x-gnumeric',
            'sgf' => 'application/x-go-sgf',
            'gcf' => 'application/x-graphing-calculator',
            'gtar' => 'application/x-gtar',
            'tgz' => 'application/x-gtar',
            'taz' => 'application/x-gtar',
            'gz' => 'application/x-gtar',
            'hdf' => 'application/x-hdf',
            'phtml' => 'application/x-httpd-php',
            'pht' => 'application/x-httpd-php',
            'php' => 'application/x-httpd-php',
            'phps' => 'application/x-httpd-php-source',
            'php3' => 'application/x-httpd-php3',
            'php3p' => 'application/x-httpd-php3-preprocessed',
            'php4' => 'application/x-httpd-php4',
            'ica' => 'application/x-ica',
            'ins' => 'application/x-internet-signup',
            'isp' => 'application/x-internet-signup',
            'iii' => 'application/x-iphone',
            'jar' => 'application/x-java-archive',
            'jnlp' => 'application/x-java-jnlp-file',
            'ser' => 'application/x-java-serialized-object',
            'class' => 'application/x-java-vm',
            'js' => 'application/x-javascript',
            'chrt' => 'application/x-kchart',
            'kil' => 'application/x-killustrator',
            'kpr' => 'application/x-kpresenter',
            'kpt' => 'application/x-kpresenter',
            'skp' => 'application/x-koan',
            'skd' => 'application/x-koan',
            'skt' => 'application/x-koan',
            'skm' => 'application/x-koan',
            'ksp' => 'application/x-kspread',
            'kwd' => 'application/x-kword',
            'kwt' => 'application/x-kword',
            'latex' => 'application/x-latex',
            'lha' => 'application/x-lha',
            'lzh' => 'application/x-lzh',
            'lzx' => 'application/x-lzx',
            'frm' => 'application/x-maker',
            'maker' => 'application/x-maker',
            'frame' => 'application/x-maker',
            'fm' => 'application/x-maker',
            'fb' => 'application/x-maker',
            'book' => 'application/x-maker',
            'fbdoc' => 'application/x-maker',
            'wmz' => 'application/x-ms-wmz',
            'wmd' => 'application/x-ms-wmd',
            'com' => 'application/x-msdos-program',
            'exe' => 'application/x-msdos-program',
            'bat' => 'application/x-msdos-program',
            'dll' => 'application/x-msdos-program',
            'msi' => 'application/x-msi',
            'nc' => 'application/x-netcdf',
            'pac' => 'application/x-ns-proxy-autoconfig',
            'nwc' => 'application/x-nwc',
            'o' => 'application/x-object',
            'oza' => 'application/x-oz-application',
            'pl' => 'application/x-perl',
            'pm' => 'application/x-perl',
            'p7r' => 'application/x-pkcs7-certreqresp',
            'crl' => 'application/x-pkcs7-crl',
            'qtl' => 'application/x-quicktimeplayer',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'shar' => 'application/x-shar',
            'swf' => 'application/x-shockwave-flash',
            'swfl' => 'application/x-shockwave-flash',
            'sh' => 'text/x-sh',
            'sit' => 'application/x-stuffit',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc',
            'tar' => 'application/x-tar',
            'tcl' => 'text/x-tcl',
            'tex' => 'text/x-tex',
            'gf' => 'application/x-tex-gf',
            'pk' => 'application/x-tex-pk',
            'texinfo' => 'application/x-texinfo',
            'texi' => 'application/x-texinfo',
            '~' => 'application/x-trash',
            '%' => 'application/x-trash',
            'bak' => 'application/x-trash',
            'old' => 'application/x-trash',
            'sik' => 'application/x-trash',
            't' => 'application/x-troff',
            'tr' => 'application/x-troff',
            'roff' => 'application/x-troff',
            'man' => 'application/x-troff-man',
            'me' => 'application/x-troff-me',
            'ms' => 'application/x-troff-ms',
            'ustar' => 'application/x-ustar',
            'src' => 'application/x-wais-source',
            'wz' => 'application/x-wingz',
            'crt' => 'application/x-x509-ca-cert',
            'fig' => 'application/x-xfig',
            'au' => 'audio/basic',
            'snd' => 'audio/basic',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'kar' => 'audio/midi',
            'mpga' => 'audio/mpeg',
            'mpega' => 'audio/mpeg',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'm3u' => 'audio/x-mpegurl',
            'sid' => 'audio/prs.sid',
            'aif' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'gsm' => 'audio/x-gsm',
            'wma' => 'audio/x-ms-wma',
            'wax' => 'audio/x-ms-wax',
            'ra' => 'audio/x-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'ram' => 'audio/x-pn-realaudio',
            'pls' => 'audio/x-scpls',
            'sd2' => 'audio/x-sd2',
            'wav' => 'audio/x-wav',
            'pdb' => 'chemical/x-pdb',
            'xyz' => 'chemical/x-xyz',
            'bmp' => 'image/x-ms-bmp',
            'gif' => 'image/gif',
            'ief' => 'image/ief',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'pcx' => 'image/pcx',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'wbmp' => 'image/vnd.wap.wbmp',
            'ras' => 'image/x-cmu-raster',
            'cdr' => 'image/x-coreldraw',
            'pat' => 'image/x-coreldrawpattern',
            'cdt' => 'image/x-coreldrawtemplate',
            'djvu' => 'image/x-djvu',
            'djv' => 'image/x-djvu',
            'ico' => 'image/x-icon',
            'art' => 'image/x-jg',
            'jng' => 'image/x-jng',
            'psd' => 'image/x-photoshop',
            'pnm' => 'image/x-portable-anymap',
            'pbm' => 'image/x-portable-bitmap',
            'pgm' => 'image/x-portable-graymap',
            'ppm' => 'image/x-portable-pixmap',
            'rgb' => 'image/x-rgb',
            'xbm' => 'image/x-xbitmap',
            'xpm' => 'image/x-xpixmap',
            'xwd' => 'image/x-xwindowdump',
            'igs' => 'model/iges',
            'iges' => 'model/iges',
            'msh' => 'model/mesh',
            'mesh' => 'model/mesh',
            'silo' => 'model/mesh',
            'wrl' => 'x-world/x-vrml',
            'vrml' => 'x-world/x-vrml',
            'csv' => 'text/comma-separated-values',
            'css' => 'text/css',
            '323' => 'text/h323',
            'htm' => 'text/html',
            'html' => 'text/html',
            'uls' => 'text/iuls',
            'mml' => 'text/mathml',
            'asc' => 'text/plain',
            'txt' => 'text/plain',
            'text' => 'text/plain',
            'diff' => 'text/plain',
            'rtx' => 'text/richtext',
            'sct' => 'text/scriptlet',
            'wsc' => 'text/scriptlet',
            'tm' => 'text/texmacs',
            'ts' => 'text/texmacs',
            'tsv' => 'text/tab-separated-values',
            'jad' => 'text/vnd.sun.j2me.app-descriptor',
            'wml' => 'text/vnd.wap.wml',
            'wmls' => 'text/vnd.wap.wmlscript',
            'xml' => 'text/xml',
            'xsl' => 'text/xml',
            'h++' => 'text/x-c++hdr',
            'hpp' => 'text/x-c++hdr',
            'hxx' => 'text/x-c++hdr',
            'hh' => 'text/x-c++hdr',
            'c++' => 'text/x-c++src',
            'cpp' => 'text/x-c++src',
            'cxx' => 'text/x-c++src',
            'cc' => 'text/x-c++src',
            'h' => 'text/x-chdr',
            'c' => 'text/x-csrc',
            'java' => 'text/x-java',
            'moc' => 'text/x-moc',
            'p' => 'text/x-pascal',
            'pas' => 'text/x-pascal',
            '***' => 'text/x-pcs-***',
            'shtml' => 'text/x-server-parsed-html',
            'etx' => 'text/x-setext',
            'tk' => 'text/x-tcl',
            'ltx' => 'text/x-tex',
            'sty' => 'text/x-tex',
            'cls' => 'text/x-tex',
            'vcs' => 'text/x-vcalendar',
            'vcf' => 'text/x-vcard',
            'dl' => 'video/dl',
            'fli' => 'video/fli',
            'gl' => 'video/gl',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mxu' => 'video/vnd.mpegurl',
            'dif' => 'video/x-dv',
            'dv' => 'video/x-dv',
            'lsf' => 'video/x-la-asf',
            'lsx' => 'video/x-la-asf',
            'mng' => 'video/x-mng',
            'asf' => 'video/x-ms-asf',
            'asx' => 'video/x-ms-asf',
            'wm' => 'video/x-ms-wm',
            'wmv' => 'video/x-ms-wmv',
            'wmx' => 'video/x-ms-wmx',
            'wvx' => 'video/x-ms-wvx',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie',
            'ice' => 'x-conference/x-cooltalk',
            'vrm' => 'x-world/x-vrml',
            'rar' => 'application/x-rar-compressed',
            'cab' => 'application/vnd.ms-cab-compressed',
        );

        $part = explode('.', $fileName);
        $size = count($part);

        if ($size > 1) {
            $ext = $part[$size - 1];
            if (isset($mimeTypes[$ext])) {
                return $mimeTypes[$ext];
            }
        }

        return 'application/octet-stream';
    }
    /**
     * 添加功能JS
     * 点击附件链接自动填写[attach]id[/attach]
     * insertTextAtCursor 来自插件 TePass
     *
     * @return void
     * @date 2020-04-12
     */
    public static function bottomJS()
    {
        $options = Typecho_Widget::widget('Widget_Options');
?>
        <script src="<?php echo Typecho_Common::url('AccessoriesPro/js/jquery.ba-outside-events.js', $options->pluginUrl) ?>"></script>
        <link rel="stylesheet" href="<?php echo Typecho_Common::url('AccessoriesPro/css/admin.css', $options->pluginUrl) ?>">
        <script type="text/javascript">
            // accessories.js 所需变量
            window.add_attach_action = '<?php echo Typecho_Common::url('action/accessoriespro?addAttachment', $options->index); ?>';
            window.get_attach_action = '<?php echo Typecho_Common::url('action/accessoriespro?getAttachment', $options->index); ?>';
            window.set_attach_action = '<?php echo Typecho_Common::url('action/accessoriespro?setAttachment', $options->index); ?>';
            window.del_attach_action = '<?php echo Typecho_Common::url('action/accessoriespro?delAttachment', $options->index); ?>';
            window.get_payment_action = '<?php echo Typecho_Common::url('action/accessoriespro?getPayment', $options->index); ?>';
            window.set_payment_action = '<?php echo Typecho_Common::url('action/accessoriespro?setPayment', $options->index); ?>';
            window.is_tepass_enabled = '<?php echo (array_key_exists('TePass', Typecho_Plugin::export()['activated']) ? 1 : 0); ?>';
            window.adminUrl = '<?php Helper::options()->adminUrl(); ?>';
        </script>
        <script src="https://cdn.jsdelivr.net/gh/wangkai6688/web/plugins/AccessoriesPro/js/accessories.js"></script>
<?php }
    /**
     * 插入前台样式
     */
    public static function footer($archive)
    {
        if (Helper::options()->plugin('AccessoriesPro')->enableBuildInCss) {
            echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/wangkai6688/web/plugins/AccessoriesPro/css/content.css">';
        }
    }
    /**
     * 禁用评论函数
     *
     * @param array $value
     * @return array
     */
    public static function disableComment($value)
    {
        if ($value['type'] !== 'attachment') return $value;
        if (Helper::options()->plugin('AccessoriesPro')->enableComments) {
            $value['allowComment'] = 1;
        } else {
            $value['allowComment'] = 0;
        }
        return $value;
    }
}
class AcLabel extends Typecho_Widget_Helper_Layout
{
    public function __construct($html)
    {
        $this->html($html);
        $this->start();
        $this->end();
    }

    public function start()
    {
    }
    public function end()
    {
    }
}
