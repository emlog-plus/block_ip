<?php 
/**
 * IP黑名单插件
 * @copyright (c) crazyus.ga All Rights Reserved
 */
!defined('EMLOG_ROOT') && exit('access deined!');
include(EMLOG_ROOT.'/content/plugins/block_ip-master/block_ip-master_config.php');
function plugin_setting_view() {
}
?>
<script type="text/javascript">
 $("#menu_mg").addClass('active');
$("#block-ip").addClass('active-page');
setTimeout(hideActived,2600);
</script>
<div class="heading-bg  card-views">
<ul class="breadcrumbs">
 <li><a href="./"><i class="fa fa-home"></i> 首页</a></li>
<li class="active">防CC攻击</li>
 </ul>
</div>
<?php if(isset($_GET['setting'])):?>
<div class="actived alert alert-success alert-dismissable">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
插件设置完成
</div>
<?php endif;?>
<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-body"> 
<div class="form-group text-center">
<div id="cc">被CC攻击: <?php 
$DB=Database::getInstance();
$data = $DB->once_fetch_array("SELECT COUNT(`id`) AS cc FROM " . DB_PREFIX . "block ");
if($data['cc'] >="3000"){
$DB->query("TRUNCATE TABLE ". DB_PREFIX ."block");
$CACHE->updateCache();
}
echo $data['cc'];?> 次数</div>
</div>
</div>
</div>
</div>
</div>    
<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">		
<div class="table-wrap ">
<div class="table-responsive">		
<table class="table table-striped table-bordered mb-0" id="item_list">
    <tbody>
      <tr>
        <th style="width:38%;"><b>ip地址</b></th>
        <th style="width:16%;"><b>攻击时间</b></th>
      </tr>
      <?php   
      $DB=Database::getInstance();
	$page=max(1,intval($_GET['page']));
	$pagenum=20;
	$count=$DB->once_fetch_array("select count(*) as num from `".DB_PREFIX."block` ");	
        $res = $DB->query("SELECT `id`, `date`, `serverip` FROM ".DB_PREFIX."block order by date desc limit ".(($page-1)*$pagenum).",$pagenum");
	$pageurl =  pagination($count['num'],$pagenum,$page,"plugin.php?plugin=block_ip-master&page=");
         while($row = $DB->fetch_array($res)){
    $output.= empty($row) ? '<tr><td colspan="2">暂无攻击</td></tr>' : '<tr><td>'.$row['serverip'].'</td><td>'.date("Y-m-d h:i",$row['date']).'</td></tr>';
  }
  echo $output;
      ?>
    </tbody>
  </table>
</div>
</div>
</div>
</div>
</div>    
<?php if(!empty($pageurl)){ ?>
<div class="row">
<div class="col-lg-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="form-group text-center">
 <div id="pagenav">
 <?php echo $pageurl; ?>
</div>
</div>
</div>
</div>			
</div>
</div>
<?php }?>
<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-body"> 
<form action="plugin.php?plugin=block_ip-master&action=setting" method="post">
<div class="form-group">
<label class="control-label mb-10">时间段[秒]</label>
<input type="text" name="block_second" value="<?php echo $block_second; ?>" size="20" class="form-control" /> 
</div>
<div class="form-group">
<label class="control-label mb-10">刷新次数</label>
<input type="text" name="block_refresh" value="<?php echo $block_refresh; ?>" size="20" class="form-control"/>
 </div>
 <div class="form-group">
<label class="control-label mb-10">
<input type="checkbox" name="is_via" id="is_via" value="true"<?php if($is_via == "true"):?> checked<?php endif;?> /> 禁止使用IP代理访问本站</label>
 </div>
<div class="form-group">
<label class="control-label mb-10">IP黑名单<sup>用逗号分开</sup></label>
<textarea name="block_ip" class="form-control" cols="" rows="4" ><?php echo $block_ip;?>
 </textarea>
</div>
<div class="form-group">
<label class="control-label mb-10">IP被禁用描述</label>
<input type="text" name="block_des" value="<?php echo $block_des; ?>" size="20" class="form-control"/>
 </div>
<div class="form-group"><input type="submit" value="保存设置"  class="btn btn-success button"/></div>
</form>
</div>
</div>
</div>
</div>

<?php
function plugin_setting(){
  $block_ip = isset($_POST['block_ip']) ? addslashes($_POST['block_ip']) : '';
  $block_second = isset($_POST['block_second']) ? addslashes($_POST['block_second']) : '';
  $block_refresh = isset($_POST['block_refresh']) ? addslashes(trim($_POST['block_refresh'])) : '';
    $block_des = isset($_POST['block_des']) ? addslashes(trim($_POST['block_des'])) : '';
  $is_via = isset($_POST['is_via']) ? addslashes(trim($_POST['is_via'])) : '';
  $data = "<?php
  \$block_second = '".$block_second."';
  \$block_refresh = '".$block_refresh."';
  \$block_ip = '".$block_ip."';
  \$block_des = '".$block_des."';
  \$is_via = '".$is_via."';
?>";
    $file = EMLOG_ROOT.'/content/plugins/block_ip-master/block_ip-master_config.php';
    @ $fp = fopen($file, 'wb') OR emMsg('读取文件失败，如果您使用的是Unix/Linux主机，请修改文件/content/plugins/block_ip-master/block_ip-master_config.php的权限为755或777。如果您使用的是Windows主机，请联系管理员，将该文件设为everyone可写');
    @ $fw = fwrite($fp,$data) OR emMsg('写入文件失败，如果您使用的是Unix/Linux主机，请修改文件/content/plugins/block_ip-master/block_ip-master_config.php的权限为755或777。如果您使用的是Windows主机，请联系管理员，将该文件设为everyone可写');
    fclose($fp);
}
?>
