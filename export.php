<?php require 'includes/auth.php'; requireLogin();
$admin=isAdmin();$statuses=['Active','Inactive','Under Maintenance','Disposed'];
$q=trim($_GET['q']??'');$fs=$_GET['status']??'';$w=[];$t='';$a=[];
if(!$admin){$w[]='a.user_id=?';$t.='i';$a[]=(int)$_SESSION['user_id'];}
if($q!==''){$w[]='a.asset_name LIKE ?';$t.='s';$a[]="%$q%";}
if(in_array($fs,$statuses,true)){$w[]='a.status=?';$t.='s';$a[]=$fs;}
$s=$conn->prepare('SELECT a.*,c.category_name,u.name owner FROM assets a LEFT JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.user_id'.($w?' WHERE '.implode(' AND ',$w):'').' ORDER BY a.id');
if($a)$s->bind_param($t,...$a);$s->execute();$rows=$s->get_result();
function safe($v){$v=(string)$v;return($v!==''&&strpos("=+-@\t\r",$v[0])!==false)?"'".$v:$v;}
logAct($conn,'export','CSV');
header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="assets-'.date('Y-m-d').'.csv"');
$o=fopen('php://output','w');fwrite($o,"\xEF\xBB\xBF");
$h=['ID','Name','Category','Type','Purchase date','Warranty until','Price','Status','Description'];if($admin)$h[]='Owner';
fputcsv($o,$h,',','"','\\');
foreach($rows as $r){$line=[$r['id'],safe($r['asset_name']),safe($r['category_name']),safe($r['asset_type']),$r['purchase_date'],$r['warranty_date'],$r['price'],$r['status'],safe($r['description'])];
 if($admin)$line[]=safe($r['owner']);fputcsv($o,$line,',','"','\\');}
