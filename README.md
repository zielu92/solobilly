![CodeRabbit Pull Request Reviews](https://img.shields.io/coderabbit/prs/github/zielu92/solobilly?utm_source=oss&utm_medium=github&utm_campaign=zielu92%2Fsolobilly&labelColor=171717&color=FF570A&link=https%3A%2F%2Fcoderabbit.ai&label=CodeRabbit+Reviews)
# Solobilly

**Solobilly** is an all-in-one management app for freelancers and small businesses. It simplifies tracking expenses, generating invoices, and organizing essential data for accounting.

---

## Features

- **Dashboard** – Overview with key statistics
- **Holiday Calendar** – View and manage holidays
- **Exchange Rates** – Display current exchange rates
- **Buyers List** – Manage clients and customer data
- **Invoice Generation** – Create and download invoices
- **Expense Tracking** – Categorized cost tracking
- **Payment Methods** – Support for multiple custom payment methods
- **Worklogs** – Track work hours and assignments
- **User Management** – Role-based permission system

---

## Installation

### Option 1: Docker (Recommended)

```bash
git clone https://github.com/zielu92/solobilly.git
cd solobilly
cp .env.example .env
./vendor/bin/sail build --no-cache
./vendor/bin/sail up
./vendor/bin/sail artisan migrate --seed
``` 
### Option 2: Manual setup (without Docker)
```bash
git clone https://github.com/zielu92/solobilly.git
cd solobilly
cp .env.example .env
# Edit .env to configure database and other settings
composer install
php artisan key:generate
php artisan migrate --seed
```
---
## Custom Payment Methods

Solobilly supports modular, pluggable payment methods via [Filament Modules](https://github.com/savannabits/filament-modules).

### Creating a Custom Payment Module

1. **Create the module**

   ```bash
   php artisan module:make MyPaymentMethod
   ```
2. **Create a handler class**
Extend `Modules/Payments/app/Payments/Payment.php` with your custom logic.
3. **Add configuration**
   Create the config file:
   `Modules/MyPaymentMethod/config/paymentmethods.php`
4. **Register the config in the service provider**
   Update Modules/MyPaymentMethod/Providers/MyPaymentMethodServiceProvider.php:
```php
$this->mergeConfigFrom(
    module_path($this->name, '/config/paymentmethods.php'),
    'payment_methods'
);
```
## Custom Invoice Templates

Invoices are composed of two parts:

- **Main Invoice Template**
- **Optional Payment Method Templates**

### How to Customize

- **Templates**  
  Place main invoice templates isn:  
  `resources/views/invoice/template/`  
  Use the included `Test` template as a reference.

- **CSS**  
  Store invoice styles in:  
  `public/css/invoice/`  
  ⚠️ Only use CSS 2.0 features or lower for compatibility with [laravel-dompdf](https://github.com/barryvdh/laravel-dompdf).

- **Payment Method Templates**  
  Add or reuse payment method templates in:  
  `Modules/Payments/resources/views/`

---

## License

This project is open-source and available under the [MIT License](https://opensource.org/licenses/MIT).
