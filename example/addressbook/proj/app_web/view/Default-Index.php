<div class="area">
<table class="listtable" border="0" cellpadding="0" cellspacing="0">
<tr><td><a href="{url('Addressbook','Add')}">�����ϵ��</a> | <a href="{url('Addressbook','Addgroup')}">�����ϵ�˷���</a></td></tr>
</table>

<form name="myform" method="post" action="">
  <table border="0" cellpadding="0" cellspacing="0" class="listtable">
    <caption>
    ͨѶ¼
    </caption>
    <tr>
      <th>ѡ��</th>
      <th>����</th>
      <th>�����ʼ�</th>
      <th>�ֻ�</th>
      <th>�绰</th>
      <th>��ַ</th>
      <th>����ʱ��</th>
      <th>�������</th>
    </tr>
	<tbody class="stripe">
<!--{loop $this->data['data']['rows'] $data}-->
    <tr>
      <td><input type="checkbox" name="ids[]" value="{$data['id']}" /></td>
      <td>{$data['firstname']} {$data['lastname']}</td>
      <td></td>
      <td>{$data['mobile']}</td>
      <td>{$data['phone']}</td>
      <td>{$data['address']}</td>
      <td>{date('Y-m-d H:i:s', $data['modified'])}</td>
      <td><a href="{url('Addressbook', 'Edit',array('id'=>$data['id']))}">�༭</a> | <a href="javascript:confirmurl('{url('Addressbook', 'Dodelete',array('id'=>$data['id']))}','ȷ��ɾ����')">ɾ��</a></td>
    </tr>
<!--{/loop}-->
	</tbody>
  </table>
  <div class="button_box">��ѡ�������</div>
</form>
{$this->data['pages']}
</div>