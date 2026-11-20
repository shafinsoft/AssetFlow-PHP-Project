<?php require 'includes/auth.php'; requireLogin();
$sc=isAdmin()?'':' AND a.user_id='.(int)$_SESSION['user_id'];
$war=$conn->query("SELECT a.id,a.asset_name,a.warranty_date FROM assets a WHERE a.warranty_date IS NOT NULL AND a.warranty_date<=DATE_ADD(CURDATE(),INTERVAL 30 DAY) AND a.warranty_date>=DATE_SUB(CURDATE(),INTERVAL 90 DAY) AND a.status<>'Disposed'$sc ORDER BY a.warranty_date");
$mnt=$conn->query("SELECT m.*,a.asset_name FROM maintenance m JOIN assets a ON a.id=m.asset_id WHERE m.done=0 AND m.due_date<=DATE_ADD(CURDATE(),INTERVAL 7 DAY)$sc ORDER BY m.due_date");
$title='Notifications';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1>Notification Center</h1><p>Warranties and maintenance that need your attention.</p></div><img src="public/images/products/laptop.svg" alt=""></header>
<?php if(!$war->num_rows&&!$mnt->num_rows): ?><div class="card"><div class="empty"><img src="public/images/products/box.svg" alt=""><p>You are all caught up. Nothing needs attention.</p></div></div><?php endif ?>
<div class="dash2">
<?php if($mnt->num_rows): ?><div class="card"><h3>Maintenance</h3><?php foreach($mnt as $m): $b=mBadge($m); ?><a class="att" href="asset-view.php?id=<?=$m['asset_id']?>"><span><b><?=e($m['title'])?></b><small><?=e($m['asset_name'])?> &middot; due <?=e($m['due_date'])?></small></span><span class="badge <?=$b[0]?>"><?=e($b[1])?></span></a><?php endforeach ?></div><?php endif ?>
<?php if($war->num_rows): ?><div class="card"><h3>Warranties</h3><?php foreach($war as $x): $d=(int)floor((strtotime($x['warranty_date'])-strtotime(date('Y-m-d')))/86400); ?><a class="att" href="asset-view.php?id=<?=$x['id']?>"><span><b><?=e($x['asset_name'])?></b><small>Warranty until <?=e($x['warranty_date'])?></small></span><span class="badge <?=$d<0?'s-Disposed':'s-UnderMaintenance'?>"><?=$d<0?'Expired':$d.' days left'?></span></a><?php endforeach ?></div><?php endif ?></div>
<?php include 'includes/footer.php'; ?>
