<?php 
include("classes/DomDocumentParser.php");
//goes to specified website, grabs document element by tag name a=anchor then selects the href name from those elements and returns them to the user

$alreadyCrawled = array(); 
$crawling = array(); 

function createLink($src, $url) {
  //convert relative links to absolute links
  $scheme = parse_url($url)["scheme"]; //scheme: http https
  $host = parse_url($url)["host"]; //host: www.hello.com 

    //if website is //www.website.com
  if(substr($src, 0, 2) == "//") {
    $src = $scheme . ":" . $src; 
  } // /about/about-us.php
  else if(substr($src, 0, 1) == "/") {
    $src = $scheme . "://" . $host . $src; 
  } // ./about/about-us.php -- current directory
  else if(substr($src, 0, 2) == "./") {
    $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1); 
  } // ../about/about-us.php
  else if(substr($src, 0, 3) == "../") {
    $src = $scheme . "://" . $host . "/" . $src; 
  } // about/about-us.php
  else if(substr($src, 0, 5) !== "https" && substr($src, 0, 4) !== "http") {
    $src = $scheme . "://" . $host . "/" . $src; 
  }

  return $src; 

}

function followLinks($url) {

  global $alreadyCrawled; 
  global $crawling; 

  //all the links
  $parser = new DomDocumentParser($url);  

  $linkList = $parser->getLinks(); 
  //loop over each array and store the links
  foreach($linkList as $link) {
    $href = $link->getAttribute("href");

    if(strpos($href, "#") !== false) {
      continue; 
    }
    else if(substr($href, 0, 11) == "javascript:") {
      continue;
    }

    $href = createLink($href, $url); 

    if(!in_array($href, $alreadyCrawled)) {
      $alreadyCrawled[] = $href; //put it at next item in array
      $crawling[] = $href; 

      //insert href
    }

    echo $href . "<br>"; 
  }

  array_shift($crawling); 

  foreach($crawling as $site) {
    followLinks($site); 
  }
}

$startUrl = "http://www.reddit.com"; 
followLinks($startUrl);


?>