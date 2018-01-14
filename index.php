<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Language" content="en-us" />
  <meta http-equiv="imagetoolbar" content="false" />
  <meta name="MSSmartTagsPreventParsing" content="true" />
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ForM@Ter API</title>
  
   <link href="images/formater_favicon-40x40.png" rel="icon" sizes="32x32">
   
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,300,400italic,500">
  <link href="/css/application.css" rel="stylesheet" type="text/css" />
 
  
</head>


<body class="api ">
  

<header class="site-header">
  <div id="header" class="container">
    <a class="site-header-logo mt-1" href="/"><img src="/images/logo-formater-transparent.png" alt="API ForM@Ter" height="60px"></a>
    <nav class="site-header-nav">
     
      <a class="site-header-nav-item" href="https://www.poleterresolide.fr/">Portal</a>
      
      <a class="site-header-nav-item" href="https://github.com/terresolide/api.formater/wiki">API Docs</a>
       <a class="site-header-nav-item" href="https://github.com/terresolide/api.formater/">On GitHub</a>
    
    </nav>
  </div>
</header>


  <div class="container">
    <div class="sub-nav mt-5 mb-3">
      <h2><a href="#">REST API ForM@Ter</a></h2>
      <ul>
  
 <!--   <li><a href="/v3/" class="active">Reference</a></li>
  <li><a href="/v3/guides/">Guides</a></li>
  <li><a href="/v3/libraries/">Libraries</a></li>-->
  
</ul>

    </div>

 
<p>Search Observatories</p>

<pre><code>http://formater.art-sciences.fr/cds/bcmt/obs?start=2011-05-01&end=2018-01-01&bbox=-20,2,70,90
</code></pre>
<div>where <ul>
<li><code>bcmt</code> is a data center,</li>
<li> <code>obs</code> is for observatories</li>
</ul>
<div>the parameters
<ul>
<li><code>2011-11-05</code> is the start date </li>
<li><code>2018-01-01</code> is the end date</li>
<li><code>-20,2,70,90</code> is used to describe bbox (longitude min, latitude min, longitude max, latitude max)</li>
</ul>
</div>
</div>
<p>Search Data for one observatory</p>

<pre><code>http://formater.art-sciences.fr/cds/bcmt/data/ams?start=2011-05-01&end=2018-01-01
</code></pre>
<div>where <ul>
<li><code>bcmt</code> is a data center,</li>
<li> <code>data</code> is for search data</li>
<li><code>ams</code> code of the observatory</li>
</ul>
<div>the parameters
<ul>
<li><code>2011-11-05</code> is the start date </li>
<li><code>2018-01-01</code> is the end date</li>
</ul>
</div>
</div>
</body>
</html>




