<?php
  // Fetching all the Navbar Data
  require('./includes/nav.inc.php');

  // Fetching all the Slider Data
  require('./includes/slider.inc.php');
?>

<!-- Article List Container -->
<section class="py-1 category-list">
  <div class="container">
    <h2 class="headings">Articles</h2>
    <div class="card-container">
      <?php
        // Article Query to fetch maximum 5 latest articles
        $articleQuery = " SELECT category.category_name, category.category_color, article.*
                          FROM category, article
                          WHERE article.category_id = category.category_id
                          AND article.article_active = 1
                          ORDER BY article.article_id DESC LIMIT 5";
        
        // Running Article Query 
        $result = mysqli_query($con, $articleQuery);

        // Check if query failed
        if (!$result) {
          die("Article Query Failed: " . mysqli_error($con));
        }

        // Count the number of returned rows
        $row = mysqli_num_rows($result);
        
        if($row > 0) {
          while($data = mysqli_fetch_assoc($result)) {
            
            $category_color = $data['category_color'];
            $category_name = $data['category_name'];
            $category_id = $data['category_id'];
            $article_id = $data['article_id'];
            $article_title = $data['article_title'];
            $article_image = $data['article_image'];
            $article_desc = $data['article_description'];
            $article_date = $data['article_date'];
            $article_trend = $data['article_trend'];

            // Trim title and description
            $article_title = substr($article_title, 0, 55) . ' . . . . .';
            $article_desc = substr($article_desc, 0, 150) . ' . . . . .';

            // Check if it's a new article
            $new = false;
            $tdy = time();
            $article_date = strtotime($article_date);
            $datediff = round(($tdy - $article_date) / (60 * 60 * 24));

            if ($datediff < 2) {
              $new = true;
            }

            // Check if user bookmarked this article
            $bookmarked = false;

            if (isset($_SESSION['USER_ID'])) {
              $bookmarkQuery = "SELECT * FROM bookmark 
                                WHERE user_id = {$_SESSION['USER_ID']}
                                AND article_id = {$article_id}";
              $bookmarkResult = mysqli_query($con, $bookmarkQuery);

              if ($bookmarkResult && mysqli_num_rows($bookmarkResult) > 0) {
                $bookmarked = true;
              }
            }

            // Create article card
            createArticleCard(
              $article_title,
              $article_image,
              $article_desc,
              $category_name,
              $category_id,
              $article_id,
              $category_color,
              $new,
              $article_trend,
              $bookmarked
            );
          }
        }

        // Add "more" card
        createMoreCard('./articles.php');
      ?>
    </div>
  </div>
</section>

<!-- Category List Container -->
<section class="py-1 category-list">
  <div class="container">
    <h2 class="headings">Categories</h2>
    <div class="card-container">
      <?php
        // Category Query to fetch 5 random categories
        $categoryQuery = "SELECT * FROM category ORDER BY RAND() LIMIT 5";
        
        // Run Category Query
        $result = mysqli_query($con, $categoryQuery);

        // Check if query failed
        if (!$result) {
          die("Category Query Failed: " . mysqli_error($con));
        }

        $row = mysqli_num_rows($result);

        if ($row > 0) {
          while ($data = mysqli_fetch_assoc($result)) {
            $category_id = $data['category_id'];
            $category_name = $data['category_name'];
            $category_image = $data['category_image'];
            $category_desc = $data['category_description'];

            // Create category card
            createCategoryCard($category_name, $category_image, $category_desc, $category_id);
          }
        }

        // Add "more" card
        createMoreCard('./categories.php');
      ?>
    </div>
  </div>
</section>

<?php
  // Fetching all the Footer Data
  require('./includes/footer.inc.php');
?>
