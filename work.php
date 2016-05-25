<?php include('perch/runtime.php');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]> ><! <![endif]-->
<html class='no-js' lang='en'>
  <!-- <![endif] -->
  <head>
    <meta charset='utf-8' />
    <meta content='IE=edge,chrome=1' http-equiv='X-UA-Compatible' />
    <title>Ryan DeCarlo | Work</title>
    <meta content='<?php perch_content('Site Description'); ?>' name='description' />
	<style>
	body{
		background: black url("../img/background2.jpg") no-repeat fixed center center / cover ;
		height:100%;
	}
	html{
		background-color:black;
	}
	</style>
    <script src="js/libs/modernizr-2.5.3.min.js" type="text/javascript"></script>
    <link href="css/site.css" media="screen" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <!--[if lt IE 7]> <p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p> <![endif]-->
    <div id='wrapper'>
      <div class='main' role='main'><header>
	  <div class="hinfo">Design + Creative Direction for television, film and video content.</div>
  <a class="logo" href='/'><img src="images/logo2.png" alt="Ryan DeCarlo"></a>
  <nav>
    <ul>
      <li class='first news'>
        <a title="News" href='/'>NEWS</a>
      </li>
      <li class="work pact pag">
        <a title="work" href='#'>All Work</a>
      </li>
      <li class="whatido">
        <a title="whatido" href='#whatido'>What I DO</a>
      </li>
	  <li class="showreels">
        <a title="showreels" href='#showreels'>SHOWREELS</a>
      </li>
	  <li class="bio">
        <a title="BIO" href='#bio'>BIO</a>
      </li>
	  <li class="last contact">
        <a title="Contact" href='#contact'>Contact</a>
      </li>
    </ul>
  </nav>
</header>
<section id='work'>
  <div class='clearfix' id='workContainer'>
    <div class='clearfix' id='workToggle'>
      <br />
      <ul>
        <li class='first active' data-section='work'>
          <a>
            All work
          </a>
        </li>
        <li data-section='awards'>
          <a>
            Awards
          </a>
        </li>
      </ul>
    </div>
    <div class='inner-content clearfix'>
      <div class="loader-container">
        <span class="loader"><span class="loader-inner"></span></span>
      </div>
      <div id='filter'>
        <ul>
          <li>
            <a>Capabilities</a>
            <ul>
              <li>
                <a data-filter='*' href='#all'>All</a>
              </li>
              <li>
                <a data-filter='.reel' href='#reel'>demo reel</a>
              </li>
              <li>
                <a data-filter='.storyboards' href='#storyboards'>storyboards</a>
              </li>
              <li>
                <a data-filter='.tv' href='#tv'>tv spots</a>
              </li>
              <li>
                <a data-filter='.trailers' href='#trailers'>trailers</a>
              </li>
              <li>
                <a data-filter='.content' href='#content'>content</a>
              </li>
              <li>
                <a data-filter='.broadcast' href='#broadcast'>broadcast</a>
              </li>
              <li>
                <a data-filter='.3ddesign' href='#3ddesign'>3d design</a>
              </li>
              <li>
                <a data-filter='.illustrations' href='#illustrations'>illustrations</a>
              </li> 
            </ul>
          </li>
        </ul>
      </div>
      <div id='scrollContainer'>
        <div id='workGrid'>
          <?php perch_content('Work'); ?>
        </div>
      </div>
    </div>
    <div id='awards'>
      <div id='awardsContainer'>
        <h2>AWARDS AND RECOGNITION TIMELINE</h2>
        <p>Most of the awards and recognitions were accomplished working along side other partners and studios.</p>
        <?php perch_content('Awards'); ?>
      </div>
    </div>
  </div>
</section>
<footer>
  <span>2015 &nbsp;ryan &nbsp;decarlo, &nbsp;inc.</span>
</footer></div>
    </div>
    <?php perch_content('About'); ?>
    <div id="next-overlay"></div>
    <script src='//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>
    <script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'></script>
    <script type='text/javascript'>
      //<![CDATA[
        window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')
        window.jQuery || document.write('<script src="js/libs/jquery-ui.min.js"><\/script>')
      //]]>
    </script>
    <script src="js/libs/jquery.isotope.min.js" type="text/javascript"></script>
    <script src="js/libs/shadowbox.js" type="text/javascript"></script>
    <script src="js/libs/jquery.mCustomScrollbar.js" type="text/javascript"></script>
    <script src="js/libs/jquery.mousewheel.min.js" type="text/javascript"></script> 
    <script src="js/site.js" type="text/javascript"></script>

    <script type='text/javascript'>
      //<![CDATA[
        window.jQuery || document.write('<\/script>')
        var _gaq=[['_setAccount','UA-5037149-1'],['_trackPageview']];
        (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g,s)}(document,'script'));
      //]]>
    </script>
	<script>
	$("document").ready(function() {
		setTimeout(function() {
		//	$("#filter ul li li:first-child a").trigger('click');
		},5000);
	});
	</script>
  </body>
</html>
