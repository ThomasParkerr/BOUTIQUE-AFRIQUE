# African Clothing Store Website - AfriqueBotique

## Overview  
The goal of this project is to create a fully functional e-commerce website that allows users to browse, purchase, and manage African clothing items. The website offers features such as adding items to a cart, applying discounts, managing loyalty points, user profile updates, password recovery, and administrative insights.

---

## **Key Features and Functionalities**  

### **User-Centric Features**
- **Add to Cart:**  
  Items can be added to a cart table, which dynamically updates and populates based on the user.  
- **Wishlist Management:**  
  Users can add items to a wishlist table and seamlessly transfer items from the wishlist to the cart.  
- **Dynamic Checkout Process:**  
  - Checkout validation ensures proper payment type selection.  
  - Items marked as "checked out" are removed from the cart but remain tracked in the purchases table.  
  - Discounts are calculated in real-time and displayed on the checkout page.  
- **Loyalty Points System:**  
  Loyalty points are updated based on completed checkouts, providing users with rewards for their purchases.  
- **Profile Management:**  
  Users can update their profiles, including updating passwords and payment options.  
- **Password Recovery:**  
  A secure password reset feature allows users to recover access if they forget their passwords.  

### **Browsing and Filtering**
- **Filter by Gender and Clothing Type:**  
  Users can filter items by gender or clothing type using attributes (item type) and (product type) in the database linked to PHP queries.  
- **Discount Banner:**  
  Displays discounts as a percentage, dynamically sourced from the sales table.

### **Admin Dashboard**
- The admin page displays aggregated stats, such as:  
  - Average loyalty points per user.  
  - Total purchases made
  - Total products on sale
  - Total number of products being sold

---

## **Challenges Encountered**
1. **Adding to Cart:**  
   Ensuring seamless transitions between cart and wishlist while maintaining data consistency.  
2. **Checkout Process:**  
   - Validating payment type.  
   - Preventing items already checked out from reappearing in the cart.  
3. **Dynamic Stock Updates:**  
   Adjusting stock levels and dynamically updating prices based on quantity during checkout.  
4. **Discounts:**  
   Calculating and displaying discounts dynamically while ensuring accurate price updates.  
5. **User Features:**  
   - Secure implementation of password recovery.  
   - Proper linkage between wishlist and cart functionalities.  
6. **Filtering and Searching:**  
   Optimizing SQL queries for filtering by gender and clothing type.  

---

## **How the Website Achieved Its Functionalities**

1. **Add to Cart and Wishlist:**
   - Items added to the cart or wishlist are inserted into respective tables.  
   - Cart functionality automatically populates items from the cart table for the specific user.  
   - Wishlist functionality allows transferring items directly to the cart using the "Add to Cart" button.  

2. **Dynamic Checkout Process:**
   - Stock levels are updated upon checkout, ensuring real-time inventory tracking.  
   - Discounts are dynamically applied, with calculations from the products table for reduced prices.  
   - Checkout form uses SQL to mark items as "checked out," ensuring they no longer appear in the cart.  

3. **Filtering by Gender and Clothing Type:**
   - Gender and clothing type attributes are added to the products table.  
   - PHP queries dynamically filter items based on selected attributes.  

4. **Profile and Password Management:**
   - Users can securely update their profile information, including payment options and passwords.  
   - Password recovery uses a secure form to reset passwords upon verification.  

5. **Discount Display and Banner:**
   - Discounts are calculated based on attributes in the sales table.  
   - The discount percentage is displayed prominently on the discount banner on the item.

6. **Admin Page:**
   - Queries fetch data from multiple tables to provide an overview of user activity and store performance.

---

## **Technical Details**
- **Backend:** PHP with MySQL database integration.  
- **Frontend:** HTML, CSS, and JavaScript for a user-friendly interface.  
- **Database Tables:**
  - `AfriqueBotique_Cart`: Stores items in the user's cart and Tracks items checked out by the user.
  - `AfriqueBotique_Wishlist`: Manages user wishlist items.
  - `AfriqueBotique_Products`: Holds product details, including stock, gender, clothing type, and discounts.
  - `AfriqueBotique_Sales`: Manages discount percentages for dynamic price calculations.
  - `AfriqueBotique_Users`: Manages user profiles and credentials.
  - `AfriqueBotique_payments`: Stores payment details of users and uses them for checkout verification.
  - `afriquebotique_loyaltypoints`: Records loyalty points users accumulated and the tier they have reached
  - `AfriqueBotique_Admin`: Database to store admin details. Used to gain access to super Admin page


---

This README provides an overview of the project, its functionalities, and how challenges were addressed. For further details, refer to the project source code and live application links.  
