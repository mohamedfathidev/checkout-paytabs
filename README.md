<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<h2 align="center">💳 Laravel Checkout System with Manual Integration</h2>

<p align="center">
  A Laravel-based checkout and payment system built using <strong>PayTabs API</strong> to enable seamless, secure, and professional online payments with  order management and refund capabilities.
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
  <a href="https://php.net">
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP Version">
  </a>
</p>

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## ✨ Features

- 🛒 **Order Management**: Create and manage orders with detailed descriptions and amounts
- 📋 **Customer Information**: Collect comprehensive billing and shipping details
- 🔄 **Seamless Integration**: Pass customer data directly to PayTabs without re-entering
- 💳 **Secure Payments**: Pay securely using PayTabs Hosted Checkout Page
- ✅ **Automatic Callbacks**: Handle success and failure callbacks automatically
- ↩️ **Full Refund System**: Complete refund functionality for successful payments
- 🌐 **Local Development**: Full support for local development using ngrok
- 🔒 **Signed URLs**: Secure payment success/failure routes with Laravel signed URLs
- 📊 **Payment Tracking**: Comprehensive payment history and transaction tracking
- 🛡️ **Error Handling**: Robust error handling and validation throughout the system

## 🛠 Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL
- **Payment Gateway**: PayTabs API
- **Frontend**: Blade Templates with Tailwind CSS
- **Development**: Ngrok (for local callback URLs)
- **Package Management**: Composer
- **Version Control**: Git

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL Database
- PayTabs Account (for API credentials)
- Ngrok (for local development)

### 1. Clone the repository

```bash
git clone https://github.com/your-username/simple-checkout-using-paytabs.git
cd simple-checkout-using-paytabs
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
```

Edit your `.env` file and configure the following variables:

```env
APP_NAME="Laravel Checkout System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# PayTabs Configuration
paytabs_profile_id=your_paytabs_profile_id
paytabs_server_key=your_paytabs_server_key
paytabs_currency=EGP
paytabs_region=EGY

# Ngrok URL for local development
NGROK_URL=https://your-ngrok-url.ngrok.io
```

### 4. Generate Application Key and Run Migrations

```bash
php artisan key:generate
php artisan migrate
php artisan cache:clear
php artisan config:clear
```

## 🔧 Running the Project

### 1. Start Laravel Development Server

```bash
php artisan serve
```

Your application will be available at `http://localhost:8000`

### 2. Start Ngrok (for local development)

In a separate terminal window:

```bash
ngrok http 8000
```

Copy the ngrok URL (e.g., `https://abcd1234.ngrok.io`) and update your `.env` file:

```env
NGROK_URL=https://abcd1234.ngrok.io
```

### 3. Clear Cache After Ngrok URL Update

```bash
php artisan cache:clear
php artisan config:clear
```

## 💡 How It Works

### 1. **Order Creation**
   - Visit the homepage to create a new order
   - Enter order description and total amount
   - System creates order with pending status

### 2. **Checkout Process**
   - Fill in billing and shipping information
   - System validates customer details
   - Redirects to PayTabs hosted payment page

### 3. **Payment Processing**
   - Customer completes payment on PayTabs
   - PayTabs sends callback to your application
   - System updates order status and stores payment details

### 4. **Post-Payment**
   - Customer redirected to success/failure page
   - Payment details stored in database
   - Order status updated to 'paid' or 'failed'

### 5. **Refund Process**
   - Access refund functionality for paid orders
   - System processes refund through PayTabs API
   - Creates refund transaction record

---

## Test Cards

1. Status (Failed) E => 4012001036983332            & CVV : 530   
2. Status (Success) A => 4111111111111111           & CVV : 123    
---

## 📂 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── OrderController.php      # Order creation and management
│   │   ├── CheckoutController.php   # Checkout process handling
│   │   └── PaymentController.php    # Payment processing and callbacks
│   └── Middleware/
│       └── EnsureOrderNotPaid.php   # Prevent duplicate payments
├── Models/
│   ├── Order.php                    # Order model with relationships
│   └── Payment.php                  # Payment transaction model
├── Services/
│   ├── PaymentService.php           # PayTabs API integration
│   └── ValidationService.php        # Form validation logic
└── Providers/
    └── AppServiceProvider.php       # Application service provider

config/
└── paytabs.php                      # PayTabs configuration

database/
└── migrations/
    ├── create_orders_table.php      # Orders table structure
    └── create_payments_table.php    # Payments table structure

resources/
└── views/
    ├── order.blade.php              # Order creation form
    ├── checkout-details.blade.php   # Checkout form
    ├── payment-success.blade.php    # Success page
    ├── payment-failure.blade.php    # Failure page
    └── error.blade.php              # Error page

routes/
└── web.php                          # Application routes
```

## 🔌 API Integration

### PayTabs Configuration

The system integrates with PayTabs API for payment processing:

- **Profile ID**: Your PayTabs merchant profile
- **Server Key**: API authentication key
- **Currency**: EGP (Egyptian Pound) by default
- **Region**: EGY (Egypt) by default

### Supported Payment Features

- ✅ Sale transactions
- ✅ Refund transactions
- ✅ Callback handling
- ✅ Return URL processing
- ✅ Transaction verification

## 🛡️ Security Features

- **Signed URLs**: Payment success/failure routes are protected with Laravel signed URLs
- **Input Validation**: Comprehensive validation for all user inputs
- **SQL Injection Protection**: Uses Laravel's Eloquent ORM
- **XSS Protection**: Blade templating engine provides automatic escaping
- **CSRF Protection**: Built-in CSRF token protection
- **Secure Callbacks**: PayTabs callback verification

## 📊 Database Schema

### Orders Table
- `id` - Primary key
- `description` - Order description
- `total_amount` - Order total (decimal)
- `delivery_method` - Delivery method (default: shipping)
- `currency` - Currency code (default: EGP)
- `customer_details` - JSON customer information
- `status` - Order status (pending, paid, cancelled, failed)
- `created_at`, `updated_at` - Timestamps

### Payments Table
- `id` - Primary key
- `order_id` - Foreign key to orders
- `tran_ref` - PayTabs transaction reference
- `customer_name` - Customer name
- `tran_type` - Transaction type (sale, refund)
- `amount` - Payment amount
- `currency` - Payment currency
- `payment_method` - Payment method used
- `status` - Payment status
- `created_at`, `updated_at` - Timestamps

## 🚨 Error Handling

The system includes comprehensive error handling:

- **Order Creation Errors**: Validation and database errors
- **Payment Processing Errors**: API failures and invalid responses
- **Callback Errors**: Invalid callback data and processing failures
- **Refund Errors**: Already refunded orders and API failures
- **User-Friendly Messages**: Clear error messages for users

## 🔧 Customization

### Adding New Payment Methods

1. Update `PaymentService.php` to include new payment method logic
2. Modify the PayTabs API payload as needed
3. Update validation rules in `ValidationService.php`

### Modifying Order Flow

1. Edit `OrderController.php` for order creation logic
2. Update `CheckoutController.php` for checkout process
3. Modify Blade templates for UI changes

### Database Modifications

1. Create new migrations for schema changes
2. Update models to reflect new fields
3. Modify controllers to handle new data

## 📝 Environment Variables

| Variable | Description | Required | Default |
|----------|-------------|----------|---------|
| `paytabs_profile_id` | PayTabs merchant profile ID | Yes | - |
| `paytabs_server_key` | PayTabs API server key | Yes | - |
| `paytabs_currency` | Payment currency | No | EGP |
| `paytabs_region` | Payment region | No | EGY |
| `NGROK_URL` | Ngrok URL for local development | Yes (local) | - |
| `DB_DATABASE` | Database name | Yes | - |
| `DB_USERNAME` | Database username | Yes | - |
| `DB_PASSWORD` | Database password | Yes | - |

## 🧪 Testing

### Running Tests

```bash
php artisan test
```

### Manual Testing Flow

1. Create an order with valid data
2. Complete checkout with customer information
3. Test payment success scenario
4. Test payment failure scenario
5. Test refund functionality
6. Verify database records

## 📫 Contact & Support

- **Developer**: Mohamed Fathi
- **Email**: mohamedfathidev161@gmail.com
- **Phone**: +20 1020131424

## 📄 License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Notes

- **PayTabs Credentials**: Ensure you have valid PayTabs merchant account credentials
- **Ngrok Requirement**: PayTabs requires public callback URLs - use ngrok for local development
- **Database Setup**: Make sure your MySQL database is properly configured
- **Cache Clearing**: Run `php artisan cache:clear` after changing environment variables
- **Refund Limitations**: Refunds are only available for orders with successful payments
- **Currency Support**: Currently configured for EGP (Egyptian Pound) - modify for other currencies
- **Error Logs**: Check `storage/logs/paytabs-callback.log` for payment callback details

## 🔄 Changelog

### Version 1.0.0
- Initial release
- PayTabs integration
- Order management system
- Payment processing
- Refund functionality
- Local development support
