<?php require 'includes/auth.php'; requireLogin();
$uid=(int)$_SESSION['user_id'];$admin=isAdmin();
$w=$admin?'':' WHERE user_id='.$uid;$wa=$admin?'':' WHERE a.user_id='.$uid;$wc=$admin?' WHERE ':' WHERE a.user_id='.$uid.' AND ';
$r=$conn->query("SELECT COUNT(*) t,COALESCE(SUM(price),0) v,COALESCE(SUM(status='Active'),0) a,COALESCE(SUM(status='Inactive'),0) i,COALESCE(SUM(status='Under Maintenance'),0) m,COALESCE(SUM(status='Disposed'),0) d FROM assets$w")->fetch_assoc();
$cust=$admin?$conn->query("SELECT COUNT(*) c FROM users WHERE role='customer'")->fetch_assoc()['c']:null;
$tot=max(1,(int)$r['t']);
$hs=[];$est=0;
foreach($conn->query("SELECT a.status,a.price,a.purchase_date,a.warranty_date,(SELECT COUNT(*) FROM maintenance m WHERE m.asset_id=a.id AND m.done=0 AND m.due_date<CURDATE()) od FROM assets a$wa") as $x){
 if($x['status']==='Disposed')continue;$hs[]=healthScore($x['status'],$x['warranty_date'],(int)$x['od']);$est+=deprec($x['price'],$x['purchase_date']);}
$health=$hs?(int)round(array_sum($hs)/count($hs)):null;
$hl=$health===null?'No data':($health>=80?'Excellent':($health>=60?'Good':($health>=40?'Fair':'Needs attention')));
$mo=[];for($i=5;$i>=0;$i--)$mo[date('Y-m',strtotime("first day of -$i month"))]=0;
foreach($conn->query("SELECT DATE_FORMAT(a.created_at,'%Y-%m') ym,COUNT(*) k FROM assets a$wc a.created_at>=DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 5 MONTH),'%Y-%m-01') GROUP BY ym") as $x)if(isset($mo[$x['ym']]))$mo[$x['ym']]=(int)$x['k'];
$mx=max(1,max($mo));
$bars=[['Active',$r['a'],'#16a34a'],['Inactive',$r['i'],'#94a3b8'],['Under Maintenance',$r['m'],'#d97706'],['Disposed',$r['d'],'#dc2626']];
$pal=['#2563eb','#7c3aed','#14b8a6','#d97706','#db2777','#64748b','#16a34a'];$seg=[];$cats=[];$acc=0;$i=0;
foreach($conn->query("SELECT COALESCE(c.category_name,'No category') n,COUNT(*) k FROM assets a LEFT JOIN categories c ON c.id=a.category_id$wa GROUP BY n ORDER BY k DESC") as $c){
 $p=$c['k']/$tot*100;$col=$pal[$i%7];$seg[]=$col.' '.round($acc,2).'% '.round($acc+$p,2).'%';$acc+=$p;$cats[]=[$c['n'],$c['k'],$col];$i++;}
$grad=$seg?implode(',',$seg):'#e8edfb 0% 100%';
$war=$conn->query("SELECT a.id,a.asset_name,a.warranty_date FROM assets a$wc a.warranty_date IS NOT NULL AND a.warranty_date<=DATE_ADD(CURDATE(),INTERVAL 30 DAY) AND a.warranty_date>=DATE_SUB(CURDATE(),INTERVAL 90 DAY) AND a.status<>'Disposed' ORDER BY a.warranty_date LIMIT 4");
$mnt=$conn->query("SELECT m.*,a.asset_name FROM maintenance m JOIN assets a ON a.id=m.asset_id$wc m.done=0 AND m.due_date<=DATE_ADD(CURDATE(),INTERVAL 7 DAY) ORDER BY m.due_date LIMIT 4");
$recent=$conn->query("SELECT a.id,a.asset_name,a.status,a.image,c.category_name FROM assets a LEFT JOIN categories c ON c.id=a.category_id$wa ORDER BY a.id DESC LIMIT 5");
$act=$conn->query('SELECT user_name,action,detail,created_at FROM activity_log'.($admin?'':' WHERE user_id='.$uid).' ORDER BY id DESC LIMIT 6');
$rv=array_map('intval',$_SESSION['recent']??[]);$rvr=null;
if($rv){$ids=implode(',',$rv);$rvr=$conn->query("SELECT a.id,a.asset_name,a.image,c.category_name FROM assets a LEFT JOIN categories c ON c.id=a.category_id$wc a.id IN ($ids) ORDER BY FIELD(a.id,$ids)");}
$title='Dashboard';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1><?=$admin?'Admin':'Customer'?> Dashboard</h1><p><?=date('H')<12?'Good morning':(date('H')<18?'Good afternoon':'Good evening')?>, <?=e($_SESSION['name'])?>. <?=$admin?'Here is the whole system at a glance.':'Here is how your assets are doing.'?></p></div>
<img src="public/images/products/laptop.svg" alt=""></header>
<div class="grid">
<div class="card stat">Total Assets<b><?=(int)$r['t']?></b></div>
<div class="card stat">Active<b><?=(int)$r['a']?></b></div>
<div class="card stat">Inactive<b><?=(int)$r['i']?></b></div>
<div class="card stat">Under Maintenance<b><?=(int)$r['m']?></b></div>
<div class="card stat">Disposed<b><?=(int)$r['d']?></b></div>
<div class="card stat">Total Value<b><?=number_format($r['v'],2)?></b></div>
<div class="card stat">Estimated Value<b><?=number_format($est,2)?></b></div>
<?php if($cust!==null): ?><div class="card stat">Customers<b><?=(int)$cust?></b></div><?php endif ?></div>
<div class="dash3">
<div class="card"><h3>Asset health</h3><div class="donut-wrap"><div class="ring" style="background:conic-gradient(<?=$health===null?'#e3e8f4':ringColor($health)?> <?=(int)$health?>%,#e3e8f4 0)"><b><?=$health===null?'-':$health?></b></div>
<div><b><?=e($hl)?></b><p class="mute" style="margin:4px 0 0">Average of status, warranty and overdue maintenance across assets that are not disposed.</p></div></div></div>
<div class="card"><h3>Assets by status</h3><?php foreach($bars as $b): ?><div class="bar-row"><span><?=$b[0]?></span><div class="track"><i style="width:<?=round($b[1]/$tot*100)?>%;background:<?=$b[2]?>"></i></div><b><?=(int)$b[1]?></b></div><?php endforeach ?></div>
<div class="card"><h3>Assets by category</h3><div class="donut-wrap"><div class="donut" style="background:conic-gradient(<?=$grad?>)"><span><?=(int)$r['t']?><small>assets</small></span></div>
<ul class="legend"><?php foreach($cats as $c): ?><li><i style="background:<?=$c[2]?>"></i><?=e($c[0])?><b><?=(int)$c[1]?></b></li><?php endforeach ?></ul></div></div></div>
<div class="dash2">
<div class="card"><h3>Assets added, last 6 months</h3><div class="mbars"><?php foreach($mo as $ym=>$k): ?><div><span><?=$k?></span><i style="height:<?=round($k/$mx*100)?>%"></i><small><?=date('M',strtotime($ym.'-01'))?></small></div><?php endforeach ?></div></div>
<div class="card"><h3>Needs attention</h3>
<?php if(!$war->num_rows&&!$mnt->num_rows): ?><div class="empty"><img src="public/images/products/box.svg" alt=""><p>Nothing needs attention.</p></div><?php endif ?>
<?php foreach($mnt as $m): $b=mBadge($m); ?><a class="att" href="asset-view.php?id=<?=$m['asset_id']?>"><span><b><?=e($m['title'])?></b><small><?=e($m['asset_name'])?> &middot; maintenance</small></span><span class="badge <?=$b[0]?>"><?=e($b[1])?></span></a><?php endforeach ?>
<?php foreach($war as $x): $d=(int)floor((strtotime($x['warranty_date'])-strtotime(date('Y-m-d')))/86400); ?><a class="att" href="asset-view.php?id=<?=$x['id']?>"><span><b><?=e($x['asset_name'])?></b><small>Warranty until <?=e($x['warranty_date'])?></small></span><span class="badge <?=$d<0?'s-Disposed':'s-UnderMaintenance'?>"><?=$d<0?'Expired':$d.' days left'?></span></a><?php endforeach ?></div></div>
<div class="dash3">
<div class="card"><h3>Recent assets</h3>
<?php if(!$recent->num_rows): ?><div class="empty"><img src="public/images/products/box.svg" alt=""><p>No assets yet. <a href="assets.php">Add your first asset</a>.</p></div><?php endif ?>
<?php foreach($recent as $x): ?><a class="rec" href="asset-view.php?id=<?=$x['id']?>"><img class="thumb" src="<?=e(assetImg($x['image'],$x['category_name']))?>" alt=""><span><b><?=e($x['asset_name'])?></b><small><?=e($x['category_name']??'No category')?></small></span><span class="badge s-<?=str_replace(' ','',e($x['status']))?>"><?=e($x['status'])?></span></a><?php endforeach ?></div>
<div class="card"><h3>Recently viewed</h3>
<?php if(!$rvr||!$rvr->num_rows): ?><p class="mute">Assets you open will show up here.</p><?php endif ?>
<?php if($rvr): foreach($rvr as $x): ?><a class="rec" href="asset-view.php?id=<?=$x['id']?>"><img class="thumb" src="<?=e(assetImg($x['image'],$x['category_name']))?>" alt=""><span><b><?=e($x['asset_name'])?></b><small><?=e($x['category_name']??'No category')?></small></span></a><?php endforeach; endif ?></div>
<div class="card"><h3>Recent activity</h3>
<?php if(!$act->num_rows): ?><p class="mute">No activity yet.</p><?php endif ?>
<?php foreach($act as $x): ?><div class="att"><span><b><?=e(ucfirst(str_replace('_',' ',$x['action'])))?></b><small><?=e($x['detail'])?><?=$x['detail']!==''?' &middot; ':''?><?=$admin?e($x['user_name']).' &middot; ':''?><?=e($x['created_at'])?></small></span></div><?php endforeach ?></div></div>
<?php include 'includes/footer.php'; ?>
