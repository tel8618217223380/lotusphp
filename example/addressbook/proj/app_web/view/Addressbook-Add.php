<div class="area">
  <form action="{url('Addressbook', 'DoAdd')}" method="post"  enctype="multipart/form-data" name="theform" id="theform" onsubmit="return validate(this)">
    <table border="0" cellpadding="0" cellspacing="0" class="listtable">
      <caption>
      �����ϵ��
      </caption>
      <tbody class="stripe">
        <tr width="60">
          <td>��</td>
          <td><input name="data[firstname]" type="text" size="80" maxlength="100" /></td>
        </tr>
        <tr>
          <td>��</td>
          <td><input name="data[lastname]" type="text" size="80" maxlength="100" /></td>
        </tr>
        <tr>
          <td>��˾</td>
          <td><input name="data[company]" type="text" size="80" maxlength="100" /></td>
        </tr>
        <tr>
          <td>��ַ</td>
          <td><textarea name="data[address]" cols="80" rows="3"></textarea></td>
        </tr>
        <tr>
          <td>�ֻ�</td>
          <td><input name="data[mobile]" type="text" size="50" maxlength="20" /></td>
        </tr>
        <tr>
          <td>�绰</td>
          <td><input name="data[phone]" type="text" size="50" maxlength="50" /></td>
        </tr>
      </tbody>
    </table>
    <div class="button_box">
      <input class="btn" name="dosubmit" type="submit" value="�ύ" />
    </div>
  </form>
</div>