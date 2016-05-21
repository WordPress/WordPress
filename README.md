# WORDPRESS 中文版说明
- 优美的个人信息发布平台

---

## 写在最前

欢迎。WordPress对我来说是一个具有特殊意义的项目。大家都能为WordPress添砖加瓦，因此作为其中一员我十分自豪。开发者和贡献者为WordPress奉献了难以估量的时间，我们都在致力于让WordPress更加优秀。现在，感谢您也参与其中。

— Matt Mullenweg

## 安装：著名的五分钟安装

1. 将WordPress压缩包解压至一个空文件夹，并上传它。
2. 在浏览器中访问wp-admin/install.php。它将帮助您把数据库连接信息写入到wp-config.php文件中。
  1. 如果上述方法无效，也没关系，这很正常。请用文本编辑器（如写字板）手动打开wp-config-sample.php文件，填入数据库信息。
  2. 将文件另存为wp-config.php并上传。
  3. 在浏览器中访问wp-admin/install.php。
3. 在配置文件就绪之后，WordPress会自动尝试建立数据库表。若发生错误，请检查wp-config.php文件中填写的信息是否准确，然后再试。若问题依然存在，请访问中文支持论坛寻求帮助。
4. 若您不设置密码，请牢记生成的随机密码。若您不输入用户名，用户名将是admin。
5. 完成后，安装向导会带您到登录页面。用刚刚设置的用户名和密码登录。若您使用随机密码，在登录后可以按照页面提示修改密码。

## 升级
### 自动升级
若您正在使用WordPress 2.7或以上版本，您可使用内置的自动升级工具进行升级：
1. 在浏览器中打开wp-admin/update-core.php，按照提示操作。
2. 还有别的步骤么？——没了！

### 手动升级
1. 在升级之前，请确保备份旧有数据以及被您修改过的文件，例如index.php。
2. 删除旧版程序文件，记得备份修改过的内容。
3. 上传新版程序文件。
4. 在浏览器中访问/wp-admin/upgrade.php。

## 从其他内容管理系统“搬家”
WordPress支持导入多种系统的数据。请先按照上述步骤安装WordPress，然后您可在后台使用我们提供的导入工具。

## 最低系统需求
- PHP 5.2.4或更高版本。
- MySQL 5.0或更高版本。

## 系统推荐
- 启用mod_rewrite这一Apache模块。
- 在您的站点设置至 http://cn.wordpress.org 的链接。
- Apache模块mod_rewrite。
- 在您的站点上放置一个到wordpress.org的链接。

## 在线资源
若您遇上文档中未有提及的情况，请首先参考我们为您准备的丰富的WordPress在线资源：

> [WordPress Codex文档]()<br>
  * Codex是WordPress的百科全书，它包含现有版本WordPress的海量信息资源，主要文章均有中文译文。<br>
  
> [WordPress官方博客]()<br>
  * 在这里，您将接触到WordPress的最新升级信息和相关新闻，建议加入收藏夹。<br>
  
> [WordPress Planet]()<br>
  * WordPress Planet汇集了全球所有WordPress相关的内容。<br>
  
> [WordPress中文支持论坛]()<br>
 如果感到束手无策，请将问题提交至中文支持论坛，它有大量的热心的用户和良好的社区氛围。无论求助还是助人，在这里您应该确保自己的问题和答案均准确细致。<br>
 
> [WordPress IRC频道]()<br>
 同样，WordPress也有即时的聊天室用于WordPress用户交流以及部分技术支持。IRC的详细使用方法可以访问前面几个关于技术支持的站点。（irc.freenode.net #wordpress）<br>

## 最后
- 对WordPress有任何建议、想法、评论或发现了bug，请加入中文支持论坛。
- WordPress准备了完善的插件API接口方便您进行扩展开发。作为开发人员，如果你有兴趣了解并加以利用，请参阅Codex上的插件文档。请尽量不要更改核心代码。

## 分享精神
WordPress没有数百万的市场运作资金，也没有名人赞助。不过我们有更棒的支持，那就是您！如果您喜欢WordPress，请将它介绍给自己的朋友，或者帮助他人安装一个WordPress，又或者写一篇赞扬我们的文章。

WordPress是对Michel V.创建的b2/cafélog的官方后续版本。WordPress开发团队将b2/cafélog发展成如今的WordPress。如果您愿意支持我们的工作，欢迎您对WordPress进行捐赠。

## 许可证
WordPress基于GPL第二版或（根据您选择的）以后版本发布。详见license.txt（英文）。

