<?php

/**
 * ProcessWire 2.x Admin Markup Template
 *
 * Copyright 2010 by Ryan Cramer / Copyright 2012 by Nikola Vidoni
 *
 */

Debug::timer('futura_timer');

$searchForm = $user->hasPermission('page-edit') ? $modules->get('ProcessPageSearch')->renderSearchForm() : '';
$bodyClass = $input->get->modal ? 'modal' : '';
if(!isset($content)) $content = '';

$config->styles->prepend($config->urls->adminTemplates . "styles/main.css");
$config->styles->append($config->urls->adminTemplates . "styles/ui.css");
$config->scripts->append($config->urls->adminTemplates . "scripts/sticky-sidebar.js");
$config->scripts->append($config->urls->adminTemplates . "scripts/menu.js");
$config->scripts->append($config->urls->adminTemplates . "scripts/main.js");
$config->scripts->append($config->urls->adminTemplates . "scripts/cufon-yui.js");
$config->scripts->append($config->urls->adminTemplates . "scripts/adventor.font.js");

$browserTitle = wire('processBrowserTitle');
if(!$browserTitle) $browserTitle = __(strip_tags($page->get('title|name')), __FILE__) . ' &bull; ProcessWire';

/*
 * Dynamic phrases that we want to be automatically translated
 *
 * These are in a comment so that they register with the parser, in place of the dynamic __() function calls with page titles.
 *
 * __("Pages");
 * __("Setup");
 * __("Modules");
 * __("Access");
 * __("Admin");
 *
 */

?>
<!DOCTYPE html>
<html lang="<?php echo __('en', __FILE__); // HTML tag lang attribute
	/* this intentionally on a separate line */ ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $browserTitle; ?></title>

	<script type="text/javascript">
		<?php

		$jsConfig = $config->js();
		$jsConfig['debug'] = $config->debug;
		$jsConfig['urls'] = array(
			'root' => $config->urls->root,
			'admin' => $config->urls->admin,
			'modules' => $config->urls->modules,
			'core' => $config->urls->core,
			'files' => $config->urls->files,
			'templates' => $config->urls->templates,
			'adminTemplates' => $config->urls->adminTemplates,
			);
		?>

		var config = <?php echo json_encode($jsConfig); ?>;
	</script>

	<?php foreach($config->styles->unique() as $file) echo "\n\t<link type='text/css' href='$file' rel='stylesheet' />"; ?>

	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="<?php echo $config->urls->adminTemplates; ?>styles/ie.css" />
	<![endif]-->

	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo $config->urls->adminTemplates; ?>styles/ie7.css" />
	<![endif]-->

	<?php foreach($config->scripts->unique() as $file) echo "\n\t<script type='text/javascript' src='$file'></script>"; ?>

	<script type="text/javascript">Cufon.replace('h1, h2, #latest, #newest, #loginBox #ProcessLoginForm .ui-widget-header, #loginBox #InputfieldForm1 .ui-widget-header');</script>

</head>

<?php if($user->isGuest()) { ?>

<body class="login">

	<div id="background"></div>

    <div id="loginBox">

    	<div class="header">
        	<div class="logo">ProcessWire</div>
        </div>

        <?php echo $content?>

    </div>

    <?php if(count($notices)) include($config->paths->adminTemplates . "notices.inc"); ?>

</body>

<?php } else { ?>

<body<?php if($bodyClass) echo " class='$bodyClass'"; ?>>

<div id="wrapper">

<div id="main">

	<div id="header" class="header">

		<div class="container">

            <p id="logo">ProcessWire</p>

			<div id="nav" class="nav">
				<ul>
					<?php include($config->paths->templatesAdmin . "topnav.inc"); ?>
				</ul>
			</div>

			<?php if(!$user->isGuest()): ?>

            <ul id='breadcrumbs'>
			<?php
			foreach($this->fuel('breadcrumbs') as $breadcrumb) {
				$title = __($breadcrumb->title, __FILE__);
				echo "\n\t\t\t\t<li><a href='{$breadcrumb->url}'>{$title}</a></li>";
			}
            ?>
            </ul>

            <div id="user">You are logged in as <span><?php echo $user->name?></span></div>

			<?php endif; ?>

			<?php if(!$user->isGuest()): ?>

            <ul id="info">

                <?php if($user->hasPermission('profile-edit')): ?>
                <li><a class="profile" href="<?php echo $config->urls->admin; ?>profile/"><?php echo ucfirst (__('profile', __FILE__)); ?></a></li>
                <?php endif; ?>

                <li><a class="view" href="<?php echo $config->urls->root; ?>"><?php echo ucfirst (__('Site', __FILE__)); ?></a></li>

                <li><a class="logout" href="<?php echo $config->urls->admin; ?>login/logout/"><?php echo ucfirst (__('logout', __FILE__)); ?></a></li>

            </ul>

            <?php endif; ?>

			<h1 class="title"><?php echo __(strip_tags($this->fuel->processHeadline ? $this->fuel->processHeadline : $page->get("title|name")), __FILE__); ?></h1>

		</div>

	</div>

</div>

<?php if(count($notices)) include($config->paths->adminTemplates . "notices.inc"); ?>

<div id="content" class="content">

    <div class="container">

        <div class="main-content">

            <?php if(trim($page->summary)) echo "<h2>{$page->summary}</h2>"; ?>

            <?php if($page->body) echo $page->body; ?>

            <?php echo $content?>

			<?php if($config->debug && $this->user->isSuperuser()) include($config->paths->adminTemplates . "debug.inc"); ?>

        </div>

    </div>

    <div id="sidebar">

        <div id="search"><?php echo tabIndent($searchForm, 3); ?></div>

        	<div id="latest-updates">

            <div id="latest">Latest updates</div>

				<?php $modified = $pages->find('limit=3, sort=-modified'); ?>

                <ul>

                    <?php foreach($modified as $m){
                        if ($m->editable()) {
                            echo "<li><a href='".$config->urls->admin."page/edit/?id={$m->id}'><span class='date'>". date('d.m.', $m->modified) ."</span> " . $m->title . "</a></li>\n";
                        }
                    } ?>

                </ul>

            </div>

            <div id="newest-added">

                <div id="newest">Newest added</div>

                <?php $newest = $pages->find('limit=3, sort=-created'); ?>

                <ul>

                    <?php foreach($newest as $n){
                        if ($n->editable()) {
                            echo "<li><a href='".$config->urls->admin."page/edit/?id={$n->id}'><span class='date'>". date('d.m.', $n->created) ."</span> " . $n->title . "</a></li>\n";
                        }
                    } ?>

                </ul>

			</div>

            <div id="top"><span>Back to top</span><a href="#">Top</a></div>

        </div>

    </div>

</div>

</div>

<div id="footer" class="footer">

    <div class="container">

        <div id="left">

            <a href="http://www.processwire.com">ProcessWire <?php echo $config->version; ?></a> <span>|</span> Copyright &copy; <?php echo date("Y"); ?> by Ryan Cramer

        </div>

        <div id="right">

            <div id="code">

                <?php echo "This page was created in <span>". Debug::timer('futura_timer') ."</span> seconds"; ?>

            </div>

        </div>

    </div>

</div>

</body>

<?php } ?>

</html>
