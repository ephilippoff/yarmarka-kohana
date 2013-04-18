<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">

	<title>Admin</title>
	<?=HTML::style('bootstrap/css/bootstrap.min.css')?>
	 <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
</head>
<body>
<?=HTML::script('http://code.jquery.com/jquery-latest.js')?>
<?=HTML::script('bootstrap/js/bootstrap.min.js')?>

<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
	<div class="container">
	  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </a>
	  <a class="brand" href="/">yarmarka.biz</a>
	  <div class="nav-collapse collapse">
		<ul class="nav">
		  <li class="active">
		  	<a href="/khbackend">Home</a>
	 	  </li>
		  <li><a href="<?=Url::site('khbackend/users/logout')?>">Log Out</a></li>
		</ul>
	  </div><!--/.nav-collapse -->
	</div>
  </div>
</div>

<div class="container">
	<?=$content?>
</div>
</body>
</html>
