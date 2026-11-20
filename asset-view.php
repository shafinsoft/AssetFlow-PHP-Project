<?php require 'includes/auth.php'; requireLogin();
$id=(int)($_GET['id']??0);$uid=(int)$_SESSION['user_id'];
if(!$id||!canTouch($conn,$id)){flash('Asset not found or not allowed','err');header('Location: assets.php');exit;}
$rc=$_SESSION['recent']??[];array_unshift($rc,$id);$_SESSION['recent']=array_slice(array_values(array_unique($rc)),0,6);
$s=$conn->prepare('SELECT a.*,c.category_name,u.name owner FROM assets a LEFT JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.user_id WHERE a.id=?');
$s->bind_param('i',$id);$s->execute();$a=$s->get_result()->fetch_assoc();
$isFav=(bool)$conn->query("SELECT 1 FROM favorites WHERE user_id=$uid AND asset_id=$id")->num_rows;
$od=(int)$conn->query("SELECT COUNT(*) FROM maintenance WHERE asset_id=$id AND done=0 AND due_date<CURDATE()")->fetch_row()[0];
$score=healthScore($a['status'],$a['warranty_date'],$od);$val=deprec($a['price'],$a['purchase_date']);
$mt=$conn->query("SELECT * FROM maintenance WHERE asset_id=$id ORDER BY done,due_date LIMIT 6");
$tl=[];foreach($conn->query("SELECT action,detail,user_name,created_at FROM activity_log WHERE asset_id=$id ORDER BY id DESC LIMIT 15") as $x)$tl[]=$x;
$lbl=['asset_add'=>'Asset added','asset_update'=>'Details updated','asset_status'=>'Status changed','maintenance_add'=>'Maintenance scheduled','maintenance_done'=>'Maintenance completed','maintenance_delete'=>'Maintenance removed'];
$hasAdd=false;foreach($tl as $x)if($x['action']==='asset_add')$hasAdd=true;
$wd=$a['warranty_date'];$days=$wd?(int)floor((strtotime($wd)-strtotime(date('Y-m-d')))/86400):null;
$title=$a['asset_name'];include 'includes/header.php'; ?>
<p><a href="assets.php">&larr; Back to assets</a></p>
<div class="view">
<img class="view-img" src="<?=e(assetImg($a['image'],$a['category_name']))?>" alt="<?=e($a['asset_name'])?>">
<div><div class="titlerow"><h1><?=e($a['asset_name'])?></h1>
<form method="post" action="favorite.php"><?=csrfField()?><input type="hidden" name="id" value="<?=$id?>"><input type="hidden" name="back" value="asset-view.php?id=<?=$id?>"><button class="star<?=$isFav?' on':''?>" title="Favorite" aria-label="Toggle favorite">&#9733;</button></form></div>
<span class="badge s-<?=str_replace(' ','',e($a['status']))?>"><?=e($a['status'])?></span>
<div class="healthrow"><div class="ring sm" style="background:conic-gradient(<?=ringColor($score)?> <?=$score?>%,#e3e8f4 0)"><b><?=$score?></b></div><div><b>Health score</b><small class="mute">Based on status, warranty and overdue maintenance</small></div></div>
<dl class="dl"><dt>Category</dt><dd><?=e($a['category_name']??'None')?></dd>
<dt>Type</dt><dd><?=e($a['asset_type']?:'-')?></dd>
<dt>Purchase date</dt><dd><?=e($a['purchase_date']?:'-')?></dd>
<dt>Warranty</dt><dd><?php if(!$wd): ?>-<?php else: ?><?=e($wd)?> <span class="badge <?=$days<0?'s-Disposed':($days<=30?'s-UnderMaintenance':'s-Active')?>"><?=$days<0?'Expired':($days<=30?$days.' days left':$days.' days left')?></span><?php endif ?></dd>
<dt>Price</dt><dd><?=number_format($a['price'],2)?></dd>
<dt>Estimated value</dt><dd><?=number_format($val,2)?> <small class="mute">straight-line, 5 years, 10% salvage</small></dd>
<?php if(isAdmin()): ?><dt>Owner</dt><dd><?=e($a['owner'])?></dd><?php endif ?>
<dt>Added</dt><dd><?=e($a['created_at'])?></dd></dl>
<p><?=$a['description']!==''&&$a['description']!==null?nl2br(e($a['description'])):'No description.'?></p>
<a class="btn" href="assets.php?edit=<?=$a['id']?>">Edit asset</a> <a class="btn gray" href="maintenance.php?asset=<?=$a['id']?>">Schedule maintenance</a> <button type="button" class="btn gray" onclick="window.print()">Print</button></div></div>
<div class="dash2" style="margin-top:22px">
<div class="card"><h3>Maintenance</h3>
<?php if(!$mt->num_rows): ?><div class="empty"><img src="public/images/products/box.svg" alt=""><p>No maintenance scheduled.</p></div><?php endif ?>
<?php foreach($mt as $m): $b=mBadge($m); ?><div class="att"><span><b><?=e($m['title'])?></b><small>Due <?=e($m['due_date'])?></small></span><span class="badge <?=$b[0]?>"><?=e($b[1])?></span></div><?php endforeach ?></div>
<div class="card"><h3>Timeline</h3><ul class="tl">
<?php foreach($tl as $x): ?><li><b><?=e($lbl[$x['action']]??ucfirst(str_replace('_',' ',$x['action'])))?></b><small><?=e($x['detail'])?><?=$x['detail']!==''?' &middot; ':''?><?=e($x['user_name'])?> &middot; <?=e($x['created_at'])?></small></li><?php endforeach ?>
<?php if(!$hasAdd): ?><li><b>Record created</b><small><?=e($a['created_at'])?></small></li><?php endif ?></ul></div></div>
<?php include 'includes/footer.php'; ?>
