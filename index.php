<?php include "search.php" ?>

<html lang="en">
  <meta name="robots" content="noindex">
  <head>
    <title>Custom Search | Secret Escapes</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,600,600i,700,700i|Source+Serif+Pro:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
    <div class="content-spacing section-spacing">


      <!-- Form & sidebar -->
      <div class="sidebar boxpad--xxl text">
        <h1 class="title title--lg">Custom Search</h1>
        <hr class="hr--left">
        <div>
          <p class="text text--xl">Enter your search terms below to test results. Use commas to separate keywords.</p>
          <p class="text text--xl">The fields are constructive, so if you use keywords "london", "rome" and "paris", you will get results from all three cities. The tags field then filters upon this selection for example using "spa" will give you only results from the above three cities tagged with "spa".</p>
          <div class="space--md"></div>
          <form name="search" id="searchForm" onsubmit="searchForm()">

            <label for='territory'>Territory:</label>
            <select name="territory" id="territory">
              <option value="es">United Kingdom</option>
              <option value="it">Italy</option>
              <option value="sv">Sweden</option>
            </select>
            <div class="space--xs"></div>

            <label for='keywords'>Keywords (e.g. Location, hotel name):</label>
            <?php
              if( searchQuery()['keywords'] ) {
                echo "<input type='text' id='keywords' name='keywords' value='" . searchQuery()['keywords'] . "'>";
              }else{
                echo "<input type='text' id='keywords' name='keywords'>";
              }
            ?>
            <div class="space--xs"></div>

            <label for='tags'>Tags (e.g. CMS tags):</label>
            <?php
              if( searchQuery()['tags'] ) {
                echo "<input type='text' id='tags' name='tags' value='" . searchQuery()['tags'] . "'>";
              }else{
                echo "<input type='text' id='tags' name='tags'>";
              }
            ?>

            <div class="space--sm"></div>
            <input type="submit" class="btn btn--orange">
            <a href="javascript:resetForm()" class="btn btn--subtle">Reset</a>
          </form>
        </div>
      </div>


      <!-- Search results -->
      <div class="search-results boxpad--xxl">

        <div class="search-results__title">
          <h2>
          <?php
            if( searchQuery() ) {
              echo "<span class='title'>Your search: </span>";
              echo search()['total'];
              echo " results";
            }else{
              echo "<span class='title'>Showing all sales</span>";
            }
          ?>
          </h2>

          <?php
            if( searchQuery()['keywords'] ) {
              echo "<p class='text--xl'><span class='text--bold'>Keywords: </span>" . searchQuery()['keywords'] . "</p>";
            }
            if( searchQuery()['tags'] ) {
              echo "<p class='text--xl'><span class='text--bold'>Tags: </span>" . searchQuery()['tags'] . "</p>";
            }
          ?>
        </div>

        <?php
          echo search()['results'];
        ?>
      </div>

    </div>

    <script type="text/javascript">

      // Retain territory choice on reload
      const urlParams = new URLSearchParams(window.location.search);
      const territoryQ = urlParams.get('territory');
      if(territoryQ){
        document.getElementById('territory').value = territoryQ;
      }else{
        document.getElementById('territory').value = 'uk';
      }

      function searchForm(){
        var curr_url = window.location.href;
        var url = curr_url.substring(0, curr_url.indexOf('?'));

        var territory = document.getElementById("territory");
        var keywords = document.getElementById("keywords");
        var tags = document.getElementById("tags");
        url += '?territory=' + territory.value;
        if( keywords.value && tags.value ){
          url += '&keywords=' + keywords.value + '&tags=' + tags.value;
        }else if(keywords.value){
          url += '&keywords=' + keywords.value;
        }else if(tags.value){
          url += '&tags=' + tags.value;
        }
        window.location.href = url;
      }
      function resetForm(){
        var url = window.location.href;
        var clean_url = url.substring(0, url.indexOf('?'));
        window.location.href = clean_url;
      }
    </script>
  </body>
</html>