<div class="area">
  <form action="{url('User', 'DoRegister')}" method="post"  enctype="multipart/form-data" name="theform" id="theform">
    <table border="0" cellpadding="0" cellspacing="0" class="listtable">
      <caption>
      用户注册
      </caption>
      <tbody class="stripe">
        <tr width="60">
          <td>用户名</td>
          <td><input name="username" type="text" size="80" maxlength="100" /></td>
        </tr>
        <tr>
          <td>手机</td>
          <td><input name="modile" type="text" size="80" maxlength="100" /></td>
        </tr>
        <tr>
          <td>邮箱</td>
          <td><input name="email" type="text" size="80" maxlength="100" /></td>
        </tr>
        <tr>
          <td>密码</td>
          <td><input name="password" type="text" size="80" maxlength="100" /></td>
        </tr>
        <tr>
          <td>确认密码</td>
          <td><input name="repassword" type="text" size="50" maxlength="20" /></td>
        </tr>
      </tbody>
    </table>
    <div class="button_box">
      <input class="btn" name="dosubmit" type="submit" value="提交" />
    </div>
  </form>
</div>