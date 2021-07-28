<?php 
include("config.php");
include("classes/SiteResultsProvider.php");

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
?>

<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Welcome To Legoog</title>
  <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=<?php echo time(); ?>">
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
      $resultsProvider = new SiteResultsProvider($conn); 

      $numResults =  $resultsProvider->getNumresults($term); 

      echo "<p class='resultsCount'>$numResults results found</p>"; 

      echo $resultsProvider->getResultsHtml(1, 20, $term); 
      ?>


    </div>
      


  </div>
</body>
</html>