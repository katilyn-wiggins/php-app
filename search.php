<?php 
include("config.php");
include("classes/SiteResultsProvider.php");
include("classes/ImageResultsProvider.php");

  if(isset($_GET["term"])) {
    $term = $_GET["term"];
  } 
  else {
    exit("You must enter a search term");
    //exit = stop executing all remaining code 
  }

  if(isset($_GET["type"])) {
    $type = $_GET["type"];
  } 
  else {
  $type = "sites";  
  }

  $page = isset($_GET["page"]) ? $_GET["page"] : 1; //if we don't specific a page in the url it will be 1
?>

<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Welcome To Legoog</title>
  <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=<?php echo time(); ?>">

  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</head>
<body>

  <div class="wrapper"> 

    <div class="header">

      <div class="headerContent">

        <div class="logoContainer">

            <a href="index.php">
              <img src="assets/images/legoog.png" alt="google" height="150px">
            </a>

        </div>

        <div class="searchContainer">
          <form action="search.php" method="GET">
           <div class="searchBarContainer">

             <input type="hidden" name="type" value="<?php echo $type; ?>">
              <input class="searchBox" type="text" name="term" value="<?php 
              echo $term; ?>">
                <button class="searchButton">
                  <img src="assets/images/search.png" /> 
                </button>
            </div>
          </form>
        </div>

      </div>

      <div class="tabsContainer">
          <ul class="tabList">
            <li class="<?php echo $type== 'sites' ? 'active' :  '' ?>">
             <!-- use single quotes to insert php so you don't have to open a new php block -->
              <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a>
            </li>
            <li class="<?php echo $type== 'images' ? 'active' :  '' ?>">
              <a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a>
            </li>
          </ul>

        </div>
    </div>

    <div class="mainResultsSection">
      <?php 
      if($type == "sites") {
        $resultsProvider = new SiteResultsProvider($conn); 
        $pageSize = 20; 
      } 
      else {
        $resultsProvider = new ImageResultsProvider($conn); 
        $pageSize = 30; 
      }
      
      $numResults =  $resultsProvider->getNumresults($term); 

      echo "<p class='resultsCount'>$numResults results found</p>"; 

      echo $resultsProvider->getResultsHtml($page, $pageSize, $term); 
      ?>


    </div>
  
    <div class="paginationContainer">
      <div class="pageButtons">
          <div class="pageNumberContainer">
            <img src="assets/images/page-start.png">
          </div> 

          <?php 
            $pagesToShow = 10; 
            $numPages = ceil($numResults / $pageSize);
            $pagesLeft = min($pagesToShow, $numPages); 
            $currentPage = $page - floor($pagesToShow / 2);  
            
            if($currentPage < 1 ){
              $currentPage = 1; 
            }

            if($currentPage + $pagesLeft > $numPages + 1) {
              $currentPage = $numPages + 1 - $pagesLeft;
            }

            while($pagesLeft != 0 && $currentPage <= $numPages) {

              if($currentPage == $page) {
                echo "<div class='pageNumberContainer'>
                <img src='assets/images/active-page.png'>
                <span class='pageNumber'>$currentPage</span>
              </div>";
              } else {
                echo "<div class='pageNumberContainer'>
                <a href='search.php?term=$term&type=$type&page=$currentPage'>
                  <img src='assets/images/next-page.png'>
                  <span class='pageNumber'>$currentPage</span>
                </a>
              </div>";
              }
              
             

              $currentPage++; 
              $pagesLeft--; 

            }

          ?>


          <div class="pageNumberContainer">
            <img src="assets/images/final-page.png">
          </div> 
      </div> 
    </div>

  </div>
	<script type="text/javascript" src="assets/js/script.js"></script>
</body>
</html>