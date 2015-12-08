<style type="text/css">
<!--
#subsubmenu li {
	display: inline;
	line-height: 170%;
	list-style: none;
	text-align: center;
}

#subsubmenu {
  font-size: 0.9em;
	background: #CDD9E2;
	border-bottom: none;
	margin: 0;
	color: #4F5D69;
	padding: 6px 2em 0 5em;
}

#subsubmenu .current {
	background: #f9fcfe;
	color: black;
}

#subsubmenu a {
	border: none;
	color: #4F5D69;
	font-size: 12px;
	padding: 3px 1em 2px 1em;
}

#subsubmenu a:hover {
	background: #89A5BB;
	color: #393939;
}

#subsubmenu li {
	line-height: 150%;
}
-->
</style>
<ul id="subsubmenu">
<?php foreach ($submenu AS $key=>$value) { ?>
	  <li><a <?php if($value['selected']) { ?>class="current"<?php } ?> href="<?php echo $url ?>&amp;sub=<?php echo $value['id'] ?>"><?php echo $value['name'] ?></a></li>
<?php } ?>
</ul>
