<?php



function search(){
  /* ============================================
   * API Connection and config
   */
  $territory = "es";
  $territoryQ = isset($_GET['territory']);
  if($territoryQ){
    $territory = $_GET['territory'];
  }

  // Correct Secret Escapes site for territory
	$seSite = 'https://www.secretescapes.com';
	if( $territory == 'it' ){
		$seSite = 'https://it.secretescapes.com';
	}else if( $territory == 'sv' ){
		$seSite = 'https://www.secretescapes.se';
	}else if( $territory == 'de' ){
		$seSite = 'https://www.secretescapes.de';
	}else{
		$seSite = 'https://www.secretescapes.com';
	}

  $seapitoken = "90370f0a-cc20-46a7-9934-a1cc4df00502";
  $saleDataURL = "https://api.secretescapes.com/v4/sales?se-api-token=".$seapitoken."&affiliate=".$territory;

  $json = file_get_contents($saleDataURL);
  $rawsales = json_decode($json, true);
  // check sale is live
  $sales = [];
  foreach($rawsales as $sale){
    $saleStartDate = new DateTime($sale['start']);
    $currentDate = new DateTime();
    if($saleStartDate <= $currentDate){
      // live
      array_push($sales,$sale);
    }
  }


  $outputArray = [];
  /* ============================================
   * KEYWORD FILTERING
   */
  $keywordsQ = isset($_GET['keywords']);
  if($keywordsQ AND strlen($_GET['keywords']) >= 1 ){
    $filterKeywords = $_GET['keywords'];
    // separate keywords by comma separation
    $keywordFilterArray = explode(', ', $filterKeywords);

    // FILTERS - Checking set fields against filter
    $mergedArray = [];
    foreach($keywordFilterArray as $filter){
      $filteredArray = array_filter($sales, function($sales) use($filter){
        if( stripos(" ".$sales['editorial']['destinationName']." ", $filter ) ){
          return true;
        }else if( stripos(" ".$sales['location']['city']['name']." ", $filter ) ){
          return true;
        }else if( stripos(" ".$sales['location']['country']['name']." ", $filter ) ){
          return true;
        }else if( stripos(" ".$sales['editorial']['reasonToLove']." ", $filter ) ){
          return true;
        }else if( stripos(" ".$sales['editorial']['title']." ", $filter ) ){
          return true;
        }
      });
      array_push($mergedArray, $filteredArray);
    }
    $mergedArray = call_user_func_array('array_merge', $mergedArray);
    // print_r($mergedArray);
    $outputArray = array_values($mergedArray); // reset array key values
  }



  /* ============================================
   * TAG FILTERING
   */

  $filterByTags = $sales;
  if( sizeof($outputArray) > 0 ){
    $filterByTags = $outputArray;
  }

  $tagsQ = isset($_GET['tags']);
  if( $tagsQ AND strlen($_GET['tags']) >= 1 ){
    $filterTags = $_GET['tags'];

    // separate tags by comma separation
    $tagFilterArray = explode(', ', $filterTags);

    // FILTERS - Checking set fields against filter
    $mergedArray = [];
    foreach($tagFilterArray as $filter){
      $filteredArray = array_filter($filterByTags, function($filterByTags) use($filter){

        for($i = 0; $i <= count($filterByTags['tags'])-1; $i++){
          $tag = $filterByTags['tags'][$i];
          if( stripos( " ".$tag, $filter ) ){
            return true;
          }
        }
      });
      array_push($mergedArray, $filteredArray);
    }
    $mergedArray = call_user_func_array('array_merge', $mergedArray);
    // print_r($mergedArray);
    $outputArray = array_values($mergedArray); // reset array key values

  }

  if( !sizeof($outputArray) > 0 ){
    $outputArray = $sales;
  }

  $filteredSales = $outputArray;
  /* ============================================
   * DISPLAY RESULTS
   */

  $total = sizeof($filteredSales);
  $results = '';
  for($i = 0; $i <= count($filteredSales)-1; $i++){
    $item = $filteredSales[$i];
    $tags = '';
    for($i2 = 0; $i2 <= count($item['tags'])-1; $i2++){
      $tags .= $item['tags'][$i2] . ", ";
    }
    $results.= "<div class='sale'>
        <p>Title: ".$item['editorial']['title']."</p>
        <p>Location: ".$item['editorial']['destinationName']."</p>
        <p>City: ".$item['location']['city']['name']."</p>
        <p>Country: ".$item['location']['country']['name']."</p>
        <p>Reason to love: ".$item['editorial']['reasonToLove']."</p>
        <p>Tags: ".$tags."</p>
        <p><a href='".$seSite.$item['links']['sale']."'>View sale</a></p>
      </div>";
    $tags=''; // reset tags for each sale
  }

  return array('results' => $results, 'total' => $total, 'json' => $filteredSales);

}



/* ============================================
 * Display search query on page
 */
function searchQuery(){
  $keywordsQ = isset($_GET['keywords']);
  $tagsQ = isset($_GET['tags']);
  $keywords = '';
  $tags = '';

  if($keywordsQ AND $tagsQ){
    $keywords = $_GET['keywords'];
    $tags = $_GET['tags'];
    return array('keywords' => $keywords, 'tags' => $tags);

  }else if($keywordsQ){
    $keywords = $_GET['keywords'];
    return array('keywords' => $keywords, 'tags' => $tags);

  }else if($tagsQ){
    $tags = $_GET['tags'];
    return array('keywords' => $keywords, 'tags' => $tags);

  }else{
    return false;
  }

}