<script src="b2quicktags.js" language="JavaScript" type="text/javascript"></script>

<input type="button" class="quicktags" accesskey="b" name="addbbcode0" value=" B " style="font-weight:bold; width: 30px" onclick="bbstyle(this.form,0)" />

<input type="button" class="quicktags" accesskey="i" name="addbbcode2" value=" i " style="font-style:italic; width: 30px" onclick="bbstyle(this.form,2)" />

<input type="button" class="quicktags" accesskey="u" name="addbbcode4" value=" u " style="text-decoration: underline; width: 30px" onclick="bbstyle(this.form,4)" />

<input type="button" class="quicktags" accesskey="s" name="addbbcode6" value="strike" style="text-decoration: line-through;width: 50px" onclick="bbstyle(this.form,6)" />

<input type="button" class="quicktags" accesskey="p" name="addbbcode10" value="&lt;p>" style="width: 40px" onclick="bbstyle(this.form,10)" />

<?php if (basename($HTTP_SERVER_VARS['SCRIPT_FILENAME']) != 'b2bookmarklet.php') { ?>
<input type="button" class="quicktags" accesskey="l" name="addbbcode12" value="&lt;li>" style="width: 40px" onclick="bbstyle(this.form,12)" />
<?php } ?>
<?php if (basename($HTTP_SERVER_VARS['SCRIPT_FILENAME']) != 'b2bookmarklet.php') { ?>
<input type="button" class="quicktags" accesskey="q" name="addbbcode8" value="b-quote" style="width: 60px" onclick="bbstyle(this.form,8)" />
<?php } ?>

<input type="button" class="quicktags" accesskey="m" name="addbbcode14" value="image" title="Insert an image" style="width: 40px"  onclick="bblink(this.form,14)" />

<input type="button" class="quicktags" accesskey="h" name="addbbcode16" value="link" title="Insert a link" style="text-decoration: underline; width: 40px" onclick="bblink(this.form,16)" />

<input type="button" class="quicktags" accesskey="c" name="closetags" value="X" title="Close all tags" style="width: 30px; font-weigh: bolder;"  onclick="bbstyle(document.post,-1)" />
