<!DOCTYPE html>
<html lang="en">
<head>
	<title>{$pageTitle|htmlentities}</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="core/js/main.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.3.0/css/bulma.css"/>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bungee+Shade&display=swap" type="text/css" />
	<link rel="stylesheet" href="core/fonts/varsity/stylesheet.css" />
	<link rel="stylesheet" href="core/css/main.css" />
	{$pageMeta}
</head>
<body>
	<section class="hero is-warning header">
		<div class="hero-body">
			<header class="container">
				<div class="columns is-12 is-clearfix">
					<div class="column is-4">
						<h1 class="title title-left level-left"><a href="index.php?module=home">FOOD<span>2</span>TEASE</a></h1>
					</div>
					<div class="column is-8">
						<nav>
							<ul class="socials-topright">
								<li>
									<a href="mailto:food2tease@outlook.com" alt="mail ons" data-placement="left" data-toggle="tooltip" title="Mail ons"><span class="icon"><i class="fa fa-envelope"></i></span></a>
								<li>
									<a href="https://www.facebook.com/groups/2607279932936027" target="_blank" alt="bezoek onze facebook groep" data-placement="left" data-toggle="tooltip" title="Bezoek onze facebook groep"><span class="icon"><i class="fa fa-facebook-square"></i></span></a>
								</li>
							</ul>
						</nav>
					</div>
				</div>
				<div id="shopcartLayout" class="{option:oShopcartVisible}columns is-12 is-clearfix{/option:oShopcartVisible}{option:oShopcartHidden}hidden{/option:oShopcartHidden}">
					<div class="column is-9">&nbsp;</div>
          <div class="column is-3 card is-vertical hero is-info is-bold">
						<footer class="card-footer column is-auto has-text-centered">
		          <p><span id="shopcartQuantity">{$shopcartQuantity|htmlentities}</span> artikel<span id="shopcartPlural" class="{option:oShopcartNonPlural}hidden {/option:oShopcartNonPlural}hideable">en</span> &nbsp;&ndash;&nbsp;  &euro; <span id="shopcartTotal">{$shopcartTotal|htmlentities}</span></p>
						</footer>
						<footer class="card-footer{option:oShopcartRedirect} hidden{/option:oShopcartRedirect}">
	            <a href="{$shopcartUrl}" class="card-footer-item">Bekijk winkelmandje&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></a>
						</footer>
          </article>
				</div>
			</header>
		</div>
	</section>

	<section id="maincontent" class="clearfix">

	{$pageContent}
		<a class="button is-info has-text-centered" onclick="backToTop()" id="btnBackToTop" data-placement="left" data-toggle="tooltip" title="Terug naar boven">
			<div class="{option:oShopcartHidden}hidden{/option:oShopcartHidden} is-pulled-left" id="shopcartQuantitySmallDiv"># <span id="shopcartQuantitySmall">{$shopcartQuantitySmall|htmlentities}</span>&nbsp;&nbsp;</div>
			<span class="icon"><i class="fa fa-arrow-up"></i></span>
			<div class="{option:oShopcartHidden}hidden{/option:oShopcartHidden} is-pulled-right" id="shopcartTotalSmallDiv">&nbsp;&nbsp;&euro; <span id="shopcartTotalSmall">{$shopcartTotalSmall|htmlentities}</span></div>
		</a>
	</section>

	<footer class="footer hero is-warning">
		<div class="content is-medium has-text-centered columns is-3">
			<p class="column is-one-third has-text-right-tablet"><span class="icon"><i class="fa fa-phone"></i></span> Telnr. 0498 / 79 24 34</p>
			<p class="column is-one-third"><span class="icon"><i class="fa fa-envelope"></i></span> food2tease@outlook.com</p>
			<p class="column is-one-third has-text-left-tablet"><span class="icon"><i class="fa fa-code"></i></span> Made by Chris De Smedt 2020</p>
		</div>
	</footer>
</body>
</html>
