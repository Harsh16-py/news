<?php
  // Fetching all the Navbar Data
  require('./includes/nav.inc.php');
?>

<!-- Article List Container -->
<section class="py-1 category-list">
  <div class="container">
    <h2 class="headings">Articles</h2>
    <div class="card-container">
      <?php
        $limit = 6;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Declaring Article Query
        $articleQuery = "";

        // If we get category_id from URL and it is not null
        if(isset($_GET['id']) && $_GET['id'] != '') {
          $category_id = $_GET['id'];

          // ✅ FIXED SQL SYNTAX
          $articleQuery = "SELECT category.category_name, category.category_color, article.*
                           FROM category, article
                           WHERE article.category_id = category.category_id
                           AND category.category_id = {$category_id}
                           AND article.article_active = 1
                           ORDER BY article.article_id DESC
                           LIMIT {$offset}, {$limit}";
        } else {
          // ✅ FIXED SQL SYNTAX
          $articleQuery = "SELECT category.category_name, category.category_color, article.*
                           FROM category, article
                           WHERE article.category_id = category.category_id
                           AND article.article_active = 1
                           ORDER BY article.article_id DESC
                           LIMIT {$offset}, {$limit}";
        }

        // ✅ EXECUTE + ERROR DEBUGGING
        $result = mysqli_query($con, $articleQuery);
        if (!$result) {
          die("Query Failed: " . mysqli_error($con) . "<br>SQL: " . $articleQuery);
        }

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

            $article_title = substr($article_title, 0, 55).' . . . . .';
            $article_desc = substr($article_desc, 0, 150).' . . . . .';

            $new = false;
            $tdy = time();
            $article_date = strtotime($article_date);
            $datediff = round(($tdy - $article_date) / (60*60*24));
            if($datediff < 2) $new = true;

            $bookmarked = false;
            if(isset($_SESSION['USER_ID'])) {
              $bookmarkQuery = "SELECT * FROM bookmark 
                                WHERE user_id = {$_SESSION['USER_ID']}
                                AND article_id = {$article_id}";
              $bookmarkResult = mysqli_query($con, $bookmarkQuery);
              $bookmarkRow = mysqli_num_rows($bookmarkResult);
              if($bookmarkRow > 0) $bookmarked = true;
            }

            createArticleCard($article_title, $article_image, 
              $article_desc, $category_name, $category_id, $article_id, 
              $category_color, $new, $article_trend, $bookmarked);
          }
        } else {
          echo "</div>";
          createNoArticlesCard();
        }
      ?>
    </div>

    <?php
      // Pagination
      $paginationQuery = "";
      if(isset($_GET['id']) && $_GET['id'] != '') {
        $category_id = $_GET['id'];
        $paginationQuery = "SELECT * FROM article WHERE category_id = {$category_id} AND article_active = 1";
      } else {
        $paginationQuery = "SELECT * FROM article WHERE article_active = 1";
      }

      $paginationResult = mysqli_query($con, $paginationQuery);
      if(mysqli_num_rows($paginationResult) > 0) {
        $total_articles = mysqli_num_rows($paginationResult);
        $total_page = ceil($total_articles / $limit);
    ?>

    <div class="text-center py-2">
      <div class="pagination">
        <?php
          $cat_id = "";
          if(isset($_GET['id']) && $_GET['id'] != '') {
            $cat_id = 'id='.$_GET['id'].'&';
          }

          if($page > 1){
            echo '<a href="articles.php?'.$cat_id.'page='.($page - 1).'">&laquo;</a>';
          }

          for($i = 1; $i <= $total_page; $i++) {
            $active = ($i == $page) ? "page-active" : "";
            echo '<a href="articles.php?'.$cat_id.'page='.$i.'" class="'.$active.'">'.$i.'</a>';
          }

          if($total_page > $page){
            echo '<a href="articles.php?'.$cat_id.'page='.($page + 1).'">&raquo;</a>';
          }
        ?>
      </div>
    </div>
    <?php } ?>
  </div>
</section>

<?php
  // Fetching all the Footer Data
  require('./includes/footer.inc.php');
?>
