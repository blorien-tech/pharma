# BLORIEN Pharma - User Guide

A comprehensive guide for pharmacy staff on using the BLORIEN Pharma system.

## Table of Contents

1. [Getting Started](#getting-started)
2. [Dashboard](#dashboard)
3. [Point of Sale (POS)](#point-of-sale-pos)
4. [Product Management](#product-management)
5. [Batch Management](#batch-management)
6. [Transaction History](#transaction-history)
7. [User Management](#user-management)
8. [Supplier Management](#supplier-management)
9. [Purchase Orders](#purchase-orders)
10. [Customer Management](#customer-management)
11. [Reports](#reports)
12. [Analytics Dashboard](#analytics-dashboard)
13. [Alerts](#alerts)
14. [Common Tasks](#common-tasks)

---

## Getting Started

### First Time Login

1. Open your browser and navigate to the system URL
2. If this is the first time, you'll be redirected to `/setup`
3. Enter the owner account details:
   - Full Name
   - Email Address
   - Password (minimum 8 characters)
4. Click "Complete Setup"

### Regular Login

1. Go to the login page
2. Enter your email and password
3. Click "Sign in"
4. You'll be redirected to the dashboard

### User Roles

- **Owner**: Full system access
- **Manager**: Can manage products, users, view reports
- **Cashier**: Can process sales only

---

## Dashboard

The dashboard is your home screen showing:

### Statistics Cards
- **Today's Sales**: Total sales amount and transaction count
- **Total Products**: Number of active products
- **Low Stock Alert**: Products needing restock
- **Expiring Soon**: Batches expiring within 30 days

### Recent Transactions
- Last 5 transactions
- Quick view of transaction type, amount, and date

### Quick Actions
- **New Sale**: Opens POS interface
- **Add Product**: Create a new product
- **View Reports**: Access transaction history

---

## Point of Sale (POS)

### Processing a Sale

1. **Navigate to POS**
   - Click "POS" in the navigation menu
   - Or click "New Sale" on the dashboard

2. **Search for Products**
   - Type product name or SKU in the search box
   - Products will appear as you type
   - Click on a product to add it to cart

3. **Manage Cart Items**
   - **Adjust Quantity**: Use +/- buttons or type directly
   - **Remove Item**: Click the X button
   - **View Total**: Cart summary shows subtotal and total

4. **Apply Discount** (Optional)
   - Enter discount amount in the Discount field
   - Total will update automatically

5. **Select Payment Method**
   - Cash
   - Card
   - Mobile Money
   - Other

6. **For Cash Payments**
   - Enter amount paid
   - System calculates change automatically

7. **Complete Sale**
   - Click "Complete Sale"
   - Receipt will open in a new tab
   - Cart will clear automatically

### Printing Receipts

- Receipt opens automatically after sale
- Click "Print Receipt" button
- Select your thermal printer
- Receipt includes:
  - Pharmacy details
  - Transaction ID and date
  - Item list with prices
  - Total, payment method, change

---

## Product Management

### Adding a New Product

1. Navigate to **Products** → **Add Product**
2. Fill in the form:
   - **Product Name**: Full product name
   - **SKU**: Unique identifier (e.g., "PARA-500")
   - **Description**: Optional details
   - **Purchase Price**: Cost price
   - **Selling Price**: Retail price
   - **Current Stock**: Initial quantity
   - **Minimum Stock**: Alert threshold
   - **Active**: Check to make available for sale
3. Click "Create Product"

### Editing a Product

1. Go to **Products**
2. Find the product (use search if needed)
3. Click "Edit"
4. Update the information
5. Click "Update Product"

### Viewing Products

Products are displayed in cards showing:
- Product name and SKU
- Purchase and selling prices
- Current stock (red if low)
- Active/Inactive status

### Deleting a Product

1. Find the product
2. Click "Delete"
3. Confirm the action
4. Product will be soft-deleted

---

## Batch Management

### Why Batch Tracking?

- Track expiry dates
- Implement FIFO (First In, First Out)
- Prevent selling expired products
- Monitor which batches are moving

### Adding a Batch

1. Go to **Products**
2. Click "Batches" for the product
3. Fill in the batch form:
   - **Batch Number**: Manufacturer's batch number
   - **Expiry Date**: Must be future date
   - **Quantity**: Number of units received
   - **Purchase Price**: Optional (uses product price if empty)
4. Click "Add Batch"

### Viewing Batches

Batch table shows:
- Batch number
- Expiry date (color-coded)
- Quantity received and remaining
- Status (Active, Expiring Soon, Expired, Depleted)

### Status Colors

- **Green** (Active): Good condition
- **Yellow** (Expiring Soon): Within 30 days of expiry
- **Red** (Expired): Past expiry date
- **Gray** (Depleted): No stock remaining

---

## Transaction History

### Viewing Transactions

1. Navigate to **Transactions**
2. View list of all transactions
3. Use filters:
   - **Type**: Sales or Returns
   - **Date**: Specific date
4. Click "Filter" to apply

### Transaction Details

Click "View" on any transaction to see:
- Receipt number and date
- All items purchased
- Batch numbers (for traceability)
- Payment details
- Subtotal, discounts, total

### Processing Returns

1. Find the original sale transaction
2. Click "Return"
3. Confirm the return
4. System will:
   - Create a return transaction
   - Refund the amount
   - Restore inventory
   - Link return to original sale

---

## User Management

*Available for Owners and Managers only*

### Creating a User

1. Go to **Users** → **Create User**
2. Enter details:
   - Full Name
   - Email (unique)
   - Password (minimum 8 characters)
   - Confirm Password
   - Role (Owner/Manager/Cashier)
   - Active status
3. Click "Create User"

### User Roles Explained

**Owner:**
- Full system access
- Can manage users
- Can view all reports
- Can modify system settings

**Manager:**
- Can manage products and inventory
- Can view reports
- Can manage users
- Cannot access system settings

**Cashier:**
- Can process sales
- Can view POS and products
- Cannot manage inventory
- Cannot access reports or users

### Deactivating a User

Users cannot be deleted, but can be deactivated:
1. Edit the user
2. Uncheck "Active"
3. Update user
4. User cannot log in anymore

---

## Supplier Management

*Available for Owners and Managers only*

### Adding a Supplier

1. Navigate to **Suppliers** → **Add Supplier**
2. Fill in supplier information:
   - **Supplier Name**: Contact person
   - **Company Name**: Business name
   - **Email**: Contact email
   - **Phone**: Contact number
   - **Address**: Full address
   - **Tax ID**: Optional tax identification
   - **Active**: Check to enable
3. Click "Create Supplier"

### Viewing Suppliers

Supplier list shows:
- Contact and company name
- Phone and email
- Total products supplied
- Status (Active/Inactive)

### Editing Suppliers

1. Find the supplier
2. Click "Edit"
3. Update information
4. Click "Update Supplier"

---

## Purchase Orders

*Available for Owners and Managers only*

### Creating a Purchase Order

1. Navigate to **Purchase Orders** → **Create PO**
2. Select **Supplier** from dropdown
3. Choose **Order Date**
4. Set **Expected Delivery** (optional)
5. **Add Items**:
   - Click "+ Add Item"
   - Select Product
   - Enter Quantity
   - Unit Price (auto-fills from product)
   - View Subtotal
6. Add **Shipping Cost** and **Tax** if applicable
7. Review Total
8. Add **Notes** (optional)
9. Click "Create Purchase Order"

### Receiving Stock

1. Find the Purchase Order
2. Click "Receive"
3. For each item:
   - Verify **Quantity Ordered**
   - Enter **Received Quantity** (may differ)
   - Enter **Batch Number** from packaging
   - Enter **Expiry Date**
4. Set **Received Date**
5. Click "Confirm Receipt & Update Inventory"

**What Happens:**
- Inventory automatically updates
- Product batches are created
- Purchase order status changes to RECEIVED
- Product purchase price updates if different

### Managing Purchase Orders

**Statuses:**
- **PENDING**: Newly created, not yet ordered
- **ORDERED**: Sent to supplier
- **RECEIVED**: Stock received and added to inventory
- **CANCELLED**: Order cancelled

**Actions:**
- **View**: See PO details
- **Receive**: Record stock receipt
- **Cancel**: Cancel pending/ordered PO

---

## Customer Management

### Adding a Customer

1. Navigate to **Customers** → **Add Customer**
2. Fill in basic information:
   - **Full Name** *
   - **Phone Number** * (unique)
   - **Email** (optional)
   - **Address**
   - **City**
   - **ID Number** (national ID, etc.)
3. **Credit Settings**:
   - Check "Enable Credit for this Customer"
   - Set **Credit Limit** (৳)
4. Add **Notes** if needed
5. Check "Active Customer"
6. Click "Create Customer"

### Processing Credit Sales

1. Go to **POS**
2. Add products to cart
3. Select **Customer** from dropdown
4. If credit enabled, check "Use Credit"
5. Available credit is shown
6. Click "Complete Sale"
7. System validates credit availability
8. Credit balance updates automatically

### Recording Customer Payments

1. Go to **Customers**
2. Find customer with balance
3. Click "Payment"
4. Enter **Payment Amount**
5. Select **Payment Method** (Cash/Card/Mobile/Other)
6. Add **Notes** (optional)
7. Click "Record Payment"

**Result:**
- Customer balance reduces
- Credit transaction recorded
- Available credit increases

### Balance Adjustments

For corrections or special circumstances:

1. Go to Customer profile
2. Click "Adjust Balance"
3. Enter **Adjustment Amount**:
   - Positive: Increases balance (customer owes more)
   - Negative: Decreases balance (forgive debt)
4. Provide **Reason** (required)
5. Click "Apply Adjustment"

**Note:** All adjustments are audited

### Viewing Customer Details

Customer profile shows:
- Credit limit and current balance
- Available credit
- Total transactions
- Credit transaction history
- Recent purchases
- Overdue status (if balance > limit)

---

## Reports

Navigate to **Reports** to access 6 comprehensive reports:

### 1. Sales Report

- Date range filtering
- Total sales and transaction count
- Average transaction value
- Sales by date breakdown
- Payment method distribution
- Transaction details table

### 2. Profit Analysis

- Date range filtering
- Total revenue, cost, and profit
- Profit margin percentage
- Profit by product (top 20)
- Margin calculations per product

### 3. Inventory Report

- Current inventory valuation (cost)
- Potential retail value
- Potential profit
- Low stock product list
- Products by value (top 20)

### 4. Top Selling Products

- Period selection (week/month/year)
- Top 20 products by quantity sold
- Revenue per product
- Average selling price

### 5. Supplier Performance

- Date range filtering
- Total spent and orders
- Orders by supplier
- Received vs pending orders
- Spending breakdown

### 6. Customer Credit Report

- Total credit limit across all customers
- Outstanding balance
- Available credit
- Overdue customers count
- Credit utilization by customer
- Balance status for each customer

---

## Analytics Dashboard

Navigate to **Analytics** for visual business insights:

### Interactive Charts

**Sales Trend Chart:**
- 30-day sales trend line
- Dual axis (sales amount + transaction count)
- Period selector (7/30/90 days)
- Real-time updates

**Payment Method Distribution:**
- Doughnut chart showing revenue by payment type
- Current month data
- Percentages and amounts

**Inventory Status:**
- Pie chart of stock levels
- Low Stock, Adequate Stock, Out of Stock
- Product count per category

**Top Products Bar Chart:**
- Top 10 products by revenue
- Current month data
- Visual comparison

**Credit Utilization:**
- Doughnut chart of used vs available credit
- Total amounts displayed
- Real-time customer credit status

### Monthly Comparison Cards

- This month sales
- Last month sales
- Growth percentage (color-coded)

**Features:**
- All charts are interactive
- Hover for detailed tooltips
- Responsive design
- Auto-refresh data

---

## Alerts

### Accessing Alerts

Click "Alerts" in the navigation to see:

### Low Stock Products

Products where current stock ≤ minimum stock:
- Product name and SKU
- Current vs minimum stock
- Quick link to restock

### Expiring Soon Batches

Batches expiring within 30 days:
- Product and batch number
- Expiry date
- Days remaining
- Quantity to clear

### Expired Batches

Batches past expiry date:
- Must be removed from inventory
- Cannot be sold
- Needs disposal

---

## Common Tasks

### Daily Opening Procedure

1. Log in to the system
2. Check dashboard for alerts
3. Review low stock items
4. Check expiring batches
5. Prepare POS for the day

### Processing a Quick Sale

1. Go to POS
2. Search product by name or SKU
3. Click to add to cart
4. Adjust quantity if needed
5. Select payment method
6. Complete sale
7. Print receipt

### Restocking Products (Method 1: Direct Batch)

1. Receive new stock
2. Go to product's batch page
3. Add new batch with:
   - Batch number from supplier
   - Expiry date from packaging
   - Quantity received
4. System updates stock automatically

### Restocking Products (Method 2: Purchase Order)

1. Create Purchase Order for supplier
2. Add products and quantities needed
3. Submit order
4. When stock arrives, click "Receive"
5. Enter batch details for each item
6. System creates batches and updates stock automatically

### End of Day

1. Check today's sales on dashboard
2. Review transaction history
3. Note low stock items for next day
4. Log out

### Handling Returns

1. Customer brings item with receipt
2. Find transaction by receipt number
3. Verify items and condition
4. Click "Return" button
5. Process refund
6. Stock is restored automatically

### Monthly Inventory Check

1. Go to Alerts page
2. Review expired batches
3. Remove expired items
4. Check low stock items
5. Plan restocking
6. Review sales patterns
7. Check Reports for insights
8. Review Analytics Dashboard

### Processing a Credit Sale

1. Go to POS
2. Select customer from dropdown
3. Add products to cart
4. Check "Use Credit" option
5. Verify available credit
6. Complete sale
7. Credit balance updates automatically

### Collecting Customer Payment

1. Navigate to Customers
2. Find customer with outstanding balance
3. Click "Payment"
4. Enter amount received
5. Select payment method
6. Record payment
7. Receipt generated automatically

### Checking Business Performance

1. Go to Analytics Dashboard
2. Review sales trends
3. Check top products
4. Monitor inventory status
5. Review payment method distribution
6. Check credit utilization

### Generating Reports

1. Navigate to Reports
2. Select report type needed
3. Set date range (if applicable)
4. Click Filter/Apply
5. Review data
6. Export or print if needed

---

## Tips and Best Practices

### POS Tips

- Use product search to find items quickly
- Always verify quantity before completing sale
- Check cart total before payment
- Print receipt for every transaction

### Inventory Management

- Add batches immediately when stock arrives
- Always check expiry dates
- Use FIFO - sell older stock first
- Regular stock counts

### Customer Service

- Print clear, readable receipts
- Explain batch numbers if asked
- Process returns promptly
- Keep transaction history for reference

### Security

- Log out when leaving the computer
- Don't share your password
- Report suspicious activity
- Keep receipts secure

### Credit Management

- Always verify customer credit limit before sale
- Monitor overdue customers regularly
- Record payments promptly
- Document balance adjustments with clear reasons
- Review credit utilization weekly

### Purchase Management

- Create POs for all supplier orders
- Verify received quantities match orders
- Enter accurate batch numbers and expiry dates
- Review supplier performance monthly
- Track pending orders regularly

### Analytics & Reporting

- Check Analytics Dashboard weekly
- Review sales trends for planning
- Use reports for business decisions
- Monitor top products for stocking
- Track profit margins by product

---

## Keyboard Shortcuts

- **Search in POS**: Focus search box (auto-focused on load)
- **Escape**: Clear search results
- **Enter**: Add selected product to cart

---

## Troubleshooting

### Cannot Find Product in Search

- Check spelling
- Try searching by SKU
- Product might be inactive
- Product might not exist

### Sale Won't Complete

- Check if stock is available
- Verify payment amount (for cash)
- Check internet connection
- Try again or contact support

### Receipt Won't Print

- Check printer is on
- Check printer connection
- Try "Print" from browser
- Save PDF as backup

### Forgot Password

- Contact system administrator (Owner)
- Owner can create new user account
- Use temporary password
- Change password after login

---

## Getting Help

If you encounter issues:

1. **Check this guide** for solutions
2. **Ask your manager** for assistance
3. **Contact system administrator** for technical issues
4. **Document the problem** with screenshots if possible

---

## Appendix

### Common Terms

- **SKU**: Stock Keeping Unit (product code)
- **Batch**: Group of products with same expiry date
- **FIFO**: First In, First Out (inventory method)
- **POS**: Point of Sale (checkout system)
- **Transaction**: Completed sale or return

### Product Categories

Products should be organized by:
- Medicine type (tablets, syrup, injection)
- Purpose (pain relief, antibiotics, etc.)
- Brand or generic
- Storage requirements

### Report Types

- Daily sales report
- Monthly sales summary
- Low stock report
- Expiry report
- User activity log

---

*For technical issues or system errors, contact your system administrator.*

**BLORIEN Pharma System v2.0 - Phase 2 Complete**

### What's New in Version 2.0

- Supplier Management
- Purchase Order System
- Customer Credit Management
- Advanced Reporting (6 reports)
- Analytics Dashboard with Charts
- Credit Sales through POS
- Payment Recording
- Balance Adjustments
- Supplier Performance Tracking
