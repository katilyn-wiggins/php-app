<?php 
class DomDocumentParser {
  //created last - makes value stay like state
  private $doc; 
  // passing in a url
  public function __construct($url) {
      // making a request to go to url 
      $options = array(
        'http'=>array('method'=>"GET", 'header'=>"User-Agent: legoogBot/0.1\n")
      ); 
      $context = stream_context_create($options); 
      //passing in contents of website in to DomDocument - doc contains all of the html from that website
      $this->doc = new DomDocument(); 
      @$this->doc->loadHTML(file_get_contents($url, false, $context)); 
  }

  public function getlinks() {
    return $this->doc->getElementsByTagName("a"); //get all anchor tags on website
  }
}

?>