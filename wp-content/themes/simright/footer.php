        <footer>
            <section class="bg-blue section-wrap">
                <div class="footer">
                    <div class="footer-contact">
                        <div class="footer-sales">
                            <h4><?php pll_e('Sales:'); ?></h4>
                            <p><a href="mailto:sales@simright.com">sales@simright.com</a></p>
                        </div>
                        <div>
                            <h4><?php pll_e('Technical Support:'); ?></h4>
                            <p><a href="mailto:support@simright.com">support@simright.com</a></p>
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
</html>
