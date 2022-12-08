<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = $_POST['flat'] .' '. $_POST['street'] .' '. $_POST['city'] .' '. $_POST['state'] .' '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products = [];

   $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $cart_query->execute([$user_id]);
   if($cart_query->rowCount() > 0){
      while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
         $cart_products[] = $cart_item['name'].' ( '.$cart_item['quantity'].' )';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      };
   };

   $total_products = implode(', ', $cart_products);

   $order_query = $conn->prepare("SELECT * FROM `orders` WHERE name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ?");
   $order_query->execute([$name, $number, $email, $method, $address, $total_products, $cart_total]);

   if($cart_total == 0){
      $message[] = 'Giỏ hàng trống';
   }elseif($order_query->rowCount() > 0){
      $message[] = 'Đơn hàng đã được đặt';
   }else{
      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);
      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);
      $message[] = 'Đặt hàng thành công';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thanh toán</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="display-orders">

   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart_items->execute([$user_id]);
      if($select_cart_items->rowCount() > 0){
         while($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)){
            $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <p> <?= $fetch_cart_items['name']; ?> <span>(<?= $fetch_cart_items['price'].' VND'.' x '. $fetch_cart_items['quantity']; ?>)</span> </p>
   <?php
    }
   }else{
      echo '<p class="empty">Giỏ hàng trống</p>';
   }
   ?>
   <div class="grand-total">Tổng cộng : <span><?= $cart_grand_total; ?> VND</span></div>
</section>

<section class="checkout-orders">

   <form action="" method="POST">

      <h3>Đặt Hàng</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Tên:</span>
            <input type="text" name="name" placeholder="Nhập tên của bạn" class="box" required>
         </div>
         <div class="inputBox">
            <span>Số điện thoại:</span>
            <input type="number" name="number" placeholder="Nhập số điện thoại của bạn" class="box" required>
         </div>
         <div class="inputBox">
            <span>Email:</span>
            <input type="email" name="email" placeholder="Nhập email của bạn" class="box" required>
         </div>
         <div class="inputBox">
            <span>Phương thức thanh toán :</span>
            <select name="method" class="box" required>
               <option value="Thanh toán khi nhận hàng">Thanh toán khi nhận hàng</option>
               <option value="Thẻ tín dụng">Thẻ tín dụng</option>
               <option value="Paypal">Paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Dịa chỉ 1:</span>
            <input type="text" name="flat" placeholder="Ví dụ: số nhà" class="box" required>
         </div>
         <div class="inputBox">
            <span>Địa chỉ 2:</span>
            <input type="text" name="street" placeholder="Ví dụ: tên đường" class="box" required>
         </div>
         <div class="inputBox">
            <span>Thành phố:</span>
            <input type="text" name="city" placeholder="Ví dụ: Hà Nội" class="box" required>
         </div>
         <div class="inputBox">
            <span>Quận:</span>
            <input type="text" name="state" placeholder="Ví dụ: Quận 1,2,3,4,..." class="box" required>
         </div>
         <div class="inputBox">
            <span>Quốc gia:</span>
            <input type="text" name="country" placeholder="Ví dụ: Việt Nam" class="box" required>
         </div>
         <div class="inputBox">
            <span>Mã bưu chính:</span>
            <input type="number" min="0" name="pin_code" placeholder="Ví dụ: 123456" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($cart_grand_total > 1)?'':'disabled'; ?>" value="Đặt hàng">

   </form>

</section>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>