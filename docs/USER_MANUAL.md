# BLORIEN Pharma - User Manual

**Version:** 2.6.0
**Last Updated:** November 2025
**Audience:** Pharmacy Staff (Owner, Manager, Cashier)

---

## Table of Contents

1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [Point of Sale (POS)](#point-of-sale-pos)
4. [Product Management](#product-management)
5. [Customer Management](#customer-management)
6. [Dues Management](#dues-management)
7. [Purchase Orders](#purchase-orders)
8. [Reports](#reports)
9. [Daily Closing](#daily-closing)
10. [Common Tasks](#common-tasks)
11. [Troubleshooting](#troubleshooting)

---

## Introduction

BLORIEN Pharma is a complete pharmacy management system designed for Bangladesh pharmacies. It handles:

- **Sales Processing** - Fast POS with receipt printing
- **Inventory** - Track medicine stock with expiry dates
- **Credit Sales** - Customer credit accounts & simple dues
- **Purchase Orders** - Order from suppliers
- **Reports** - Sales, profit, inventory analytics

### Who Uses What

| Role | Can Do |
|------|--------|
| **Owner** | Everything - full system access |
| **Manager** | Products, inventory, reports, purchase orders |
| **Cashier** | POS sales, view products, create dues |

---

## Getting Started

### First Login

1. Open browser: `http://localhost:8000` (or your server address)
2. Click **Login**
3. Enter your email and password
4. You'll see the **Dashboard**

### Dashboard Overview

The dashboard shows:
- Today's sales total
- Number of transactions
- Low stock alerts
- Expiring medicines
- Pending dues
- Quick action buttons

### Language Selection

Switch between English and Bengali:
1. Click language button in top navigation
2. Select **English** or **বাংলা**
3. Entire system switches immediately
4. Your preference is saved

---

## Point of Sale (POS)

The POS is where you make sales. It's designed to be fast and simple.

### Making a Sale

**Step 1: Open POS**
- Click **POS** in sidebar (cart icon)
- You'll see the POS interface

**Step 2: Find Products**
- Type in search box: medicine name, generic name, brand, or SKU
- Example: Type "napa" - finds Napa (Paracetamol)
- Example: Type "para" - also finds Paracetamol
- Click product to add to cart

**Step 3: Adjust Quantity**
- Product added with quantity = 1
- Click **+** or **-** buttons to change
- Or type quantity directly
- Click **×** to remove item

**Step 4: Apply Discount (Optional)**
- Enter discount amount in ৳
- Automatically deducted from total

**Step 5: Select Payment Method**
- **Cash** - Enter amount paid, system calculates change
- **Card** - Credit/debit card payment
- **Mobile Money** - bKash, Nagad, etc.
- **Credit** - For registered customers only
- **Other** - Any other payment method

**Step 6: Complete Sale**
- Click **Complete Sale**
- Receipt prints automatically
- Cart clears - ready for next customer

### Example: Cash Sale

```
Customer wants:
- Napa 500mg (2 strips)
- Sergel 20mg (1 box)

1. Search "napa" → Add to cart
2. Change quantity to 2
3. Search "sergel" → Add to cart
4. Subtotal shows: ৳285
5. Apply ৳10 discount
6. Total: ৳275
7. Select "CASH"
8. Enter paid: ৳500
9. Change shown: ৳225
10. Click "Complete Sale"
11. Receipt prints
```

### Credit Sales

For customers with credit accounts:

1. Select customer from dropdown
2. Add items to cart
3. Select payment method "CREDIT"
4. Click "Complete Sale"
5. Amount added to customer balance
6. No cash exchanged

**Important:** System checks credit limit. If customer is over limit, sale is blocked.

### Stock Alerts

While searching, you'll see:
- **Green badge**: Stock available
- **Orange badge**: Low stock
- **Red badge**: Out of stock

Don't sell out-of-stock items - system won't allow it.

---

## Product Management

*For Managers and Owners only*

### Viewing Products

1. Click **Products** in sidebar
2. See all products in grid view
3. Use search to find specific product

Each card shows:
- Product name
- SKU
- Current stock
- Selling price
- Active/Inactive status

### Adding New Product

1. Click **+ Add Product** button
2. Fill required fields:
   - **Name**: Brand name (e.g., "Napa")
   - **Generic Name**: Chemical name (e.g., "Paracetamol")
   - **SKU**: Unique code (e.g., "NAPA-500")
   - **Barcode**: If available
   - **Purchase Price**: What you pay
   - **Selling Price**: What customers pay
   - **Min Stock**: Alert threshold
3. Optional fields:
   - **Brand Name**
   - **Description**
   - **Supplier**
4. Click **Save**

### Quick Stock Add

Fast way to add stock without purchase order:

1. Find product
2. Click **+ Stock** button
3. Enter:
   - Quantity
   - Batch number
   - Expiry date (auto-filled to +1 year)
   - Purchase price (optional)
4. Click **Add Stock**

Stock added immediately and ready to sell.

### Managing Batches

Each product can have multiple batches with different expiry dates.

**View Batches:**
1. Click product card
2. Click **Batches** tab
3. See all batches with:
   - Batch number
   - Expiry date
   - Quantity received/remaining
   - Status (Active/Expired/Expiring Soon)

**Add Batch:**
1. On product's batch page
2. Fill form:
   - Batch number
   - Expiry date
   - Quantity
   - Purchase price
3. Click **Add Batch**

**FIFO System:** System automatically sells from batches expiring soonest first.

---

## Customer Management

### Creating Customer Account

1. Go to **Customers**
2. Click **+ Add Customer**
3. Fill details:
   - **Name** (required)
   - **Phone** (required, unique)
   - Email
   - Address
   - ID number
4. For credit customers:
   - Enable **Credit Enabled**
   - Set **Credit Limit** (৳)
5. Click **Save**

### Recording Customer Payment

When customer pays their credit:

1. Go to **Customers**
2. Find customer
3. Click **Record Payment**
4. Enter:
   - Amount
   - Payment method
   - Notes (optional)
5. Click **Save**

Balance automatically updated.

### Viewing Customer History

1. Click customer name
2. See:
   - Current balance
   - Available credit
   - All transactions
   - All payments
   - Credit history

---

## Dues Management

Simple way to track dues without creating customer accounts (like a digital notebook).

### Creating a Due

**Method 1: From POS**
When completing sale:
1. Check **Mark as Due**
2. Enter customer name
3. Enter phone (optional)
4. Enter due date (optional)
5. Complete sale
6. Due automatically created

**Method 2: Manual Entry**
1. Go to **Dues** (দেনা-পাওনা)
2. Click **+ Create Due**
3. Fill:
   - Customer Name (required)
   - Customer Phone
   - Amount (৳)
   - Due Date (optional)
   - Notes
4. Click **Save**

### Recording Due Payment

When customer pays:

1. Go to **Dues**
2. Find due (search by name or phone)
3. Click **Pay**
4. Enter:
   - Payment amount
   - Payment method
   - Notes
5. Click **Save**

**Status Updates Automatically:**
- PENDING → amount_paid = 0
- PARTIAL → paid some, not all
- PAID → paid in full

### Phone Lookup

Quick find customers:
1. Type phone number in search
2. See all dues for that phone
3. See payment history

---

## Purchase Orders

*For Managers and Owners*

### Creating Purchase Order

1. Go to **Purchase Orders**
2. Click **+ Create Order**
3. Select **Supplier**
4. Set **Order Date** (today)
5. Set **Expected Delivery** (auto: +7 days)
6. Click **+ Add Item**
7. For each item:
   - Select product
   - Enter quantity
   - Unit price (auto-fills from product)
8. Add shipping/tax if needed
9. Click **Create**

Order saved with status: PENDING

### Receiving Stock

When supplier delivers:

1. Go to **Purchase Orders**
2. Find your order
3. Click **Receive Stock**
4. For each item:
   - Confirm quantity received
   - Enter batch number
   - Enter expiry date (auto: +1 year)
5. Click **Receive**

**What Happens:**
- Status → RECEIVED
- Batches created automatically
- Stock added to products
- Ready to sell immediately

---

## Reports

*For Managers and Owners*

### Sales Report

See all sales for a period:

1. Go to **Reports** → **Sales Report**
2. Select date range (default: this month)
3. Click **Apply Filter**

Shows:
- Total sales amount
- Number of transactions
- Average transaction value
- Total discounts given
- Payment method breakdown
- Sales by date
- Recent transactions list

### Profit Analysis

See profit margins:

1. Go to **Reports** → **Profit Report**
2. Select date range
3. View:
   - Total revenue
   - Total cost
   - Total profit
   - Profit margin %
   - Profit by product (top 20)

### Inventory Report

Current stock status:

1. Go to **Reports** → **Inventory Report**
2. View:
   - Total inventory value
   - Total retail value
   - Potential profit
   - Low stock products
   - Products by value
   - Batch information

### Top Products

Best sellers:

1. Go to **Reports** → **Top Products**
2. Select period:
   - This week
   - This month
   - This year
3. See top 20 by:
   - Quantity sold
   - Revenue generated

### Supplier Performance

1. Go to **Reports** → **Supplier Report**
2. Select date range
3. View per supplier:
   - Total orders
   - Total spent
   - Received orders
   - Pending orders

### Customer Credit Report

1. Go to **Reports** → **Customer Report**
2. View all credit customers:
   - Credit limits
   - Current balances
   - Available credit
   - Credit utilization %
   - Overdue customers
   - Transaction count

---

## Daily Closing

End-of-day summary for reconciliation.

### Generating Daily Summary

1. Go to **Daily Closing**
2. Select date (default: today)
3. View report showing:

**Sales Summary:**
- Cash sales: ৳
- Card sales: ৳
- Mobile money: ৳
- Credit sales: ৳
- Total sales: ৳

**Dues Activity:**
- Dues created: ৳
- Due payments: ৳
  - By Cash: ৳
  - By Card: ৳
  - By Mobile: ৳

**Totals:**
- Total transactions
- Total cash in hand
- Total revenue (sales + due payments)

### Printing Daily Report

1. Click **Print** button
2. Receipt printer prints summary
3. Use for cash reconciliation
4. File for records

---

## Common Tasks

### How to: Give Discount on Sale

1. Add items to cart in POS
2. Enter discount amount in ৳
3. Total updates automatically
4. Complete sale normally

### How to: Process Return

Returns are handled manually:

1. Go to **Transactions**
2. Find original transaction
3. Click **View**
4. Note items and amounts
5. Process refund:
   - Give cash to customer
   - Manual stock adjustment

*Future feature: automated returns coming soon*

### How to: Check Stock Level

**Quick Check:**
- Dashboard shows low stock count
- Click to see list

**Detailed Check:**
1. Go to **Products**
2. Each card shows current stock
3. Red = low stock
4. Search for specific product

**By Batch:**
1. Go to product
2. Click **Batches**
3. See each batch with remaining qty

### How to: Find Expiring Medicine

1. Go to **Alerts** (bell icon)
2. View **Expiring Soon** tab (default: 30 days)
3. See:
   - Product name
   - Batch number
   - Expiry date
   - Quantity remaining
   - Days until expiry

### How to: Search by Phone

In Dues:
1. Go to **Dues**
2. Type phone number
3. See all dues for that number

In Customers:
1. Go to **Customers**
2. Search by phone
3. Click to view details

### How to: Change Password

1. Click profile icon (top right)
2. Select **Settings**
3. Enter current password
4. Enter new password
5. Confirm new password
6. Click **Update**

---

## Troubleshooting

### "Insufficient Stock" Error

**Problem:** Can't complete sale
**Cause:** Not enough stock in any batch
**Solution:**
1. Check product stock in **Products**
2. Add stock via **Quick Stock** or **Purchase Order**
3. Try sale again

### "Credit Limit Exceeded" Error

**Problem:** Can't sell on credit to customer
**Cause:** Customer balance > credit limit
**Solution:**
1. Collect payment from customer first
2. Record payment in **Customers**
3. Try sale again
OR
4. Increase credit limit (Owner only)

### "Batch Not Found" Error

**Problem:** Product shows stock but can't sell
**Cause:** No active batch
**Solution:**
1. Go to product's **Batches** page
2. Check if all batches expired
3. Add new batch with valid expiry

### Can't Find Product in POS

**Problem:** Search returns no results
**Cause:** Product might be inactive or deleted
**Solution:**
1. Go to **Products**
2. Check if product exists
3. Check **Active** status
4. If inactive, activate it

### Receipt Not Printing

**Problem:** Receipt doesn't print after sale
**Cause:** Printer not configured or offline
**Solution:**
1. Check printer is on
2. Check printer connected
3. Check browser print settings
4. Try print again from **Transactions** list

### Wrong Language Showing

**Problem:** System in wrong language
**Cause:** Language setting changed
**Solution:**
1. Click language toggle (top right)
2. Select preferred language
3. System switches immediately

---

## Tips for Efficiency

### 1. Use Keyboard Shortcuts
- **Tab**: Move to next field
- **Enter**: Add product to cart (when search result highlighted)
- **Ctrl+F**: Quick search

### 2. Memorize Common SKUs
- Search by SKU is instant
- Create simple SKU patterns (NAPA-500, PARA-100)

### 3. Set Realistic Min Stock
- Don't set too high (unnecessary alerts)
- Don't set too low (risk stockout)
- Review and adjust monthly

### 4. Use Quick Stock Add
- For small quantities
- When urgent (customer waiting)
- For one-time suppliers

### 5. Regular Batch Check
- Weekly review **Expiring Soon**
- Mark down prices for soon-expiring items
- Return to supplier if possible

### 6. Daily Closing Routine
- End of each day, generate report
- Count actual cash
- Reconcile differences
- File report

---

## Best Practices

### Stock Management
- ✅ Add stock as soon as received
- ✅ Always enter correct expiry dates
- ✅ Use batch numbers consistently
- ✅ Check expiry alerts weekly
- ✅ FIFO system sells oldest first automatically

### Sales Processing
- ✅ Verify customer identity for credit sales
- ✅ Count cash carefully
- ✅ Check product expiry before selling
- ✅ Print and give receipt to customer
- ✅ Process returns promptly

### Customer Management
- ✅ Update phone numbers when they change
- ✅ Record all payments immediately
- ✅ Follow up on overdue accounts
- ✅ Review credit limits quarterly
- ✅ Keep customer information accurate

### Data Integrity
- ✅ Don't delete transactions
- ✅ Use adjustments for corrections
- ✅ Keep notes for unusual entries
- ✅ Regular backups (Owner responsibility)
- ✅ Log out when leaving computer

---

## Quick Reference

### Payment Methods

| Code | Method | Usage |
|------|---------|-------|
| CASH | Cash | Most common, calculate change |
| CARD | Card | Credit/debit card |
| MOBILE | Mobile Money | bKash, Nagad, Rocket |
| CREDIT | Credit Account | Registered customers only |
| OTHER | Other | Any other method |

### Due Status Codes

| Status | Meaning |
|--------|---------|
| PENDING | Not paid at all |
| PARTIAL | Paid some, not all |
| PAID | Paid in full |

### Purchase Order Status

| Status | Meaning |
|--------|---------|
| PENDING | Created, not ordered yet |
| ORDERED | Sent to supplier |
| RECEIVED | Stock received and added |
| CANCELLED | Order cancelled |

### Batch Status

| Status | Meaning |
|--------|---------|
| Active | Selling normally |
| Expiring Soon | Within 30 days of expiry |
| Expired | Past expiry date |
| Depleted | Sold out |

---

## Need Help?

### For Technical Issues
- Check this manual first
- Contact system administrator
- Check [Developer Guide](DEVELOPER_GUIDE.md)

### For Business Processes
- Consult pharmacy owner/manager
- Review [Features Guide](FEATURES.md)
- Check [Product Overview](PRODUCT_OVERVIEW.md)

---

**Document Version:** 1.0
**System Version:** 2.6.0
**Last Updated:** November 2025
**Language:** English

---

*End of User Manual*
