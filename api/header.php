<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<header class="header">

   <section class="flex">

      <a href="index.php" class="logo">Cửa Hàng Tạp Hóa<span></span></a>

      <nav class="navbar">
         <a href="index.php">Trang chủ</a>
         <a href="about.php">Thông tin</a>
         <a href="orders.php">Lịch sử đơn hàng</a>
         <a href="shop.php">Cửa hàng</a>
         <a href="contact.php">Liên hệ</a>
      </nav>

      <div class="icons">
         <?php
            $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
            $total_wishlist_counts = $count_wishlist_items->rowCount();

            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_counts = $count_cart_items->rowCount();
         ?>
         <div id="menu-btn" class="fas fa-bars"></div>
         <a href="search_page.php"><i class="fas fa-search"></i></a>
         <a href="wishlist.php"><i class="fas fa-heart"></i><span>(<?= $total_wishlist_counts; ?>)</span></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_counts; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php          
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               ?>
               <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
               <p><?= $fetch_profile["name"]; ?></p>
               <a href="user_profile_update.php" class="btn">Chỉnh sửa thông tin</a>
               <a href="logout.php" class="delete-btn" onclick="return confirm('Bạn có muốn đăng xuất?');">Đăng xuất</a>  
            <?php
            }else{
         ?>
         <p>Đăng nhập hoặc đăng ký tài khoản của bạn!</p>
         <div class="flex-btn">
            <a href="register.php" class="option-btn">Đăng ký</a>
            <a href="login.php" class="option-btn">Đăng nhập</a>
         </div>
         <?php
            }
         ?>      
         
         
      </div>

   </section>

</header>