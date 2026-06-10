
# Vendor + Reseller Panel — Full Implementation Plan

> Status: Planning only (kono code change kora hoy nai)
> Date: 2026-05-30
> Decision: Vendor = Multi-vendor marketplace, Reseller = Dropshipping/margin, Auth = notun alada guard

---

## 1. Boro chobi (architecture)

Ekhon 2 ta guard ache: `web` (admin/staff) ar `customer` (frontend buyer). Notun **2 ta guard** banabo:

| Guard | Table | Ke | Ki kore |
|---|---|---|---|
| `web` (ache) | users | Admin/Staff | Sob kichu control, vendor/reseller approve, payout |
| `customer` (ache) | customers | Buyer | Product kine |
| **`vendor` (notun)** | vendors | Marketplace seller | Nijer product list, order, stock, earning |
| **`reseller` (notun)** | resellers | Dropshipper | Catalog theke product niye nijer dame beche margin |

**Mul concept difference:**
- **Vendor** = nijer product, nijer stock, nijer malikana. Order ase -> vendor pack kore. Platform commission kate.
- **Reseller** = tomar (ba vendor-er) product-i beche. Stock nei. Reseller-price-e pay, nijer selling price boshay, **margin = selling - reseller price** sei lab. Delivery platform/courier kore.

---

## 2. Phase 0 — Foundation (dui panel-er common base)

### 2.1 Auth scaffolding
- `config/auth.php` e `vendor` ar `reseller` guard + provider add kora.
- 2 ta migration: `create_vendors_table`, `create_resellers_table`.
  - Common field: `name, email, phone, password, status (pending/active/suspended), image, address, remember_token, timestamps`.
  - Vendor extra: `shop_name, shop_slug, trade_license, commission_rate, payout details (bank/bkash), balance`.
  - Reseller extra: `business_name, default_margin_type (flat/percent), default_margin_value, balance, withdraw details`.
- `Vendor` ar `Reseller` model — `Authenticatable` extend korbe (User model-er moto).
- Middleware: `app/Http/Middleware/VendorAuth.php`, `ResellerAuth.php` (Customer.php-er pattern). Kernel e alias: `'vendor'`, `'reseller'`.

### 2.2 Route + controller structure
- `routes/web.php` e notun 2 ta group:
  - `prefix=vendor, namespace=Vendor, middleware=[vendor,...]`
  - `prefix=reseller, namespace=Reseller, middleware=[reseller,...]`
- Notun controller folder: `app/Http/Controllers/Vendor/` ar `app/Http/Controllers/Reseller/` (Auth + Dashboard + niche jeigula lagbe).

### 2.3 Panel UI base
- Notun layout: `resources/views/vendor/layouts/master.blade.php`, `resources/views/reseller/layouts/master.blade.php` (admin layout-er sidebar copy kore trim).

---

## 3. Vendor (Multi-vendor marketplace) — bistarito

### 3.1 Database
- `products` table e `vendor_id` (nullable — purono product = platform-er own) add.
- Order split-er jonno: `order_details` e protyek line-er `vendor_id` snapshot (migration: `add_vendor_id_to_order_details`). Karon ek order-e multiple vendor-er product thakte pare.
- `vendor_payouts` table: vendor-wise earning, commission cut, paid/pending status, payout request.

### 3.2 Vendor panel-er module
1. **Register/Login** -> register korle status `pending`, admin approve na korle login/sell korte parbe na.
2. **Dashboard** — nijer total order, today sales, pending payout, low stock.
3. **Product manage** — sudhu nijer product-er CRUD (admin ProductController logic reuse, `where vendor_id = auth` scope). Notun product e admin approval lagbe (default).
4. **Order** — sudhu nijer product-er order line, status update (packed/handover).
5. **Stock/inventory** — nijer product-er stock.
6. **Earning/Payout** — commission cut-er por earning, withdraw request.
7. **Profile/Shop settings**.

### 3.3 Admin side (web guard e notun)
- Vendor list, approve/suspend, commission rate set.
- Sob vendor-er payout request + paid mark.
- Order page e vendor-wise filter.

### 3.4 Frontend
- Product page e "Sold by {shop_name}" (optional).
- Order ase -> ek invoice, kintu backend e vendor-wise split kore protyek vendor-er panel-e jay.

---

## 4. Reseller (Dropshipping/margin) — bistarito

### 4.1 Database
- `products` (ba `product_variables`) e `reseller_price` field add (reseller-er kena dam).
- `reseller_products` (pivot, optional): kon reseller kon product nijer store-e niyeche + tar `selling_price`.
- `order_details` e: `reseller_id`, `reseller_price` (cost), `reseller_margin` (= sell - reseller_price) snapshot.
- `orders` e `reseller_id` (ei order kon reseller diyeche).
- `reseller_payouts` / `reseller_earnings`: order delivered hole margin reseller balance e, withdraw request.

### 4.2 Reseller flow (mul jinis)
1. Reseller login -> catalog dekhe (product + reseller_price).
2. Order place kore **customer-er address e** (manual order create, admin POS-er moto) — selling price reseller boshay.
3. Margin = selling - reseller_price -> order delivered hole reseller-er earning.
4. Delivery platform/courier kore (pathao/steadfast integration ache).
5. Reseller withdraw kore.

### 4.3 Reseller panel-er module
1. Register/Login (pending -> admin approve).
2. Dashboard — total order, pending/delivered, earning, balance.
3. **Catalog** — sob available product, reseller_price soho, search/filter.
4. **Order create** — admin OrderController-er order_create/POS logic-er reseller version (cart -> customer info -> selling price -> place).
5. **My orders** — status tracking.
6. **Earnings/Withdraw**.
7. Profile.

### 4.4 Admin side
- Reseller approve/suspend.
- reseller_price bulk set (product-wise ba global margin rule).
- Reseller order monitor + payout.

---

## 5. Common/shared kaj
- **Order splitting service**: ek order e vendor product + platform product mixed thakle backend e vendor-wise group. `OrderRoutingService` e rakha valo.
- **Payout/Ledger**: vendor ar reseller-er jonno transaction ledger (existing FinancialAccount/JournalEntry-er sathe link).
- **Notification**: notun order -> vendor/reseller notify (existing SMS gateway reuse).
- **Permission tightening**: admin web-guard e "vendor manage", "reseller manage" permission Spatie-te add.

---

## 6. Suparish kora execution order (phases)

1. **Phase 1** — Foundation: 2 guard, 2 table, middleware, login/register, blank dashboard.
2. **Phase 2** — Vendor panel: product scope, order split, dashboard.
3. **Phase 3** — Vendor admin side: approve, commission, payout.
4. **Phase 4** — Reseller panel: catalog, reseller_price, order create, margin calc.
5. **Phase 5** — Reseller earning/payout + admin monitor.
6. **Phase 6** — Frontend touch (sold-by), notification, permission, testing.

---

## 7. Risk / khyal rakhar bishoy
- **Order model boro & complex** (profit-loss snapshot, courier, marketing field). Vendor/reseller split-er somoy ei calc gula vangle problem. Order code age bhalo kore porte hobe.
- Stock sync: vendor product cancel/return hole stock thik korte hobe.
- reseller_price na set thakle margin 0/negative — validation lagbe.
- Multi-guard hole `Auth::guard()` sob jaygay explicit korte hobe, na hole session conflict.

---

## 8. Khola siddhanto (decide korte hobe)
- Vendor-er notun product e admin approval lagbe kina? (default: lagbe)
- Reseller-er alada storefront/subdomain lagbe kina? (default: lagbe na, sudhu order create)
- reseller_price product-level naki variable-level?
- Commission: vendor-wise alada naki global rate?
