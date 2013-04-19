<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">

	<title>Admin</title>
	<?=HTML::style('bootstrap/css/bootstrap.min.css')?>
	<?=HTML::style('bootstrap/css/bootstrap-responsive.min.css')?>
	 <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      input[type="text"] {
        height: auto;
      }
    </style>
</head>
<body>
<?=View::factory('layouts/admin_head')?>
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="/">yarmarka.biz</a>
				<div class="nav-collapse collapse navbar-inverse-collapse">
					<?php if (Auth::instance()->have_access_to('user')) : ?>
					<ul class="nav">
						<li class="dropdown <?=($module_name == 'user') ? 'active' : ''?>">
							<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Users <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=Url::site('khbackend/users/index')?>">List</a></li>
							</ul>
						</li>
						<li><a href="<?=Url::site('khbackend/users/logout')?>">Log Out</a></li>
					</ul>
					<?php endif; ?>
				</div>
		</div>
  </div>
</div>

<div class="container">
	<div id="loading" style="position:absolute; z-index:99999; background-color:red; color: white; top: 10; left: 5; display:none">
	  Loading...
	</div>
	<?=$content?>
</div>
</body>
</html>
