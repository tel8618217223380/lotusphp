<div class="area">
<table class="listtable" border="0" cellpadding="0" cellspacing="0">
<tr><td><a href="{url('Addressbook','Add')}">添加联系人</a> | <a href="{url('Addressbook','Addgroup')}">添加联系人分组</a></td></tr>
</table>

<form name="myform" method="post" action="">
  <table border="0" cellpadding="0" cellspacing="0" class="listtable">
    <caption>
    通讯录
    </caption>
    <tr>
      <th>选择</th>
      <th>姓名</th>
      <th>电子邮件</th>
      <th>手机</th>
      <th>电话</th>
      <th>地址</th>
      <th>更新时间</th>
      <th>管理操作</th>
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
      <td><a href="{url('Addressbook', 'Edit',array('id'=>$data['id']))}">编辑</a> | <a href="javascript:confirmurl('{url('Addressbook', 'Dodelete',array('id'=>$data['id']))}','确认删除吗？')">删除</a></td>
    </tr>
<!--{/loop}-->
	</tbody>
  </table>
  <div class="button_box">对选中项操作</div>
</form>
{$this->data['pages']}
</div>