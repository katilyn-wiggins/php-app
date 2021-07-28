<?php 
include("config.php");
include("classes/DomDocumentParser.php");
//goes to specified website, grabs document element by tag name a=anchor then selects the href name from those elements and returns them to the user

$alreadyCrawled = array(); 
$crawling = array(); 
$alreadyFoundImages = array(); 

function insertLink($url, $title, $description, $keywords) {
  global $conn; 

  $query = $conn->prepare("INSERT INTO sites(url, title, description, keywords)
                          VALUES(:url, :title, :description, :keywords)"); //:name = placeholder 
  $query->bindParam(":url", $url); //binding placeholder to value
  $query->bindParam(":title", $title); 
  $query->bindParam(":description", $description); 
  $query->bindParam(":keywords", $keywords); 

  return $query->execute(); 
}

function insertImage($url, $src, $alt, $title) {
  global $conn; 

  $query = $conn->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
                          VALUES(:siteUrl, :imageUrl, :alt, :title)"); //:name = placeholder 
  
  $query->bindParam(":siteUrl", $url); //binding placeholder to value
  $query->bindParam(":imageUrl", $src); 
  $query->bindParam(":alt", $alt); 
  $query->bindParam(":title", $title); 

  return $query->execute(); 
}

//check if link already exists in database 
function linkExists($url) {
  global $conn; 

  $query = $conn->prepare("SELECT * FROM sites WHERE url = :url");
                 
  $query->bindParam(":url", $url); 
  $query->execute();
  
  return $query->rowCount() != 0;   
}

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

function getDetails($url) {

    global $alreadyFoundImages; 

    $parser = new DomDocumentParser($url); 

    $titleArray = $parser->getTitleTags(); 

    //making sure the title array is not empty
    if(sizeOf($titleArray) == 0 || $titleArray->item(0) == NULL) {
      return; 
    } 

    $title = $titleArray->item(0)->nodeValue;  //title of the first item in the array
    $title = str_replace("\n", "", $title); 

    if($title == "") { //if no title exists, ignore link
      return; 
    }

    //keywords and description
    $description = "";
    $keywords = "";

    $metasArray = $parser->getMetaTags(); 

    foreach($metasArray as $meta) {
      if($meta->getAttribute("name") == "description") {
        $description = $meta->getAttribute("content"); 
      }
      if($meta->getAttribute("name") == "keywords") {
        $description = $meta->getAttribute("content"); 
      }
    }

      $description = str_replace("\n", "", $title); 
      $keywords = str_replace("\n", "", $title); 

      if(linkExists($url)) {
        echo "$url already exists in database<br>";
      } 
      else if(insertLink($url, $title, $description, $keywords))
      {
        echo "Success: $url<br>";
      }
      else {
        echo "Error: Failed to insert $url<br>"; 
      }

      $imageArray = $parser->getImages(); 
      foreach($imageArray as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if(!$title && !$alt) {
          continue; 
        }

        $src = createLink($src, $url); //converts image address to link

        if(!in_array($src, $alreadyFoundImages)) {
          $alreadyFoundImages[] = $src; 

          echo "Insert: " . insertImage($url, $src, $alt, $title); 
        }
      }
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

      getDetails($href); 
      //insert href
    }
    else return; 
  }

  array_shift($crawling); 

  foreach($crawling as $site) {
    followLinks($site); 
  }
}

$startUrl = "http://www.reddit.com"; 
followLinks($startUrl);


?>