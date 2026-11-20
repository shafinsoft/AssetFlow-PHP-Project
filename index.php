<?php require 'includes/auth.php'; $title='Home'; $full=true;
$vid=file_exists(__DIR__.'/public/videos/hero.mp4');
$ic=function($p){return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$p.'</svg>';};
$features=[
['<path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/>','Track assets','Add, edit and delete items with price, purchase date and status.'],
['<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>','Find things fast','Search by name, category, status and date, then update many assets at once.'],
['<path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>','Stay organized','Admins group assets into categories.'],
['<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/>','Keep data private',"Customers can never open each other's assets."],
['<path d="M6 16v-5a6 6 0 0112 0v5l2 2H4z"/><path d="M10 21h4"/>','Warranty and maintenance alerts','Get notified about expiring warranties and maintenance that is due.'],
['<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>','Activity log','Admins can review who did what and when.'],
['<path d="M12 4v11"/><path d="M7 11l5 5 5-5"/><path d="M5 20h14"/>','CSV export','Download your asset list for Excel in one click.'],
['<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-8 9"/>','Photos and dark mode','Add a photo to every asset and switch to a dark theme any time.']];
include 'includes/header.php'; ?>
<section class="cover lp<?=$vid?' has-video':''?>"><?php if($vid): ?><video autoplay muted loop playsinline preload="metadata"><source src="public/videos/hero.mp4" type="video/mp4"></video><?php endif ?>
<div class="floaters" aria-hidden="true">
<img class="fl" data-d="16" style="--w:150px;left:42%;top:7%;--r:-7deg;--t:8s;--dx:14px;--dy:-18px" src="public/images/products/laptop.svg" alt="">
<img class="fl" data-d="22" style="--w:170px;right:2%;top:5%;--r:5deg;--t:9s;--dx:-12px;--dy:20px" src="public/images/products/car.svg" alt="">
<img class="fl" data-d="12" style="--w:115px;right:3%;top:74%;--r:-5deg;--t:7s;--dx:10px;--dy:-14px" src="public/images/products/box.svg" alt=""></div>
<div class="hero"><div>
<span class="pill">New: maintenance scheduler, health score and favorites</span>
<h1>Know what you own, where it is, and what shape it's in.</h1>
<p>AssetFlow keeps laptops, furniture, vehicles and everything else you own in one searchable list, with a status on every item.</p>
<?php if(logged()): ?><a class="btn" href="dashboard.php">Open dashboard</a>
<?php else: ?><a class="btn" href="signup.php">Create free account</a><a class="btn gray" href="login.php">Login</a><?php endif ?>
<ul class="trust" style="animation-delay:.45s"><li>Free to use</li><li>Admin and Customer roles</li><li>Your data stays private</li></ul></div>
<div class="scene3d"><div class="gchip g1"><b>92</b>Asset health</div><div class="gchip g2"><b>12 days</b>Warranty left</div>
<div class="mock"><div class="mock-bar"><i></i><i></i><i></i><span>My assets (sample)</span></div>
<div class="mock-stats"><div><b>24</b>Total</div><div><b>18</b>Active</div><div><b>3</b>Maintenance</div></div>
<div class="mock-row" style="--i:0"><span>MacBook Pro 14<small>Electronics</small></span><span class="badge s-Active">Active</span></div>
<div class="mock-row" style="--i:1"><span>Standing desk<small>Furniture</small></span><span class="badge s-UnderMaintenance">Under Maintenance</span></div>
<div class="mock-row" style="--i:2"><span>Delivery van<small>Vehicle</small></span><span class="badge s-Inactive">Inactive</span></div>
<div class="mock-row" style="--i:3"><span>Old projector<small>Electronics</small></span><span class="badge s-Disposed">Disposed</span></div></div></div></div></section>

<div class="stripe rv"><div class="stripe-in"><div><b>4</b>asset statuses</div><div><b>2</b>user roles</div><div><b>30 days</b>warranty alerts</div><div><b>CSV</b>export in one click</div></div></div>

<div class="marquee" aria-hidden="true"><div class="mq-track"><?php for($k=0;$k<2;$k++): foreach(['Laptops','Phones','Furniture','Vehicles','Cameras','Printers','Tools','Monitors','Projectors','Bikes'] as $w): ?><span><?=$w?></span><?php endforeach; endfor ?></div></div>

<section class="lp-sec" id="about"><div class="in about">
<div class="rv"><h2>About AssetFlow</h2><p class="lead">A simple asset management system for admins and customers. Every customer sees and edits only their own assets, while admins keep the categories and the customer list in order.</p>
<span class="pill2">Admin and Customer roles</span><span class="pill2">Photos and warranty dates</span><span class="pill2">Light and dark mode</span></div>
<div class="about-art rv" style="--i:2"><div class="ap"><img src="public/images/products/laptop.svg" alt=""><span>MacBook Pro 14 <em class="badge s-Active">Active</em></span></div>
<div class="ap"><img src="public/images/products/chair.svg" alt=""><span>Office chair <em class="badge s-UnderMaintenance">Maintenance</em></span></div><div class="tag">12 days of warranty left</div></div></div></section>

<section class="lp-sec" id="features" style="padding-top:20px"><div class="in"><div class="lp-h rv"><h2>What you can do</h2><p>Everything you need to keep track of what you own.</p></div>
<div class="bento">
<div class="bx b-wide rv"><div><h3>Track assets</h3><p>Add, edit and delete items with price, purchase date and status.</p></div>
<div class="bx-v"><div class="mini-row"><img src="public/images/products/laptop.svg" alt=""><span>MacBook Pro 14</span><span class="badge s-Active">Active</span></div>
<div class="mini-row"><img src="public/images/products/chair.svg" alt=""><span>Standing desk</span><span class="badge s-UnderMaintenance">Under Maintenance</span></div>
<div class="mini-row"><img src="public/images/products/car.svg" alt=""><span>Delivery van</span><span class="badge s-Inactive">Inactive</span></div></div></div>
<div class="bx rv" style="--i:1"><div><h3>Find things fast</h3><p>Search by name, category, status and date, then update many assets at once.</p></div>
<div class="bx-v"><div class="mini-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg><span>Search laptops</span></div>
<div class="mini-chips"><span>Active</span><span>Electronics</span><span>2025</span></div></div></div>
<div class="bx rv" style="--i:2"><div><h3>Stay organized</h3><p>Admins group assets into categories.</p></div>
<div class="bx-v"><div class="cat-row"><span>Electronics</span><div class="mini-bar"><i style="width:82%"></i></div><b>12</b></div>
<div class="cat-row"><span>Furniture</span><div class="mini-bar"><i style="width:52%"></i></div><b>6</b></div>
<div class="cat-row"><span>Vehicle</span><div class="mini-bar"><i style="width:22%"></i></div><b>2</b></div></div></div>
<div class="bx b-wide rv"><div><h3>Warranty and maintenance alerts</h3><p>Get notified about expiring warranties and maintenance that is due.</p></div>
<div class="bx-v"><div class="notif"><i class="dotw"></i><span><b>Warranty ends soon</b><small>MacBook Pro 14</small></span><span class="badge s-UnderMaintenance">12 days left</span></div>
<div class="notif"><i class="dotr"></i><span><b>Service is overdue</b><small>Delivery van</small></span><span class="badge s-Disposed">Overdue</span></div></div></div>
<div class="bx rv" style="--i:1"><div><h3>Activity log</h3><p>Admins can review who did what and when.</p></div>
<div class="bx-v"><ul class="tl tl-s"><li><b>Asset added</b><small>MacBook Pro 14</small></li><li><b>Status changed</b><small>Under Maintenance</small></li><li><b>Maintenance completed</b><small>Today</small></li></ul></div></div>
<div class="bx rv" style="--i:2"><div><h3>Keep data private</h3><p>Customers can never open each other's assets.</p></div>
<div class="bx-v"><div class="pv ok"><span class="av2">Y</span>Your assets<em>&#10003;</em></div><div class="pv no"><span class="av2">O</span>Other customer<em>&#10005;</em></div></div></div>
<div class="bx b-wide rv"><div><h3>CSV export</h3><p>Download your asset list for Excel in one click.</p></div>
<div class="bx-v"><div class="mini-tbl"><div><b>Name</b><b>Status</b><b>Price</b></div><div><span>MacBook Pro 14</span><span>Active</span><span>1,899</span></div><div><span>Standing desk</span><span>Under Maintenance</span><span>420</span></div><div><span>Delivery van</span><span>Inactive</span><span>12,500</span></div></div><div class="filechip"><span class="fi">CSV</span><span><b>assets.csv</b><small>Opens in Excel or Google Sheets</small></span><span class="dl-i"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11"/><path d="M7 11l5 5 5-5"/><path d="M5 20h14"/></svg></span></div></div></div>
<div class="bx b-wide rv" style="--i:1"><div><h3>Photos and dark mode</h3><p>Add a photo to every asset and switch to a dark theme any time.</p></div>
<div class="bx-v"><div class="swatches"><div class="sw"><img src="public/images/products/laptop.svg" alt=""><small>Light</small></div><div class="sw dk"><img src="public/images/products/laptop.svg" alt=""><small>Dark</small></div></div></div></div>
</div></div></section>

<section class="lp-sec dark" id="preview"><div class="in"><div class="lp-h rv"><h2>See everything at a glance</h2><p>One dashboard shows health, status, categories and what needs attention. The numbers below are sample data.</p></div>
<div class="bigmock rv"><div class="mock-bar"><i></i><i></i><i></i><span>AssetFlow dashboard (sample data)</span></div>
<div class="bm-body"><div class="bm-stats"><div><small>Total assets</small><b>24</b></div><div><small>Active</small><b>18</b></div><div><small>Under maintenance</small><b>3</b></div><div><small>Estimated value</small><b>48,200</b></div></div>
<div class="bm-grid"><div class="bm-card"><h4>Asset health</h4><div class="ring" style="background:conic-gradient(#16a34a 92%,#e3e8f4 0)"><b>92</b></div></div>
<div class="bm-card"><h4>Assets by status</h4><div class="bar-row"><span>Active</span><div class="track"><i style="width:75%;background:#16a34a"></i></div><b>18</b></div><div class="bar-row"><span>Inactive</span><div class="track"><i style="width:8%;background:#94a3b8"></i></div><b>2</b></div><div class="bar-row"><span>Maintenance</span><div class="track"><i style="width:13%;background:#d97706"></i></div><b>3</b></div><div class="bar-row"><span>Disposed</span><div class="track"><i style="width:4%;background:#dc2626"></i></div><b>1</b></div></div>
<div class="bm-card"><h4>By category</h4><div class="donut-wrap"><div class="donut" style="background:conic-gradient(#2563eb 0% 50%,#7c3aed 50% 75%,#14b8a6 75% 100%)"><span>24<small>assets</small></span></div>
<ul class="legend"><li><i style="background:#2563eb"></i>Electronics<b>12</b></li><li><i style="background:#7c3aed"></i>Furniture<b>6</b></li><li><i style="background:#14b8a6"></i>Vehicle<b>6</b></li></ul></div></div></div></div></div></div></section>

<section class="lp-sec alt" id="how"><div class="in"><div class="lp-h rv"><h2>How it works</h2><p>Up and running in three steps.</p></div><div class="steps2">
<div class="step2 rv"><span class="n">1</span><h3>Create an account</h3><p>Sign up in a few seconds as a customer.</p><div class="stepv fm"><i></i><i></i><i class="short"></i><span class="fmb">Create account</span></div></div>
<div class="step2 rv" style="--i:1"><span class="n">2</span><h3>Add your assets</h3><p>Record the name, category, price, date and a photo.</p><div class="stepv"><div class="fm2"><img src="public/images/products/laptop.svg" alt=""><div><i></i><i class="short"></i></div><span class="badge s-Active">Active</span></div></div></div>
<div class="step2 rv" style="--i:2"><span class="n">3</span><h3>Track their status</h3><p>Mark each item Active, Inactive, Under Maintenance or Disposed.</p><div class="stepv chipset"><span class="badge s-Active">Active</span><span class="badge s-Inactive">Inactive</span><span class="badge s-UnderMaintenance">Under Maintenance</span><span class="badge s-Disposed">Disposed</span></div></div></div></div></section>

<section class="lp-sec"><div class="in"><div class="lp-h rv"><h2>What people track</h2><p>From laptops to vehicles, all in one list.</p></div><div class="gal">
<figure class="rv"><img src="public/images/products/laptop.svg" alt="Laptop"><figcaption>Laptops and phones</figcaption></figure>
<figure class="rv" style="--i:1"><img src="public/images/products/chair.svg" alt="Office chair"><figcaption>Furniture</figcaption></figure>
<figure class="rv" style="--i:2"><img src="public/images/products/car.svg" alt="Car"><figcaption>Vehicles</figcaption></figure>
<figure class="rv" style="--i:3"><img src="public/images/products/box.svg" alt="Box"><figcaption>Everything else</figcaption></figure></div></div></section>

<section class="lp-sec alt" id="roles"><div class="in"><div class="lp-h rv"><h2>Built for two kinds of users</h2><p>Admins look after the system. Customers look after their own assets.</p></div>
<div class="roles"><div class="role dark rv"><span class="ri"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/></svg></span><h3>Admin</h3><ul><li>View every asset in the system</li><li>Add, edit and delete any asset</li><li>Manage categories</li><li>View and remove customers</li><li>System-wide dashboard</li><li>Activity log and CSV export</li></ul></div>
<div class="role rv" style="--i:2"><span class="ri"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg></span><h3>Customer</h3><ul><li>Add, view, edit and delete your own assets</li><li>Search and filter your list</li><li>Upload a photo for each asset</li><li>Track warranties on your dashboard</li><li>Export your assets to CSV</li><li>Private from other customers</li></ul></div></div></div></section>

<section class="lp-sec" id="faq"><div class="in faq2"><div class="rv"><h2>Questions</h2><p class="lead">Quick answers about how AssetFlow works.</p></div>
<div class="faq rv" style="--i:2">
<details><summary>Who can see my assets?</summary><p>You and the system admin. Customers can never open each other's assets.</p></details>
<details><summary>Can I add a photo?</summary><p>Yes. Upload a JPG, PNG, WebP or GIF up to 2 MB. Without a photo, a picture based on the category is shown.</p></details>
<details><summary>What do the statuses mean?</summary><p>Active means in use, Inactive means not in use, Under Maintenance means being repaired, and Disposed means no longer owned.</p></details>
<details><summary>Who manages categories?</summary><p>Admins create and edit categories. Customers pick one when they add an asset.</p></details></div></div></section>

<section class="lp-sec" style="padding-top:0"><div class="in"><div class="cta rv"><div><h2>Ready to see everything you own?</h2><p>Create an account and add your first asset today.</p></div>
<a class="btn" href="<?=logged()?'dashboard.php':'signup.php'?>"><?=logged()?'Open dashboard':'Create free account'?></a></div></div></section>
<?php include 'includes/footer.php'; ?>
