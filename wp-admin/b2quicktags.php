<script src="b2quicktags.js" language="JavaScript" type="text/javascript">
</script><table border="0" cellspacing="0" cellpadding="0">
<tr align="center" valign="middle">
<td>
<input type="button" class="quicktags" accesskey="b" name="addbbcode0" value=" B " style="font-weight:bold; width: 30px" onClick="bbstyle(this.form,0)" />
</td>
<td>
<input type="button" class="quicktags" accesskey="i" name="addbbcode2" value=" i " style="font-style:italic; width: 30px" onClick="bbstyle(this.form,2)" />
</td>
<td>
<input type="button" class="quicktags" accesskey="u" name="addbbcode4" value=" u " style="text-decoration: underline; width: 30px" onClick="bbstyle(this.form,4)" />
</td>
<td>
<input type="button" class="quicktags" accesskey="s" name="addbbcode6" value="strike" style="text-decoration: line-through;width: 50px" onClick="bbstyle(this.form,6)" />
</td>
<td>
<input type="button" class="quicktags" accesskey="p" name="addbbcode10" value="&lt;p>" style="width: 40px" onClick="bbstyle(this.form,10)" />
</td>
<?php if (basename($HTTP_SERVER_VARS["SCRIPT_FILENAME"]) != "b2bookmarklet.php") { ?><td>
<input type="button" class="quicktags" accesskey="l" name="addbbcode12" value="&lt;li>" style="width: 40px" onClick="bbstyle(this.form,12)" />
</td><?php } ?>
<?php if (basename($HTTP_SERVER_VARS["SCRIPT_FILENAME"]) != "b2bookmarklet.php") { ?><td>
<input type="button" class="quicktags" accesskey="q" name="addbbcode8" value="b-quote" style="width: 60px" onClick="bbstyle(this.form,8)" />
</td><?php } ?>
<td>
<input type="button" class="quicktags" accesskey="m" name="addbbcode14" value="image" title="insert an image" style="width: 40px"  onClick="bblink(this.form,14)" />
</td>
<td>
<input type="button" class="quicktags" accesskey="h" name="addbbcode16" value="link" title="insert a link" style="text-decoration: underline; width: 40px" onClick="bblink(this.form,16)" />
</td><td>
<input type="button" class="quicktags" accesskey="c" name="closetags" value="X" title="Close all tags" style="width: 30px; font-weigh: bolder;"  onClick="bbstyle(document.post,-1)" /></td>
</tr>
</table>