<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>华影文化管理后台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="styles/general.css" rel="stylesheet" type="text/css" />
<link href="styles/main.css" rel="stylesheet" type="text/css" />

<style type="text/css">
body {
  color: white;
}
</style>

<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>
<script language="JavaScript">
<!--
// 这里把JS用到的所有语言都赋值到这里
<?php $_from = $this->_var['lang']['js_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

if (window.parent != window)
{
  window.top.location.href = location.href;
}

//-->
</script>
</head>
<body style="background: #278296">
<form method="post" action="privilege.php" name='theForm' onsubmit="return validate()">
  <table cellspacing="0" cellpadding="0" style="margin-top: 100px" align="center">
  <tr>
      <td style="padding-left: 50px">
      <table>
      <tr>
        <td width="80"><?php echo $this->_var['lang']['label_username']; ?></td>
        <td><input type="text" name="username" /></td>
      </tr>
      <tr>
        <td><?php echo $this->_var['lang']['label_password']; ?></td>
        <td><input type="password" name="password" /></td>
      </tr>
      <?php if ($this->_var['gd_version'] > 0): ?>
      <tr>
        <td><?php echo $this->_var['lang']['label_captcha']; ?></td>
        <td><input type="text" name="captcha" class="capital" /></td>
      </tr>
      <tr>
	  
         <td></td>
      <td align="right"><img src="index.php?act=captcha&<?php echo $this->_var['random']; ?>" width="145" height="20" alt="CAPTCHA" border="1" onclick= this.src="index.php?act=captcha&"+Math.random() style="cursor: pointer;" title="<?php echo $this->_var['lang']['click_for_another']; ?>" />
      </td>
      </tr>
      <?php endif; ?>
      <!--<tr><td ><input type="checkbox" value="1" name="remember" id="remember" /><label for="remember"><?php echo $this->_var['lang']['remember']; ?></label></td></tr>-->
      <tr><td></td><td><input type="submit" value="<?php echo $this->_var['lang']['signin_now']; ?>" class="button" /></td></tr>
     
      </table>
    </td>
  </tr>
  </table>
  <input type="hidden" name="act" value="signin" />
</form>
<script language="JavaScript">
<!--
  document.forms['theForm'].elements['username'].focus();
  
  /**
   * 检查表单输入的内容
   */
  function validate()
  {
    var validator = new Validator('theForm');
    validator.required('username', user_name_empty);
    //validator.required('password', password_empty);
    if (document.forms['theForm'].elements['captcha'])
    {
      validator.required('captcha', captcha_empty);
    }
    return validator.passed();
  }
  
//-->
</script>
</body>