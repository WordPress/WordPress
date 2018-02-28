<footer>
            <section class="bg-blue section-wrap">
                <div class="footer">
                    <div class="footer-contact">
                        <div class="footer-sales">
                            <h4><?php pll_e('Email:'); ?></h4>
                            <p><a href="mailto:info@simright.com">info@simright.com</a></p>
                        </div>
                    </div>
                    <div class="footer-products">
                        <h4><?php pll_e('Products'); ?></h4>
                        <p><a href="/apps/simright-simulator"><i class="fa fa-angle-right"></i><b>Simulator</b></a></li>
                        <p><a href="/apps/simright-toptimizer"><i class="fa fa-angle-right"></i><b>Toptimizer</b></a></li>
                        <p><a href="/apps/simright-webmesher"><i class="fa fa-angle-right"></i><b>WebMesher</b></a></li>
                        <p><a href="/apps/simright-viewer"><i class="fa fa-angle-right"></i><b>Viewer</b></a></li>
                        <p><a href="/apps/simright-converter"><i class="fa fa-angle-right"></i><b>CAE Converter</b></a></li>
                        <p><a href="/apps/simright-cad-converter"><i class="fa fa-angle-right"></i><b>CAD Converter</b></a></li>
                    </div>
                    <div class="footer-follow">
                        <h4>Follow Us</h4>
                        <div>
                            <a href="https://www.facebook.com/Simright-1913454768934744" target="_blank"><img src="https://oss.simright.com/images/contact-facebook.svg" alt="Facebook"></a>
                            <a href="https://twitter.com/simright" target="_blank"><img src="https://oss.simright.com/images/contact-twiiter.svg" alt="Twitter"></a>
                            <a href="//shang.qq.com/wpa/qunwpa?idkey=15506675f16f72139d7c9632c62ac0f567fde75cdad3570137f31ac7bfce7624" target="_blank"><img src="https://oss.simright.com/images/contact-qq.svg" alt="QQ"></a>
                            <a href="http://weibo.com/u/6210090560" target="_blank"><img src="https://oss.simright.com/images/contact-weibo.svg" alt="Weibo"></a>
                            <a href=" https://www.linkedin.com/company-beta/13345726/" target="_blank"><img src="https://oss.simright.com/images/contact-insgram.svg" alt="In"></a>
                            <a href="http://www.toutiao.com/c/user/59020482121/" target="_blank"><img src="https://oss.simright.com/images/contact-toutiao.svg" alt="Toutiao"></a>
                            <a href="http://i.youku.com/simright" target="_blank"><img src="https://oss.simright.com/images/contact-youku.svg" alt="Youku"></a>
                            <a href="https://www.youtube.com/channel/UCEiDfhLf7jFWJkPhhdwpW3g" target="_blank"><img src="https://oss.simright.com/images/contact-youtube.svg" alt="Youtube"></a>
                        </div>
                        <?php $current_lan = pll_current_language(); if($current_lan == 'en'): ?>
                            <img src="https://oss.simright.com/images/rq_code.png" alt="" class="wechat_qr_code hide ">
                        <?php else : ?>
                            <img src="https://oss.simright.com/images/rq_code.png" alt="" class="wechat_qr_code ">
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <section class="clearfix copyright">
                <p class="text-center">
                    Copyright © 2018 Simright Information Technology Co.,Ltd. All Rights Reserved.
                <p class="text-center">Powered By <a rel="external" title="WordPress" class="link" href="http://wordpress.org/">WordPress</a></p>
            </section>
        </footer>
    </body>
<script>
    document.documentElement.style.fontSize = document.documentElement.clientWidth / 6.4 + 'px';
</script>
<script src="https://oss.simright.com/static/jquery.min.js"></script>
<script src="https://oss.simright.com/static/bootstrap.min.js"></script>
<script>
$(document).on('click','#pagination a:not(.noajx)',function(){
    var _this = $(this);
    var next = _this.attr("href").replace('?ajx=container','');
    var docH = $(document).height();
    var pagination = '#pagination'; // 下一页按钮标签id
    $(pagination).hide();
    $("#loadmore").show();
    $.ajax({
        url: next,
        beforeSend: function(){
            //
        },
        success: function (data) {
			if($('.blog-list .contain-box').length){
				$('.blog-list .contain-box').append($(data).find('.blog-item-wrap'));    //追加博客内容
			}else if($('.news-list .contain-box').length){
				$('.news-list .contain-box').append($(data).find('.news-item-wrap'));    //追加新闻内容
			}else if($('.joinus-list .post-list').length){
				$('.joinus-list .post-list').append($(data).find('.joinus-item'));    //追加加入我们内容
			}else if($('.video-list .list-contain').length){
				$('.video-list .list-contain').append($(data).find('.video-item'));    //追加视频内容
			}
            $(pagination).html( $(data).find(pagination).html() );    //更新分页导航
            // 后退前进处理
            //var state = {url: next,title: $(data).find("title").text(),html: $(data).find('#ajx_content').html()};
            //window.history.pushState(state,"", next);
            //$('html, body').animate({scrollTop: docH-280}, 500);    //上滚
            nextHref = $(data).find("#pagination a").attr("href");
            if ( nextHref != undefined ) {
                $(pagination).show();
                $("#loadmore").hide();
                $("#pagination a").attr("href", nextHref);
            } else {
                $(pagination).show();
                $("#loadmore").hide();
                $(pagination).html('<a href="javascript:;" class="noajx">这是最后一页了！</a>');    //最后一页
            }
        },
        complete: function(){ // 回调
            //
        },
        error: function() { // 错误时的处理
            location.href = next; //页面错误时跳转到请求的页面
        }
    });
    return false;
});
</script>
</html>
