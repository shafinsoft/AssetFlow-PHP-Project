<?php require 'includes/auth.php'; requireLogin();
$uid=(int)$_SESSION['user_id'];$admin=isAdmin();
$statuses=['Active','Inactive','Under Maintenance','Disposed'];
if($_SERVER['REQUEST_METHOD']==='POST'){checkCsrf();$id=(int)($_POST['id']??0);$act=$_POST['action']??'save';
 if($act==='delete'){
  if($id&&canTouch($conn,$id)){$o=$conn->query('SELECT image,asset_name FROM assets WHERE id='.$id)->fetch_row();
   $s=$conn->prepare('DELETE FROM assets WHERE id=?');$s->bind_param('i',$id);$s->execute();delImage($o[0]);logAct($conn,'asset_delete',$o[1]);flash('Asset deleted successfully');}
  else flash('You are not allowed to delete this asset','err');
 }elseif($act==='bulk'){
  $ids=array_unique(array_map('intval',(array)($_POST['ids']??[])));$op=$_POST['op']??'';$n=0;
  foreach($ids as $i){if(!$i||!canTouch($conn,$i))continue;
   if($op==='delete'){$o=$conn->query('SELECT image,asset_name FROM assets WHERE id='.$i)->fetch_row();
    $s=$conn->prepare('DELETE FROM assets WHERE id=?');$s->bind_param('i',$i);$s->execute();delImage($o[0]);logAct($conn,'asset_delete',$o[1]);$n++;}
   elseif(in_array($op,$statuses,true)){$s=$conn->prepare('UPDATE assets SET status=? WHERE id=?');$s->bind_param('si',$op,$i);$s->execute();logAct($conn,'asset_status',$op,$i);$n++;}}
  flash($n?$n.' asset(s) updated':'No assets were changed',$n?'ok':'err');
 }else{
  $n=trim($_POST['asset_name']??'');$cat=(int)($_POST['category_id']??0)?:null;$type=trim($_POST['asset_type']??'');
  $date=vdate($_POST['purchase_date']??'');$wd=vdate($_POST['warranty_date']??'');$price=$_POST['price']??'';$st=$_POST['status']??'';$desc=trim($_POST['description']??'');
  if($n===''||!is_numeric($price)||$price<0||!in_array($st,$statuses,true)){flash('Please fill the form correctly','err');header('Location: assets.php'.($id?"?edit=$id":''));exit;}
  $price=(float)$price;
  if($id){
   if(!canTouch($conn,$id)){flash('You are not allowed to edit this asset','err');header('Location: assets.php');exit;}
   $s=$conn->prepare('UPDATE assets SET asset_name=?,category_id=?,asset_type=?,purchase_date=?,warranty_date=?,price=?,status=?,description=? WHERE id=?');
   $s->bind_param('sisssdssi',$n,$cat,$type,$date,$wd,$price,$st,$desc,$id);$s->execute();$aid=$id;logAct($conn,'asset_update',$n,$aid);flash('Asset updated successfully');
  }else{
   $s=$conn->prepare('INSERT INTO assets(user_id,category_id,asset_name,asset_type,purchase_date,warranty_date,price,status,description) VALUES(?,?,?,?,?,?,?,?,?)');
   $s->bind_param('iissssdss',$uid,$cat,$n,$type,$date,$wd,$price,$st,$desc);$s->execute();$aid=$conn->insert_id;logAct($conn,'asset_add',$n,$aid);flash('Asset added successfully');}
  $img=saveImage($_FILES['image']??[]);
  if($img===false)flash('Asset saved, but the photo must be a JPG, PNG, WebP or GIF under 2 MB','err');
  elseif($img){$o=$conn->query('SELECT image FROM assets WHERE id='.(int)$aid)->fetch_row()[0];$s=$conn->prepare('UPDATE assets SET image=? WHERE id=?');$s->bind_param('si',$img,$aid);$s->execute();delImage($o);}
 }
 header('Location: assets.php');exit;}
$catList=[];foreach($conn->query('SELECT id,category_name FROM categories ORDER BY category_name') as $c)$catList[]=$c;
$edit=null;
if(isset($_GET['edit'])){$eid=(int)$_GET['edit'];
 if(canTouch($conn,$eid)){$s=$conn->prepare('SELECT * FROM assets WHERE id=?');$s->bind_param('i',$eid);$s->execute();$edit=$s->get_result()->fetch_assoc();}
 else flash('Asset not found or not allowed','err');}
$q=trim($_GET['q']??'');$fs=$_GET['status']??'';if(!in_array($fs,$statuses,true))$fs='';
$fc=(int)($_GET['cat']??0);$d1=vdate($_GET['from']??'');$d2=vdate($_GET['to']??'');$ff=isset($_GET['fav']);
$w=[];$t='';$a=[];
if(!$admin){$w[]='a.user_id=?';$t.='i';$a[]=$uid;}
if($q!==''){$w[]='a.asset_name LIKE ?';$t.='s';$a[]="%$q%";}
if($fs!==''){$w[]='a.status=?';$t.='s';$a[]=$fs;}
if($fc){$w[]='a.category_id=?';$t.='i';$a[]=$fc;}
if($d1){$w[]='a.purchase_date>=?';$t.='s';$a[]=$d1;}
if($d2){$w[]='a.purchase_date<=?';$t.='s';$a[]=$d2;}
if($ff)$w[]='a.id IN (SELECT asset_id FROM favorites WHERE user_id='.$uid.')';
$cond=$w?' WHERE '.implode(' AND ',$w):'';
$cs=$conn->prepare('SELECT COUNT(*) FROM assets a'.$cond);if($a)$cs->bind_param($t,...$a);$cs->execute();
$total=(int)$cs->get_result()->fetch_row()[0];$per=9;$pages=max(1,(int)ceil($total/$per));$pg=min(max(1,(int)($_GET['page']??1)),$pages);$off=($pg-1)*$per;
$s=$conn->prepare('SELECT a.*,c.category_name,u.name owner,(SELECT COUNT(*) FROM favorites f WHERE f.asset_id=a.id AND f.user_id='.$uid.') fav FROM assets a LEFT JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.user_id'.$cond.' ORDER BY a.id DESC LIMIT '.$per.' OFFSET '.$off);
if($a)$s->bind_param($t,...$a);$s->execute();$rows=$s->get_result();
$cnt=[];foreach($conn->query('SELECT status,COUNT(*) n FROM assets'.($admin?'':' WHERE user_id='.$uid).' GROUP BY status') as $c)$cnt[$c['status']]=$c['n'];$all=array_sum($cnt);
$favN=(int)$conn->query('SELECT COUNT(*) FROM favorites WHERE user_id='.$uid)->fetch_row()[0];
$keep=array_filter(['q'=>$q,'status'=>$fs,'cat'=>$fc?:'','from'=>$d1,'to'=>$d2,'fav'=>$ff?'1':'']);$qs=http_build_query($keep);
$back='assets.php'.($_SERVER['QUERY_STRING']!==''?'?'.$_SERVER['QUERY_STRING']:'');
$v=fn($k,$d='')=>e($edit[$k]??$d);
$title='Assets';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1>Asset Management</h1><p><?=$admin?'All assets across the system.':'Your assets, in one place.'?></p></div><img src="public/images/products/box.svg" alt=""></header>
<div class="chips"><a class="chip<?=($fs===''&&!$ff)?' on':''?>" href="assets.php">All <b><?=$all?></b></a>
<?php foreach($statuses as $x): ?><a class="chip<?=$fs===$x?' on':''?>" href="assets.php?status=<?=urlencode($x)?>"><?=$x?> <b><?=$cnt[$x]??0?></b></a><?php endforeach ?>
<a class="chip<?=$ff?' on':''?>" href="assets.php?fav=1">&#9733; Favorites <b><?=$favN?></b></a></div>
<form class="adv" method="get"><input name="q" id="search" placeholder="Search by name" value="<?=e($q)?>">
<select name="status"><option value="">All status</option><?php foreach($statuses as $x): ?><option <?=$fs===$x?'selected':''?>><?=$x?></option><?php endforeach ?></select>
<select name="cat"><option value="">All categories</option><?php foreach($catList as $c): ?><option value="<?=$c['id']?>" <?=$fc==$c['id']?'selected':''?>><?=e($c['category_name'])?></option><?php endforeach ?></select>
<input type="date" name="from" title="Purchased from" value="<?=e($d1)?>"><input type="date" name="to" title="Purchased until" value="<?=e($d2)?>">
<?php if($ff): ?><input type="hidden" name="fav" value="1"><?php endif ?>
<button type="submit" id="searchBtn">Search</button><a class="btn gray" href="assets.php">Reset</a>
<a class="btn gray" id="exportBtn" href="export.php?<?=e(http_build_query(array_filter(['q'=>$q,'status'=>$fs])))?>">Export CSV</a></form>
<div class="toolrow"><div class="seg"><button type="button" data-view="table" class="on">Table</button><button type="button" data-view="cards">Cards</button></div>
<button type="button" id="addAssetBtn">+ Add asset</button></div>
<form method="post" id="bulkForm" data-confirm="Apply this action to the selected assets? Deleting cannot be undone."><?=csrfField()?><input type="hidden" name="action" value="bulk">
<div class="bulk" id="bulkBar"><b id="bulkCount">0 selected</b><select name="op"><?php foreach($statuses as $x): ?><option value="<?=$x?>">Set status: <?=$x?></option><?php endforeach ?><option value="delete">Delete selected</option></select><button type="submit">Apply</button></div></form>
<div id="assetViews" data-view="table">
<div class="tw"><table id="assetTable"><tr><th><input type="checkbox" id="chkAll" aria-label="Select all"></th><th>#</th><th>Photo</th><th>Name</th><th>Category</th><th>Type</th><th>Date</th><th>Price</th><th>Status</th><?php if($admin): ?><th>Owner</th><?php endif ?><th>Actions</th></tr>
<?php if(!$rows->num_rows): ?><tr><td colspan="11"><div class="empty"><img src="public/images/products/box.svg" alt=""><p>No assets found. Use "Add asset" to create one.</p></div></td></tr><?php endif ?>
<?php foreach($rows as $r): ?><tr><td><input type="checkbox" class="rowchk" name="ids[]" value="<?=$r['id']?>" form="bulkForm" aria-label="Select asset"></td><td><?=$r['id']?></td>
<td><img class="thumb" src="<?=e(assetImg($r['image'],$r['category_name']))?>" alt=""></td><td><a href="asset-view.php?id=<?=$r['id']?>"><?=e($r['asset_name'])?></a></td>
<td><?=e($r['category_name']??'-')?></td><td><?=e($r['asset_type'])?></td><td><?=e($r['purchase_date'])?></td><td><?=number_format($r['price'],2)?></td>
<td><span class="badge s-<?=str_replace(' ','',e($r['status']))?>"><?=e($r['status'])?></span></td><?php if($admin): ?><td><?=e($r['owner'])?></td><?php endif ?>
<td><form method="post" action="favorite.php"><?=csrfField()?><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="back" value="<?=e($back)?>"><button class="star<?=$r['fav']?' on':''?>" title="Favorite" aria-label="Toggle favorite">&#9733;</button></form>
<a class="btn" href="assets.php?edit=<?=$r['id']?>">Edit</a>
<form method="post" data-confirm="Delete this asset? This cannot be undone."><?=csrfField()?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="red">Delete</button></form></td></tr><?php endforeach ?></table></div>
<div class="cards" id="cardView"><?php if(!$rows->num_rows): ?><div class="empty"><img src="public/images/products/box.svg" alt=""><p>Nothing here yet.</p></div><?php endif ?><?php $rows->data_seek(0); foreach($rows as $r): ?>
<article class="ac"><form class="starf" method="post" action="favorite.php"><?=csrfField()?><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="back" value="<?=e($back)?>"><button class="star<?=$r['fav']?' on':''?>" aria-label="Toggle favorite">&#9733;</button></form>
<a href="asset-view.php?id=<?=$r['id']?>"><img src="<?=e(assetImg($r['image'],$r['category_name']))?>" alt=""></a>
<div class="b"><h3><a href="asset-view.php?id=<?=$r['id']?>"><?=e($r['asset_name'])?></a></h3>
<div class="m"><?=e($r['category_name']??'No category')?><?php if($admin): ?> &middot; <?=e($r['owner'])?><?php endif ?></div>
<div class="f"><span class="badge s-<?=str_replace(' ','',e($r['status']))?>"><?=e($r['status'])?></span><b><?=number_format($r['price'],2)?></b></div></div>
<div class="act"><a class="btn" href="assets.php?edit=<?=$r['id']?>">Edit</a>
<form method="post" data-confirm="Delete this asset? This cannot be undone."><?=csrfField()?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="red">Delete</button></form></div></article>
<?php endforeach ?></div></div>
<?php if($pages>1): ?><nav class="pager"><?php for($i=1;$i<=$pages;$i++): ?><a class="chip<?=$i===$pg?' on':''?>" href="assets.php?<?=e($qs?($qs.'&'):'')?>page=<?=$i?>"><?=$i?></a><?php endfor ?></nav><?php endif ?>
<dialog id="assetDialog" <?=$edit?'data-open':''?>><form class="dlgform" method="post" id="assetForm" enctype="multipart/form-data">
<div class="dlg-h"><h3><?=$edit?'Edit asset':'Add asset'?></h3><?php if($edit): ?><a class="dlg-x" href="assets.php" aria-label="Close">&times;</a><?php else: ?><button type="button" class="dlg-x" data-close aria-label="Close">&times;</button><?php endif ?></div>
<?=csrfField()?><input type="hidden" name="id" value="<?=$v('id')?>"><div class="row">
<div><label>Asset Name *</label><input name="asset_name" id="asset_name" value="<?=$v('asset_name')?>"></div>
<div><label>Category</label><select name="category_id" id="category_id"><option value="">-- None --</option><?php foreach($catList as $c): ?><option value="<?=$c['id']?>" <?=($edit['category_id']??0)==$c['id']?'selected':''?>><?=e($c['category_name'])?></option><?php endforeach ?></select></div>
<div><label>Asset Type</label><input name="asset_type" id="asset_type" value="<?=$v('asset_type')?>"></div>
<div><label>Purchase Date</label><input type="date" name="purchase_date" id="purchase_date" value="<?=$v('purchase_date')?>"></div>
<div><label>Warranty until</label><input type="date" name="warranty_date" id="warranty_date" value="<?=$v('warranty_date')?>"></div>
<div><label>Price</label><input type="number" step="0.01" min="0" name="price" id="price" value="<?=$v('price','0')?>"></div>
<div><label>Status</label><select name="status" id="status"><?php foreach($statuses as $x): ?><option <?=($edit['status']??'Active')===$x?'selected':''?>><?=$x?></option><?php endforeach ?></select></div>
<div><label>Photo (JPG, PNG, WebP, max 2 MB)</label><input type="file" name="image" id="image" accept="image/*"></div></div>
<label>Description</label><textarea name="description" id="description" rows="2"><?=$v('description')?></textarea><br>
<button type="submit" id="saveAsset"><?=$edit?'Update':'Add'?> Asset</button></form></dialog>
<?php include 'includes/footer.php'; ?>
