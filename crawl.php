<?php 
include("classes/DomDocumentParser.php");
//goes to specified website, grabs document element by tag name a=anchor then selects the href name from those elements and returns them to the user
function followLinks($url) {
  //all the links
  $parser = new DomDocumentParser($url);  

  $linkList = $parser->getLinks(); 
  //loop over each array and store the links
  foreach($linkList as $link) {
    $href = $link->getAttribute("href");
    echo $href . "<br>"; 
  }
}

$startUrl = "http://www.reddit.com"; 
followLinks($startUrl);


?>