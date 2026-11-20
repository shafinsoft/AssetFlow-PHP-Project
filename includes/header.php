<?php $title=$title??'AssetFlow';$full=!empty($full);$shell=logged()&&!$full;$cur=basename($_SERVER['SCRIPT_NAME']);
if($shell){$nc=notifCount($conn);
 $P=['grid'=>'<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
 'box'=>'<path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/>','star'=>'<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
 'cal'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>','file'=>'<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 13h6M9 17h6"/>',
 'folder'=>'<path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>','users'=>'<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0113 0"/><path d="M16 4.5a3.5 3.5 0 010 7M18 14a6.5 6.5 0 013.5 6"/>',
 'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>','user'=>'<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/>','bell'=>'<path d="M6 16v-5a6 6 0 0112 0v5l2 2H4z"/><path d="M10 21h4"/>',
 'menu'=>'<path d="M4 6h16M4 12h16M4 18h16"/>','out'=>'<path d="M9 4H5v16h4M16 8l4 4-4 4M20 12H9"/>'];
 $sic=function($n)use($P){return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$P[$n].'</svg>';};
 $nav=[['dashboard.php','grid','Dashboard'],['assets.php','box','Assets'],['assets.php?fav=1','star','Favorites'],['maintenance.php','cal','Maintenance'],['report.php','file','Reports']];
 if(isAdmin())$nav=array_merge($nav,[['categories.php','folder','Categories'],['customers.php','users','Customers'],['activity.php','clock','Activity']]);
 $nav[]=['profile.php','user','Profile'];$c2=$cur==='asset-view.php'?'assets.php':$cur;}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> | AssetFlow</title>
<script>document.documentElement.classList.add("js");try{if(localStorage.getItem("theme")==="dark")document.documentElement.dataset.theme="dark"}catch(e){}</script><link rel="icon" href="public/images/logo.svg" type="image/svg+xml"><link rel="stylesheet" href="public/css/style.css"></head><body class="<?=$shell?'app':''?>">
<?php if($shell): ?>
<aside class="side" id="side"><a class="brand" href="dashboard.php">AssetFlow</a>
<nav><?php foreach($nav as $n){$f=strtok($n[0],'?');$isFav=strpos($n[0],'fav=1')!==false;$act=$f===$c2&&($c2!=='assets.php'||$isFav===isset($_GET['fav'])); ?><a class="<?=$act?'on':''?>" href="<?=$n[0]?>"><?=$sic($n[1])?><span><?=$n[2]?></span></a><?php } ?></nav>
<div class="side-user"><div class="av"><?=e(strtoupper(mb_substr($_SESSION['name'],0,1)))?></div><div><b><?=e($_SESSION['name'])?></b><small><?=e($_SESSION['role'])?></small></div><a id="logout" href="logout.php" title="Logout"><?=$sic('out')?></a></div></aside>
<div class="scrim" id="scrim"></div>
<div class="appmain"><header class="top"><button type="button" class="menu-btn" id="menuBtn" aria-label="Open menu"><?=$sic('menu')?></button>
<form class="top-search" action="assets.php" method="get"><input name="q" placeholder="Search assets" aria-label="Search assets"></form>
<div class="top-r"><a class="bellb" href="notifications.php" title="Notifications"><?=$sic('bell')?><?php if($nc): ?><i><?=$nc>99?'99+':$nc?></i><?php endif ?></a></div></header>
<?php else: ?>
<nav class="nav"><a class="brand" href="index.php">AssetFlow</a><div>
<?php if(logged()): ?><a href="dashboard.php">Dashboard</a><a href="assets.php">Assets</a>
<?php if(isAdmin()): ?><a href="categories.php">Categories</a><a href="customers.php">Customers</a><a href="activity.php">Activity</a><?php endif ?>
<a class="who" href="profile.php" title="My profile"><?=e($_SESSION['name'])?> (<?=e($_SESSION['role'])?>)</a><a id="logout" href="logout.php">Logout</a>
<?php else: ?><a href="index.php#features">Features</a><a href="index.php#how">How it works</a><a href="index.php#faq">FAQ</a><a href="login.php">Login</a><a class="btn" href="signup.php">Sign Up</a><?php endif ?></div></nav>
<?php endif ?>
<main class="<?=$full?'full':'wrap'?>"><?php if($f=flash()): ?><?php if($full): ?><div class="wrap"><?php endif ?><div class="msg <?=e($f[1])?>"><?=e($f[0])?></div><?php if($full): ?></div><?php endif ?><?php endif ?>
