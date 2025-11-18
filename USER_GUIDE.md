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
8. [Alerts](#alerts)
9. [Common Tasks](#common-tasks)

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

### Restocking Products

1. Receive new stock
2. Go to product's batch page
3. Add new batch with:
   - Batch number from supplier
   - Expiry date from packaging
   - Quantity received
4. System updates stock automatically

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

**BLORIEN Pharma System v1.0**
