digikalamvc/
│
├── .htaccess
├── digi_mvc.sql
├── PROJECT_STRUCTURE_TREE.md
│
├── index.php
├── controllers/
│       ├── Account.php
│       ├── AddComment.php
│       ├── AdminCategory.php
│       ├── AdminComment.php
│       ├── AdminDashboard.php
│       ├── AdminLogin.php
│       ├── AdminNews.php
│       ├── AdminOrder.php
│       ├── AdminProduct.php
│       ├── AdminQuestion.php
│       ├── AdminSetting.php
│       ├── AdminSlider.php
│       ├── AdminStat.php
│       ├── AdminUser.php
│       ├── Cart.php
│       ├── Checkout.php
│       ├── Collection.php
│       ├── Index.php
│       ├── Login.php
│       ├── Order.php
│       ├── Product.php
│       ├── Register.php
│       └── Search.php
├── core/
│     ├── app.php
│     ├── config.php
│     ├── controller.php
│     ├── env.example.php
│     ├── model.php
│     └── payment.php
│     
├── models/
│      ├── ModelAccount.php
│      ├── ModelAddComment.php
│      ├── ModelAdminCategory.php
│      ├── ModelAdminComment.php
│      ├── ModelAdminDashboard.php
│      ├── ModelAdminLogin.php
│      ├── ModelAdminNews.php
│      ├── ModelAdminOrder.php
│      ├── ModelAdminProduct.php
│      ├── ModelAdminSetting.php
│      ├── ModelAdminSlider.php
│      ├── ModelAdminStat.php
│      ├── ModelAdminUser.php
│      ├── ModelCart.php
│      ├── ModelCheckout.php
│      ├── ModelCollection.php
│      ├── ModelIndex.php
│      ├── ModelLogin.php
│      ├── ModelOrder.php
│      ├── ModelProduct.php
│      ├── ModelRegister.php
│      └── ModelSearch.php
├── public/
│     └── assets/
│          ├── css/
│          │   ├── main.css
│          │   └── main.css.map
│          └── js/
│              ├── account.js
│              ├── admin_category.js
│              ├── admin_comment.js
│              ├── admin_dashboard.js
│              ├── admin_layout.js
│              ├── admin_login.js
│              ├── admin_news.js
│              ├── admin_order.js
│              ├── admin_product.js
│              ├── admin_question.js
│              ├── admin_setting.js
│              ├── admin_slider.js
│              ├── admin_statistics.js
│              ├── admin_user.js
│              ├── admin.js
│              ├── cart.js
│              ├── collection.js
│              ├── comment.js
│              ├── global.js
│              ├── header.js
│              ├── home.js
│              ├── login.js
│              ├── order.js
│              ├── payment.js
│              ├── product.js
│              ├── register.js
│              └── search.js
│          
├── scss/
│    ├── _account.scss
│    ├── _admin_category.scss
│    ├── _admin_comment.scss
│    ├── _admin_dashboard.scss
│    ├── _admin_layout.scss
│    ├── _admin_login.scss
│    ├── _admin_order.scss
│    ├── _admin_product.scss
│    ├── _admin_setting.scss
│    ├── _admin_slider.scss
│    ├── _admin_statistics.scss
│    ├── _admin_user.scss
│    ├── _admin.scss
│    ├── _base.scss
│    ├── _cart.scss
│    ├── _collection.scss
│    ├── _comment.scss
│    ├── _components.scss
│    ├── _facture.scss
│    ├── _footer.scss
│    ├── _header.scss
│    ├── _home.scss
│    ├── _layout.scss
│    ├── _login.scss
│    ├── _order.scss
│    ├── _payment.scss
│    ├── _product.scss
│    ├── _register.scss
│    ├── _search.scss
│    ├── _variables.scss
│    └── main.scss
│
│
└── views/
    ├── account/
    │    ├── favorites.php
    │    └── account.php
    │
    ├── admin/
    │    ├── layout.php
    │    ├── footer.php
    │    ├── admin_category/
    │    │     ├── add_attr.php
    │    │     ├── add_category.php
    │    │     ├── attr_val.php
    │    │     ├── category.php
    │    │     └── show_attr.php
    │    ├── admin_comment/
    │    │     └── comment.php
    │    ├── admin_dashboard/
    │    │     └── dashboard.php
    │    ├── admin_login/
    │    │     └── login.php
    │    ├── admin_news/
    │    │     ├── add.php
    │    │     ├── edit.php
    │    │     └── news.php
    │    ├── admin_order/
    │    │     ├── detail.php
    │    │     ├── factor.php
    │    │     └── orders.php
    │    ├── admin_product/
    │    │     ├── add_product.php
    │    │     ├── add_review.php
    │    │     ├── attributes.php
    │    │     ├── gallery.php
    │    │     ├── reviews.php
    │    │     └── products.php
    │    ├── admin_question/
    │    │     └── question.php
    │    ├── admin_setting/
    │    │     └── settings.php
    │    ├── admin_slider/
    │    │     └── slider.php
    │    ├── admin_statistics/
    │    │     ├── reports.php
    │    │     └── results.php
    │    └── admin_user/
    │         └── users.php
    ├── cart/
    │     └── cart.php
    ├── checkout/
    │       ├── bank_transfer.php
    │       ├── checkout.php
    │       └── error.php
    ├── collection/
    │       └── collection.php
    ├── comment/
    │       └── add_comment.php
    ├── index/
    │      └── index.php
    ├── login/
    │       └── login.php
    ├── order/
    │       ├── step1_login.php
    │       ├── step2_address.php
    │       ├── step3_summary.php
    │       └── step4_payment.php
    ├── product/
    │       ├── exclusives.php
    │       ├── product.php
    │       └── tabs.php
    ├── register/
    │       └── register.php    
    ├── search/
    │       └── search.php
    ├── header.php
    └── footer.php