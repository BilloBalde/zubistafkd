# FBK-Printing Invoice System - Technical Analysis

## Overview
The Laravel application has a complete invoice/facture management system with two primary workflows:
1. **Traditional Sales** - Direct invoice creation for in-store/manual transactions
2. **E-commerce Orders** - Automatic invoice generation from online orders with manager approval

---

## 1. DATABASE STRUCTURE

### Factures Table (Invoices)
**Migration:** `2024_08_09_071551_create_factures_table.php`

```sql
CREATE TABLE factures (
  id BIGINT PRIMARY KEY
  numero_facture VARCHAR UNIQUE
  customer_id BIGINT FOREIGN KEY (customers.id)
  store_id BIGINT FOREIGN KEY (stores.id)
  quantity INT NULLABLE
  montant_total DECIMAL(15,2) NULLABLE
  avance DECIMAL(15,2) -- Down payment/advance
  reste DECIMAL(15,2) NULLABLE -- Remaining balance
  statut ENUM('non payé', 'partiel', 'payé') DEFAULT 'non payé'
  livraison ENUM('non livré', 'livré') DEFAULT 'non livré'
  notes TEXT NULLABLE
  created_at, updated_at TIMESTAMP
)
```

**Status Fields:**
- `statut` (Payment Status):
  - `'payé'` - Fully paid
  - `'partiel'` - Partially paid (advance made but balance remaining)
  - `'non payé'` - Unpaid
  
- `livraison` (Delivery Status):
  - `'livré'` - Delivered
  - `'non livré'` - Not delivered

### Sales Table
**Migration:** `2024_08_09_073416_create_sales_table.php`

```sql
CREATE TABLE sales (
  id BIGINT PRIMARY KEY
  numeroFacture VARCHAR -- Links to factures.numero_facture
  product_id BIGINT FOREIGN KEY (products.id)
  store_id BIGINT FOREIGN KEY (stores.id)
  quantity INT
  prix FLOAT(20,2) -- Unit selling price
  prixTotal FLOAT(20,2) -- Total price (prix × quantity)
  interet FLOAT(20,2) -- Profit margin (selling price - purchase price) × quantity
  created_at, updated_at TIMESTAMP
)
```

**Purpose:** Line items for each facture - tracks individual product sales with margins.

### Orders Table (E-commerce)
**Migration:** `2026_04_18_170952_create_orders_table.php`

```sql
CREATE TABLE orders (
  id BIGINT PRIMARY KEY
  user_id BIGINT FOREIGN KEY (users.id) CUSTOMER
  delivery_address_id BIGINT FOREIGN KEY (delivery_addresses.id)
  total_amount DECIMAL(12,2)
  status VARCHAR DEFAULT 'pending'
    -- pending: awaiting approval
    -- approved: approved by manager → creates facture
    -- rejected: rejected by manager
    -- completed: delivered
  payment_method VARCHAR DEFAULT 'cod' (cod, orange_money)
  payment_status VARCHAR DEFAULT 'pending' (pending, paid, failed)
  transaction_id VARCHAR NULLABLE
  invoice_number VARCHAR NULLABLE -- Links to factures.numero_facture
  store_id BIGINT NULLABLE -- Added by manager during approval
  created_at, updated_at TIMESTAMP
)
```

### Payments Table
**Migration:** `2024_08_09_184119_create_payments_table.php`

```sql
CREATE TABLE payments (
  facture_id BIGINT FOREIGN KEY (factures.id)
  versement DECIMAL -- Amount paid in this transaction
  total_paye DECIMAL -- Cumulative total paid
  reste DECIMAL -- Remaining balance
  paid_by VARCHAR -- Payment method (cash, orange money, etc.)
  note TEXT NULLABLE
)
```

---

## 2. MODELS & RELATIONSHIPS

### Facture Model
**File:** [app/Models/Facture.php](app/Models/Facture.php)

```php
class Facture extends Model {
    protected $guarded = ['id'];
    
    // Relationships
    belongsTo(Store::class)
    belongsTo(User::class)
    belongsTo(Customer::class)
    hasMany(Payment::class) // All payments for this invoice
    belongsTo(Order::class, 'numero_facture', 'invoice_number') // Links e-commerce order
    
    // Computed attribute
    getCustomerNameAttribute() // Returns "CustomerName-Mark" or "Client inconnu"
}
```

### Sale Model
**File:** [app/Models/Sale.php](app/Models/Sale.php)

```php
class Sale extends Model {
    protected $fillable = [
        'numeroFacture', 'product_id', 'store_id',
        'quantity', 'prix', 'prixTotal', 'interet'
    ];
    
    // Relationships
    belongsTo(Product::class)
    belongsTo(Store::class)
    
    // Computed attributes
    getProduitAttribute() // Product name
    getProduitImageAttribute() // Product image
}
```

### Order Model
**File:** [app/Models/Order.php](app/Models/Order.php)

```php
class Order extends Model {
    protected $fillable = [
        'user_id', 'delivery_address_id', 'total_amount',
        'status', 'payment_method', 'payment_status',
        'transaction_id', 'invoice_number'
    ];
    
    // Relationships
    belongsTo(User::class) // E-commerce customer
    belongsTo(DeliveryAddress::class, 'delivery_address_id')
    hasMany(OrderItem::class)
    hasOne(Facture::class, 'numero_facture', 'invoice_number')
    hasMany(Sale::class, 'numeroFacture', 'invoice_number')
}
```

### User Model (Role-based Access)
**File:** [app/Models/User.php](app/Models/User.php)

```php
class User extends Authenticatable {
    // Role Constants
    const ROLE_ADMIN = 1           // Full back-office access
    const ROLE_MANAGER = 2         // Platform superuser - approves e-commerce orders
    const ROLE_STORE_MANAGER = 3   // Store-level access only
    const ROLE_CUSTOMER = 4        // E-commerce customer
    
    public function isStaff(): bool
        // Returns true for roles 1, 2, 3
    
    public function isCustomer(): bool
        // Returns true for role 4
}
```

---

## 3. ROUTING & CONTROLLERS

### Facture Routes
**File:** [routes/web.php](routes/web.php)

```php
Route::resource('factures', FactureController::class);
Route::resource('sales', SaleController::class);
Route::get('/sales/ajout/{numero_facture}/{avance}/{store_id}', [SaleController::class, 'ajout']);
Route::get('/sales/voir/{numero_facture}', [SaleController::class, 'voirSales']);
Route::resource('payments', PaymentController::class);
Route::get('/facture/payment/{id}', [PaymentController::class, 'creation']);
Route::get('/facture/voirPayment/{id}', [PaymentController::class, 'voir']);
```

### E-commerce Order Routes (Manager Only)
**File:** [routes/web.php](routes/web.php) - Protected by `middleware(['auth', 'superuser'])`

```php
Route::prefix('admin')->group(function () {
    Route::get('/orders', [OrderManagementController::class, 'index']);
        // → Pending orders awaiting manager approval
    
    Route::get('/orders/confirmed', [OrderManagementController::class, 'confirmed']);
        // → Approved orders (status = 'approved')
    
    Route::get('/orders/{order}', [OrderManagementController::class, 'show']);
    
    Route::post('/orders/{order}/approve', [OrderManagementController::class, 'approve']);
        // ★ MANAGER VALIDATION - Creates facture & sales
    
    Route::post('/orders/{order}/reject', [OrderManagementController::class, 'reject']);
        // ★ Rejects order, notifies customer
    
    Route::get('/orders/{order}/stock-check', [OrderManagementController::class, 'stockCheck']);
        // Verifies product availability
});
```

### Controllers

#### FactureController
**File:** [app/Http/Controllers/FactureController.php](app/Http/Controllers/FactureController.php)

**Methods:**
- `index()` - Lists all factures (with filters by number, customer, status, delivery)
  - For Store Managers (role 3): Shows only their store's factures
  - For Admins/Managers (roles 1,2): Shows all factures

- `show($facture)` - Displays invoice details with:
  - Sales line items
  - Customer info
  - All payments made
  - Links to manager user

- `store(Request $request)` - Creates new manual facture
  - Can create customer on-the-fly
  - Sets initial status to 'pending'
  - Redirects to sales.ajout to add line items

- `update(Facture $facture)` - Marks delivery
  - Sets `livraison = 'livré'`
  - Updates linked e-commerce order status to 'completed'

#### SaleController
**File:** [app/Http/Controllers/SaleController.php](app/Http/Controllers/SaleController.php)

**Methods:**
- `index()` - Lists all sales (searchable by invoice number, product, date)

- `ajout()` - Add products to a new facture
  - Called after facture creation: `/sales/ajout/{numero_facture}/{avance}/{store_id}`
  - Returns form to add products with quantity/price

- `store(Request $request)` - Saves sales line items (called via AJAX)
  - **Key Logic:**
    ```
    For each sale item:
      1. Decrement store_products.quantity
      2. Calculate profit margin: (selling_price - purchase_price) × quantity
      3. Create Sale record
      4. Update Store.balance with profit
    
    After all sales added:
      Calculate payment status:
      IF reste == 0 → statut='payé', livraison='livré'
      ELSEIF avance > 0 AND reste > 0 → statut='partiel', livraison='non livré'
      ELSE → statut='non payé', livraison='non livré'
      
      Create Facture record with calculated totals
      Create Payment record for advance
    ```

- `update()` - Modifies sale line item
  - Updates facture totals and payments accordingly

- `voirSales()` - View all sales for a specific facture

- `pos()` - Point of sale interface
  - POS system for direct selling

#### OrderManagementController
**File:** [app/Http/Controllers/Admin/OrderManagementController.php](app/Http/Controllers/Admin/OrderManagementController.php)

**Critical Methods:**

**`index()`** - List pending orders awaiting manager approval
- Query: `Order::where('status', 'pending')`
- View: `admin.orders.index`
- Access: Managers only (role 1,2)

**`approve(Order $order, Request $request)`** - MANAGER VALIDATION WORKFLOW
★ **This is the manager validation process:**

```
1. VERIFICATION
   - Check order status is still 'pending' (not already processed)
   - Require store_id selection
   - Verify order has items

2. STOCK CHECKING
   - For each OrderItem, verify Product.pcs (global stock) >= quantity
   - Decrement Product.pcs for all items
   - Throw exception if insufficient stock

3. CREATE SALES RECORDS
   - Generate invoice number: "INV-YYYYmmdd-00001"
   - For each OrderItem:
     • Find Purchase history for cost calculation
     • Calculate profit margin: (item_price - purchase_price) × qty
     • Create Sale record with numeroFacture = invoice_number
     • Add margin to Store.balance

4. CREATE CUSTOMER RECORD
   - Use firstOrCreate by email
   - Extract from Order.user data

5. CREATE FACTURE (Invoice)
   - numero_facture = invoice_number (auto-generated)
   - payment_status: 'paid' if Order.payment_status='paid' else 'non payé'
   - avance: full amount if paid, else 0
   - reste: 0 if paid, else full amount
   - livraison: 'non livré' initially

6. RECORD PAYMENT (if advance paid)
   - Create Payment record
   - Link to facture
   - Set versement = advance amount
   - paid_by = order.payment_method (orange_money or cash)

7. UPDATE ORDER
   - Set status = 'approved'
   - Set invoice_number = INV-YYYYmmdd-00001
   - Set store_id from request

8. NOTIFY CUSTOMER
   - Send OrderStatusChanged notification
   - Email: "Votre commande #X a été approuvée"
```

**`reject(Order $order)`** - Rejects order
- Sets status = 'rejected'
- Sends notification to customer
- No stock deduction, no facture created

**`confirmed(Request $request)`** - Lists approved orders
- Query: `Order::where('status', 'approved')`
- Shows completed sales with invoice links
- Filterable by order ID, customer name, payment status

**`stockCheck()`** - AJAX endpoint to verify stock availability
- Returns JSON: `{ all_ok: bool, items: [...] }`
- Checks Product.pcs against OrderItem quantities

---

## 4. VIEWS & USER INTERFACE

### Factures List
**File:** [resources/views/factures/index.blade.php](resources/views/factures/index.blade.php)

**Features:**
- **Filters:**
  - Invoice number (numero_facture)
  - Customer
  - Payment status (paid, unpaid, partial)
  - Delivery status
  - Creation date

- **Columns Displayed:**
  - Invoice number (links to details)
  - Customer info (name-mark)
  - Store info
  - Quantity
  - Total amount (GNF)
  - Advance paid (GNF)
  - Remaining balance (GNF)
  - Payment status badge (color-coded)
  - Delivery status
  - Notes
  - Date created

- **Actions:**
  - Eye icon → View invoice details
  - Calendar icon → Mark as delivered (if not already delivered)
  - Dropdown → View payments, delete invoice

**Color Coding:**
```
Status badge:
  'payé' → Green
  'partiel' → Yellow/Warning
  'non payé' → Red
```

### Invoice Details
**File:** [resources/views/factures/show.blade.php](resources/views/factures/show.blade.php)

Shows:
- Facture header info
- All sales line items with product details
- Customer information
- Payment history
- Notes

### Orders - Pending Approval (Admin Only)
**File:** [resources/views/admin/orders/index.blade.php](resources/views/admin/orders/index.blade.php)

**Access:** Only users with role_id = 1 or 2 (Admin/Manager)

**Displays:**
- #ID
- Customer name & phone
- Ordered articles with quantities
- Total amount (GNF)
- Payment method
- Status badge
- Date created

**Actions:**
- View details
- Approve (with store selection)
- Reject

### Orders - Confirmed (Approved Orders)
**File:** [resources/views/admin/orders/confirmed.blade.php](resources/views/admin/orders/confirmed.blade.php)

**Displays:**
- Command ID
- Customer name
- Invoice number (links to facture details)
- Articles ordered
- Total + profit margin
- Payment status
- Amount paid
- Remaining balance
- Delivery status
- Date of approval

---

## 5. VALIDATION & AUTHORIZATION WORKFLOW

### Authentication Middleware
**File:** [app/Http/Middleware/CheckIfAuthenticated.php](app/Http/Middleware/CheckIfAuthenticated.php)

```
Applied to: All admin routes with 'auth.check'

Check:
  1. Is user logged in?
  2. Is user a Staff member? (roles 1, 2, or 3)
  3. NOT a customer (role 4)?
  
If customer → Redirect to shop.home
If not staff → Redirect to homepage
If staff → Grant access
```

### Manager-Only Access
Routes with `middleware(['auth', 'superuser'])`:
- `/admin/orders` - Pending orders
- `/admin/orders/confirmed` - Approved orders
- `/admin/orders/{id}/approve` - Manager approval
- `/admin/orders/{id}/reject` - Manager rejection

### Store Manager Access Restriction
In controllers (e.g., FactureController):

```php
if (auth()->user()->role_id == 3) {
    // Store manager - show only their store's data
    $dataTable = $query->where('store_id', 
        Store::where('user_id', auth()->user()->id)->first()?->id
    )->get();
} else {
    // Admin/Manager - show all
    $dataTable = $query->get();
}
```

### Sales Validation (No Explicit "Manager Approval" Field)
**Current System:**
- ✗ No `validated_by` or `approved_by` column in factures or sales tables
- ✗ No `validated_at` timestamp
- ✗ Manager approval is implicit through Order.status='approved'

**Validation Flow:**
1. User creates facture → `statut='pending'` (payment status only)
2. User adds sales items → System calculates payment status
3. E-commerce: Manager must approve order → Creates facture automatically
4. Manual: No explicit approval step, facture can be created directly

---

## 6. PAYMENT & DELIVERY TRACKING

### Payment Tracking
- **Facture.statut** - Payment status (`non payé` | `partiel` | `payé`)
- **Facture.avance** - Down payment amount
- **Facture.reste** - Remaining balance
- **Payments table** - Records all payment transactions with:
  - `versement` - Amount paid
  - `total_paye` - Cumulative total
  - `reste` - Running balance
  - `paid_by` - Payment method
  - `note` - Payment details

### Delivery Tracking
- **Facture.livraison** - Delivery status (`non livré` | `livré`)
- Updated via FactureController.update()
- Also updates linked Order.status to 'completed'

---

## 7. ACCOUNTING & PROFIT TRACKING

### Profit Margin Calculation
In SaleController.store():

```php
foreach ($salesData as $data) {
    $lastPurchase = Purchase::where('product_id', $data['product_id'])
        ->first();
    $prix_achat = $lastPurchase ? $lastPurchase->price : 0;
    
    $data["interet"] = ($data['prix'] - $prix_achat) * $data['quantity'];
    // interet = profit margin per item
    
    Sale::create([...]);
    $store->balance += $data['interet'];
    // Add profit to store balance
}
```

- **interet** field = (selling_price - purchase_price) × quantity
- Accumulated in Store.balance for revenue tracking
- Displayed in confirmed orders view

---

## 8. E-COMMERCE INTEGRATION POINTS

### Order → Facture Flow

```
CUSTOMER: Places order (ecommerce/orders.store)
          ↓
ORDER CREATED: status='pending', payment_status varies
          ↓
MANAGER: Reviews in /admin/orders
          ↓
MANAGER: Clicks "Approve" with store selection
          ↓
SYSTEM: Performs approval workflow (see section 3)
          ↓
FACTURE CREATED: Linked via invoice_number
SALES CREATED: Line items created
CUSTOMER NOTIFIED: Email with approval status
          ↓
FACTURE visible in /factures list
SALES visible in /sales list
ORDER visible in /admin/orders/confirmed
```

### Notification System
**File:** [app/Notifications/OrderStatusChanged.php](app/Notifications/OrderStatusChanged.php)

- Triggered on approve/reject
- Email sent to customer
- Message varies by status:
  - Approved: "a été approuvée"
  - Rejected: "a été refusée"

---

## 9. SUMMARY TABLE

| Component | Current Status | Tracked By | Location |
|-----------|----|----|---|
| **Invoice Creation** | ✓ Implemented | Manual or E-commerce | Facture model |
| **Sales Line Items** | ✓ Implemented | Manual per-item | Sale model |
| **Payment Tracking** | ✓ Implemented | Status + Payment records | Facture.statut + Payments |
| **Delivery Tracking** | ✓ Implemented | Status field | Facture.livraison |
| **E-commerce Order Approval** | ✓ Implemented | Manager action | Order.status |
| **Manager Validation (Field)** | ✗ NOT IMPLEMENTED | No database field | - |
| **Manager Validation Audit** | ✗ NOT IMPLEMENTED | No who/when tracked | - |
| **Stock Deduction** | ✓ Implemented | On sale creation | Product.pcs, StoreProduct.quantity |
| **Profit Tracking** | ✓ Implemented | Margin calculation | Sale.interet, Store.balance |
| **Customer Notifications** | ✓ Implemented | Email | OrderStatusChanged |

---

## 10. NEXT STEPS FOR MANAGER VALIDATION FEATURE

To add explicit manager validation tracking:

1. **Add migration** to factures/sales tables:
   - `validated_by` (user_id of manager)
   - `validated_at` (timestamp)
   - `validation_status` (pending|approved|rejected)

2. **Update Facture model** with relationships:
   - `belongsTo(User::class, 'validated_by')`

3. **Update controllers** to record manager info on creation

4. **Add validation history** in views and audit trails

See the actual implementation requirements in the project for specifics.
